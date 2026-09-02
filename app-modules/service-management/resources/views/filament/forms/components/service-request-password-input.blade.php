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
@php
    $statePath = $getStatePath();
    $hasStoredSecret = filled($getState());
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            displayValue: '',
            storedSecretId: $wire.$entangle('{{ $statePath }}'),
            isEditing: @js(! $hasStoredSecret),
            isDirty: false,
            isStoring: false,
            storagePromise: null,
            error: null,
            form: null,
            submitHandler: null,
            init() {
                this.form = this.$root.closest('form')
                this.submitHandler = async (event) => {
                    if (! this.isDirty && ! this.storagePromise) {
                        return
                    }

                    event.preventDefault()
                    event.stopImmediatePropagation()

                    const submitter = event.submitter

                    if (await this.store()) {
                        this.form?.requestSubmit(submitter ?? undefined)
                    }
                }

                this.form?.addEventListener('submit', this.submitHandler, true)
            },
            destroy() {
                this.form?.removeEventListener('submit', this.submitHandler, true)
            },
            edit() {
                this.displayValue = ''
                this.isDirty = false
                this.isEditing = true
                this.$nextTick(() => this.$refs.input?.focus())
            },
            remove() {
                this.displayValue = ''
                this.isDirty = true
                this.isEditing = true

                return this.store(true)
            },
            async store(shouldRemove = false) {
                if (this.storagePromise) {
                    return this.storagePromise
                }

                if (! this.isDirty) {
                    this.isEditing = ! this.storedSecretId

                    return true
                }

                if (! shouldRemove && this.displayValue === '') {
                    this.isDirty = false
                    this.isEditing = ! this.storedSecretId

                    return true
                }

                this.isStoring = true
                this.error = null

                this.storagePromise = (async () => {
                    try {
                        const response = await fetch(@js($getStoreUrl()), {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                value: shouldRemove ? null : this.displayValue,
                                previous_secret_id: this.storedSecretId,
                            }),
                        })

                        if (! response.ok) {
                            throw new Error('Unable to store password.')
                        }

                        const { id } = await response.json()

                        this.storedSecretId = id
                        this.displayValue = ''
                        this.isEditing = ! id
                        this.isDirty = false

                        return true
                    } catch {
                        this.error =
                            'The password could not be saved. Please try again.'

                        return false
                    } finally {
                        this.isStoring = false
                        this.storagePromise = null
                    }
                })()

                return this.storagePromise
            },
        }"
    >
        <div x-show="! isEditing && storedSecretId" class="flex items-center gap-3">
            <span class="text-gray-500">••••••••</span>

            <x-filament::link tag="button" type="button" size="sm" color="gray" x-on:click="edit()">
                Edit
            </x-filament::link>

            <x-filament::link tag="button" type="button" size="sm" color="danger" x-on:click="remove()">
                Remove
            </x-filament::link>
        </div>

        <x-filament::input.wrapper x-show="isEditing" :valid="! $errors->has($statePath)">
            <x-filament::input
                x-ref="input"
                :id="$getId()"
                type="password"
                autocomplete="new-password"
                x-model="displayValue"
                x-bind:disabled="isStoring"
                x-on:input="isDirty = true"
                x-on:blur="store()"
                x-on:keydown.enter.prevent="
                    if (await store()) {
                        $el.closest('form')?.requestSubmit()
                    }
                "
            />
        </x-filament::input.wrapper>

        <p x-show="error !== null" x-text="error" class="mt-2 text-sm text-danger-600" role="alert"></p>
    </div>
</x-dynamic-component>
