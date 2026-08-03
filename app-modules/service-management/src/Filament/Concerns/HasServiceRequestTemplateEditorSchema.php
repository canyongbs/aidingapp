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

namespace AidingApp\ServiceManagement\Filament\Concerns;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Support\RichContentDocument;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Schemas\Components\Utilities\Get;

trait HasServiceRequestTemplateEditorSchema
{
    /**
     * @return array<int, RichEditor>
     */
    protected function getServiceRequestTemplateEditorSchema(
        ServiceRequestEmailTemplateType $type,
        string $subjectLabel = 'Subject',
        string $subjectPlaceholder = 'Enter the email subject here...',
        ?string $subjectHelperText = null,
        string $bodyLabel = 'Body',
        string $bodyPlaceholder = 'Enter the email body here...',
    ): array {
        $mergeTags = ServiceRequestTypeEmailTemplate::getMergeTags();

        if ($type !== ServiceRequestEmailTemplateType::Update) {
            unset($mergeTags['recent update']);
        }

        $hasAnyContent = function (Get $get): bool {
            return RichContentDocument::hasContent($get('subject'))
                || RichContentDocument::hasContent($get('body'));
        };

        return [
            RichEditor::make('subject')
                ->label($subjectLabel)
                ->placeholder($subjectPlaceholder)
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->toolbarButtons([])
                ->mergeTags($mergeTags)
                ->helperText($subjectHelperText)
                ->required(fn (Get $get): bool => $hasAnyContent($get))
                ->live(onBlur: true)
                ->json(),
            RichEditor::make('body')
                ->label($bodyLabel)
                ->placeholder($bodyPlaceholder)
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                    ['textColor', 'small'],
                    ['mergeTags', 'customBlocks'],
                    ['clearFormatting'],
                    ['undo', 'redo'],
                ])
                ->mergeTags($mergeTags)
                ->customBlocks([
                    ServiceRequestTypeEmailTemplateButtonBlock::class,
                    SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
                ])
                ->columnSpanFull()
                ->required(fn (Get $get): bool => $hasAnyContent($get))
                ->live(onBlur: true)
                ->json(),
        ];
    }
}
