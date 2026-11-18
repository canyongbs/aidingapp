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
{{--
    Adapted from Filament's KeyValue embedded view, reusing its Alpine component. Each row's
    "key" is the stored option value and its "value" is the label, but the label
    column is rendered first and the key is generated from it in the browser.
--}}
@php
    $id = $getId();
    $reorderAction = $getAction("reorder");
    $canReorder = ! $isDisabled() && $isReorderable() && $reorderAction->isVisible() && ! $reorderAction->isDisabled();

    if ($canReorder) {
        // As in Filament's KeyValue: the hidden move buttons are the keyboard path once there is more than one row.
        $reorderAction->extraAttributes(
            [
                "x-bind:tabindex" => "rows.length > 1 ? -1 : 0",
                "x-bind:aria-hidden" => "rows.length > 1 ? 'true' : null",
            ],
            merge: true,
        );
    }
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" label-tag="div" class="fi-fo-key-value-wrp">
    <x-filament::input.wrapper :valid="! $errors->has($getStatePath())" class="fi-fo-key-value">
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc("key-value", "filament/forms") }}"
            x-id="['key-value-row']"
            x-data="keyValueFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                        reordering:
                            {{
                            \Illuminate\Support\Js::from(
                                $canReorder
                                    ? [
                                        "row" => __("filament-forms::components.key_value.reordering.row"),
                                        "position" => __("filament-forms::components.key_value.reordering.position"),
                                        "moved" => __("filament-forms::components.key_value.reordering.moved"),
                                    ]
                                    : [],
                            )
                        }},
                    })"
            x-init="
                updateRows = function () {
                    this.rows = Alpine.raw(this.state ?? []).map(({ key, value }) => ({
                        id: this.nextRowId++,
                        key,
                        value,
                    }))
                }
                getState = function () {
                    return this.rows.map(({ key, value }) => ({ key, value }))
                }
                {{-- The stock description and announcements label a row by its key, which here is the generated value. --}}
                rowDescription = ((base) =>
                    function (row, index, message) {
                        return base.call(this, { key: row.value }, index, message)
                    })(rowDescription)
            "
            wire:ignore
            wire:key="{{ $getLivewireKey() }}.{{ (int) $canReorder }}"
            {{ $getExtraAlpineAttributeBag()->class(["fi-fo-key-value-table-ctn"]) }}
        >
            <table
                aria-labelledby="{{ $id }}-label"
                id="{{ $id }}"
                class="fi-fo-key-value-table"
                x-data="{
                    slugify(label) {
                        const slug = (label ?? '')
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .toLowerCase()
                            .replace(/[^\p{L}\p{N}]+/gu, '-')
                            .replace(/^-+|-+$/g, '')

                        if (slug !== '' || (label ?? '').trim() === '') {
                            return slug
                        }

                        // Symbol-only labels have no slug; a hash keeps their key stable and non-empty so the row is saved.
                        let hash = 5381

                        for (const char of label) {
                            hash = ((hash * 33) ^ char.codePointAt(0)) >>> 0
                        }

                        return 'option-' + hash.toString(36)
                    },
                }"
            >
                <thead>
                    <tr>
                        @if ($canReorder)
                            <th scope="col" class="fi-has-action">
                                <span class="fi-sr-only">
                                    {{ __("filament-forms::components.key_value.columns.reorder.label") }}
                                </span>
                            </th>
                        @endif

                        <th scope="col">Label</th>

                        <th scope="col">Value</th>

                        <th scope="col" class="fi-has-action">
                            <span class="fi-sr-only">
                                {{ __("filament-forms::components.key_value.columns.actions.label") }}
                            </span>
                        </th>
                    </tr>
                </thead>

                <tbody
                    @if ($canReorder)
                        x-on:end.stop="reorderRows($event)"
                        x-sortable
                        data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                    @endif
                >
                    <template x-bind:key="row.id" x-for="(row, index) in rows">
                        <tr @if ($canReorder) x-bind:x-sortable-item="row.id" @endif>
                            @if ($canReorder)
                                <td class="fi-has-action">
                                    <span
                                        class="fi-sr-only"
                                        data-reorder-description
                                        x-bind:id="$id('key-value-row') + '-' + row.id"
                                        x-text="rowDescription(row, index)"
                                    ></span>
                                    <div x-sortable-handle class="fi-fo-key-value-table-row-sortable-handle">
                                        {{ $reorderAction }}
                                    </div>
                                    <div class="fi-sr-only">
                                        <button
                                            type="button"
                                            tabindex="-1"
                                            data-reorder-hidden
                                            data-reorder-direction="up"
                                            x-on:click.stop="moveRow(row.id, index - 1)"
                                            x-bind:disabled="index === 0"
                                            x-bind:aria-describedby="$id('key-value-row') + '-' + row.id"
                                        >
                                            {{ __("filament-forms::components.key_value.actions.move_up.label") }}
                                        </button>
                                        <button
                                            type="button"
                                            tabindex="-1"
                                            data-reorder-hidden
                                            data-reorder-direction="down"
                                            x-on:click.stop="moveRow(row.id, index + 1)"
                                            x-bind:disabled="index === rows.length - 1"
                                            x-bind:aria-describedby="$id('key-value-row') + '-' + row.id"
                                        >
                                            {{ __("filament-forms::components.key_value.actions.move_down.label") }}
                                        </button>
                                    </div>
                                </td>
                            @endif

                            <td>
                                <input
                                    aria-label="Label"
                                    type="text"
                                    x-model="row.value"
                                    x-on:input="
                                        row.key = slugify(row.value)
                                        updateState()
                                    "
                                    class="fi-input"
                                />
                            </td>

                            <td>
                                <input aria-label="Value" disabled type="text" x-model="row.key" class="fi-input" />
                            </td>

                            <td class="fi-has-action">
                                <div x-on:click="deleteRow(index)">
                                    {{ $getAction("delete") }}
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            @if ($canReorder)
                <span
                    x-ref="reorderStatus"
                    class="fi-sr-only"
                    role="status"
                    aria-live="polite"
                    aria-atomic="true"
                ></span>
            @endif

            <div x-on:click="addRow" class="fi-fo-key-value-add-action-ctn">
                {{ $getAction("add") }}
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
