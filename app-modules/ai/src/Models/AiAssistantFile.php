<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiAssistantFileFactory;
use AidingApp\Ai\Models\Contracts\AiFile;
use AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperAiAssistantFile
 */
class AiAssistantFile extends Model implements AiFile, HasMedia
{
    /** @use HasFactory<AiAssistantFileFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'file_id',
        'assistant_id',
        'mime_type',
        'name',
        'temporary_url',
        'parsing_results',
    ];

    /**
     * @return BelongsTo<AiAssistant, $this>
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->singleFile();
    }

    public function getKey(): string
    {
        return parent::getKey();
    }

    public function getTemporaryUrl(): ?string
    {
        return $this->temporary_url;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function getFileId(): ?string
    {
        return $this->file_id;
    }

    public function getParsingResults(): ?string
    {
        return $this->parsing_results;
    }

    /**
     * @return MorphOne<OpenAiVectorStore, $this>
     */
    public function openAiVectorStore(): MorphOne
    {
        return $this->morphOne(OpenAiVectorStore::class, 'file');
    }
}
