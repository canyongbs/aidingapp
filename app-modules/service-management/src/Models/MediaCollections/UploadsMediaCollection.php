<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
