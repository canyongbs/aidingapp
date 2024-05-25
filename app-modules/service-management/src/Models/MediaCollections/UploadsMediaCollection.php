<?php

namespace AidingApp\ServiceManagement\Models\MediaCollections;

use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

class UploadsMediaCollection extends MediaCollection
{
    protected ?int $maxNumberOfFiles;

    protected ?int $maxFileSizeInMB;

    /**
     * @var array<string, array<string>>
     */
    protected ?array $mimes = [];

    /**
     * @var array<string, callable>
     */
    private array $accepts = [];

    public function __construct(string $name = 'uploads')
    {
        parent::__construct($name);
    }

    public static function create($name = 'uploads'): static
    {
        return new self($name);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function maxNumberOfFiles(int $maxNumberOfFiles): static
    {
        $this->maxNumberOfFiles = $maxNumberOfFiles;
        $this->onlyKeepLatest($maxNumberOfFiles);

        return $this;
    }

    public function getMaxNumberOfFiles(): ?int
    {
        return $this->maxNumberOfFiles;
    }

    public function maxFileSizeInMB(int $maxFileSizeInMB): static
    {
        $this->maxFileSizeInMB = $maxFileSizeInMB;

        $this->accepts['size'] = fn (File $file) => $file->size <= $maxFileSizeInMB * 1000 * 1000;

        $this->acceptsFile(
            fn (File $file) => collect($this->accepts)
                ->reduce(fn (bool $carry, callable $rule) => $carry && $rule($file), true)
        );

        return $this;
    }

    public function getMaxFileSizeInMB(): ?int
    {
        return $this->maxFileSizeInMB;
    }

    /**
     * @param $mimes array<string, array<string>>
     */
    public function mimes(array $mimes): static
    {
        $this->mimes = $mimes;
        $this->acceptsMimeTypes(collect($mimes)->keys()->toArray());

        return $this;
    }

    public function getMimes(): array
    {
        return collect($this->mimes)
            ->keys()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getExtensions(): array
    {
        return collect($this->mimes)
            ->flatten()
            ->unique()
            ->sort()
            ->map(
                fn (string $string): string => str($string)
                    ->prepend('.')
                    ->toString()
            )
            ->values()
            ->toArray();
    }
}
