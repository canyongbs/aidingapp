{{--
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
--}}
<x-filament-widgets::widget>
    <div class="project-work-pipeline-widget">
        {{ $this->table }}
    </div>

    @assets
        {{-- Allow the milestone group heading block to fill the row and keep the title and progress vertically aligned. --}}
        <style>
            .project-work-pipeline-widget .fi-ta-group-header > div {
                display: flex;
                flex: 1 1 auto;
                align-items: center;
            }

            .project-work-pipeline-widget .fi-ta-group-header > div > .fi-ta-group-description {
                flex: 1 1 auto;
            }

            /* Move the collapse toggle before the milestone title/progress content. */
            .project-work-pipeline-widget .fi-ta-group-header > .fi-icon-btn {
                order: -1;
            }

            /*
                The milestone title is re-rendered (optionally as a clickable edit control)
                inside the group description, so hide the duplicate plain-text heading
                whenever a description is present. The "No Associated Milestone" heading
                has no description, so it stays visible.
            */
            .project-work-pipeline-widget .fi-ta-group-heading:has(+ .fi-ta-group-description) {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border-width: 0;
            }
        </style>
    @endassets

    <x-filament-actions::modals />
</x-filament-widgets::widget>
