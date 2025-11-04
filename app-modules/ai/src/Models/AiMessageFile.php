<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiMessageFileFactory;
use AidingApp\Ai\Models\Contracts\AiFile;
use AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AiMessageFile extends Model implements AiFile, HasMedia
{
    /** @use HasFactory<AiMessageFileFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use InteractsWithMedia;
    use Prunable;

    protected $fillable = [
        'file_id',
        'message_id',
        'mime_type',
        'name',
        'temporary_url',
        'parsing_results',
    ];

    /**
     * @return BelongsTo<AiMessage, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(AiMessage::class, 'message_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->singleFile();
    }

    /**
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', now()->subDays(7));
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
