<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeEmailTemplateFactory;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperServiceRequestTypeEmailTemplate
 */
class ServiceRequestTypeEmailTemplate extends Model implements Auditable, HasMedia, HasRichContent
{
    /** @use HasFactory<ServiceRequestTypeEmailTemplateFactory> */
    use HasFactory;

    use HasUuids;
    use AuditableTrait;
    use InteractsWithMedia;
    use InteractsWithRichContent;

    protected $fillable = [
        'service_request_type_id',
        'type',
        'subject',
        'body',
        'role',
    ];

    protected $casts = [
        'subject' => 'array',
        'body' => 'array',
        'type' => ServiceRequestEmailTemplateType::class,
        'role' => ServiceRequestTypeEmailTemplateRole::class,
    ];

    /**
     * @return BelongsTo<ServiceRequestType, $this>
     */
    public function serviceRequestType(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestType::class);
    }

    /**
     * @param string|array<int, string|array<string, mixed>> $content
     * @param array<string, mixed> $mergeData
     * @param array<string, mixed> $customBlockData
     */
    public function getBody(string|array $content, array $mergeData, array $customBlockData = []): HtmlString
    {
        $this->body = $content;

        $html = $this->getRichContentAttribute('body')
            ?->customBlocks([
                ServiceRequestTypeEmailTemplateButtonBlock::class => $customBlockData,
                SurveyResponseEmailTemplateTakeSurveyButtonBlock::class => $customBlockData,
            ])
            ->mergeTags($mergeData)
            ->toHtml() ?? '';

        // Convert CSS variable-based styles to inline styles for email client compatibility.
        // Text colors: style="--color: #hex; --dark-color: #hex" → style="color: #hex"
        $html = preg_replace(
            '/style="--color:\s*([^;]+);\s*--dark-color:\s*[^"]*"/',
            'style="color: $1"',
            $html,
        );

        // Grid layout: style="--cols: repeat(X, ...)" → style="display: table; width: 100%"
        $html = preg_replace(
            '/style="--cols:\s*[^"]*"/',
            'style="display: table; width: 100%; table-layout: fixed;"',
            $html,
        );

        // Grid columns: style="--col-span: span X / span X" → style="display: table-cell; vertical-align: top; padding: 0 8px"
        $html = preg_replace(
            '/style="--col-span:\s*[^"]*"/',
            'style="display: table-cell; vertical-align: top; padding: 0 8px;"',
            $html,
        );

        return new HtmlString($html);
    }

    /**
     * @param string|array<int, string|array<string, mixed>> $content
     * @param array<string, mixed> $mergeData
     */
    public function getSubject(string|array $content, array $mergeData): HtmlString
    {
        $this->subject = $content;

        $text = $this->getRichContentAttribute('subject')
            ?->mergeTags($mergeData)
            ->toText() ?? '';

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        $text = Str::limit($text, 988, '');

        return new HtmlString($text);
    }

    /**
     * @return array<string, string>
     */
    public static function getMergeTags(): array
    {
        return [
            'contact name' => '{{ contact name }}',
            'service request number' => '{{ service request number }}',
            'created date' => '{{ created date }}',
            'updated date' => '{{ updated date }}',
            'status' => '{{ status }}',
            'assigned staff name' => '{{ assigned staff name }}',
            'title' => '{{ title }}',
            'description' => '{{ description }}',
            'type' => '{{ type }}',
            'recent update' => '{{ recent update }}',
        ];
    }

    public function setUpRichContent(): void
    {
        $mergeTags = static::getMergeTags();

        $this->registerRichContent('subject')
            ->mergeTags($mergeTags);

        $this->registerRichContent('body')
            ->fileAttachmentsDisk('s3-public')
            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make())
            ->mergeTags($mergeTags)
            ->customBlocks([
                ServiceRequestTypeEmailTemplateButtonBlock::class,
                SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
            ]);
    }
}
