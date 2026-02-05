<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use App\Features\ReorderableFormFieldOptionsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('service_request_form_fields')
                ->where('type', 'radio')
                ->chunkById(100, function (Collection $fields) {
                    foreach ($fields as $field) {
                        /** @var stdClass $field */
                        $config = json_decode($field->config, true);

                        if (! is_array($config) || ! isset($config['options']) || ! is_array($config['options'])) {
                            return;
                        }

                        $options = $config['options'];

                        if (empty($options)) {
                            return;
                        }

                        $firstKey = array_key_first($options);
                        $firstOption = $options[$firstKey];

                        if (is_array($firstOption) && isset($firstOption['value']) && isset($firstOption['label'])) {
                            return;
                        }

                        $newOptions = [];

                        foreach ($options as $value => $label) {
                            $newOptions[] = [
                                'value' => (string) $value,
                                'label' => $label,
                            ];
                        }

                        $config['options'] = $newOptions;

                        DB::table('service_request_form_fields')
                            ->where('id', $field->id)
                            ->update(['config' => json_encode($config)]);
                    }
                });

            DB::table('service_request_forms')
                ->whereNotNull('content')
                ->chunkById(100, function (Collection $forms) {
                    foreach ($forms as $form) {
                        /** @var stdClass $form */
                        $content = json_decode($form->content, true);

                        if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                            return;
                        }

                        $modified = false;

                        $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                            foreach ($blocks as &$block) {
                                if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && $block['attrs']['type'] === 'radio') {
                                    if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                        $options = $block['attrs']['data']['options'];

                                        if (! empty($options)) {
                                            $firstKey = array_key_first($options);
                                            $firstOption = $options[$firstKey];

                                            if (! (is_array($firstOption) && isset($firstOption['value']) && isset($firstOption['label']))) {
                                                $newOptions = [];

                                                foreach ($options as $value => $label) {
                                                    $newOptions[] = [
                                                        'value' => (string) $value,
                                                        'label' => $label,
                                                    ];
                                                }
                                                $block['attrs']['data']['options'] = $newOptions;
                                                $modified = true;
                                            }
                                        }
                                    }
                                }

                                if (isset($block['content']) && is_array($block['content'])) {
                                    $traverse($block['content']);
                                }
                            }
                        };

                        $traverse($content['content']);

                        if ($modified) {
                            DB::table('service_request_forms')
                                ->where('id', $form->id)
                                ->update(['content' => json_encode($content)]);
                        }
                    }
                });

            DB::table('service_request_form_steps')
                ->whereNotNull('content')
                ->chunkById(100, function (Collection $forms) {
                    foreach ($forms as $form) {
                        /** @var stdClass $form */
                        $content = json_decode($form->content, true);

                        if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                            return;
                        }

                        $modified = false;

                        $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                            foreach ($blocks as &$block) {
                                if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && $block['attrs']['type'] === 'radio') {
                                    if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                        $options = $block['attrs']['data']['options'];

                                        if (! empty($options)) {
                                            $firstKey = array_key_first($options);
                                            $firstOption = $options[$firstKey];

                                            if (! (is_array($firstOption) && isset($firstOption['value']) && isset($firstOption['label']))) {
                                                $newOptions = [];

                                                foreach ($options as $value => $label) {
                                                    $newOptions[] = [
                                                        'value' => (string) $value,
                                                        'label' => $label,
                                                    ];
                                                }
                                                $block['attrs']['data']['options'] = $newOptions;
                                                $modified = true;
                                            }
                                        }
                                    }
                                }

                                if (isset($block['content']) && is_array($block['content'])) {
                                    $traverse($block['content']);
                                }
                            }
                        };

                        $traverse($content['content']);

                        if ($modified) {
                            DB::table('service_request_form_steps')
                                ->where('id', $form->id)
                                ->update(['content' => json_encode($content)]);
                        }
                    }
                });

            ReorderableFormFieldOptionsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ReorderableFormFieldOptionsFeature::deactivate();

            DB::table('service_request_form_steps')
                ->whereNotNull('content')
                ->chunkById(100, function (Collection $forms) {
                    foreach ($forms as $form) {
                        /** @var stdClass $form */
                        $content = json_decode($form->content, true);

                        if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                            return;
                        }

                        $modified = false;

                        $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                            foreach ($blocks as &$block) {
                                if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && $block['attrs']['type'] === 'radio') {
                                    if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                        $options = $block['attrs']['data']['options'];

                                        if (! empty($options) && isset($options[0]) && is_array($options[0]) && isset($options[0]['value'])) {
                                            $oldOptions = [];

                                            foreach ($options as $option) {
                                                if (isset($option['value']) && isset($option['label'])) {
                                                    $oldOptions[$option['value']] = $option['label'];
                                                }
                                            }
                                            $block['attrs']['data']['options'] = $oldOptions;
                                            $modified = true;
                                        }
                                    }
                                }

                                if (isset($block['content']) && is_array($block['content'])) {
                                    $traverse($block['content']);
                                }
                            }
                        };

                        $traverse($content['content']);

                        if ($modified) {
                            DB::table('service_request_form_steps')
                                ->where('id', $form->id)
                                ->update(['content' => json_encode($content)]);
                        }
                    }
                });

            DB::table('service_request_forms')
                ->whereNotNull('content')
                ->chunkById(100, function (Collection $forms) {
                    foreach ($forms as $form) {
                        /** @var stdClass $form */
                        $content = json_decode($form->content, true);

                        if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                            return;
                        }

                        $modified = false;

                        $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                            foreach ($blocks as &$block) {
                                if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && $block['attrs']['type'] === 'radio') {
                                    if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                        $options = $block['attrs']['data']['options'];

                                        if (! empty($options) && isset($options[0]) && is_array($options[0]) && isset($options[0]['value'])) {
                                            $oldOptions = [];

                                            foreach ($options as $option) {
                                                if (isset($option['value']) && isset($option['label'])) {
                                                    $oldOptions[$option['value']] = $option['label'];
                                                }
                                            }
                                            $block['attrs']['data']['options'] = $oldOptions;
                                            $modified = true;
                                        }
                                    }
                                }

                                if (isset($block['content']) && is_array($block['content'])) {
                                    $traverse($block['content']);
                                }
                            }
                        };

                        $traverse($content['content']);

                        if ($modified) {
                            DB::table('service_request_forms')
                                ->where('id', $form->id)
                                ->update(['content' => json_encode($content)]);
                        }
                    }
                });

            DB::table('service_request_form_fields')
                ->where('type', 'radio')
                ->chunkById(100, function (Collection $fields) {
                    foreach ($fields as $field) {
                        /** @var stdClass $field */
                        $config = json_decode($field->config, true);

                        if (! is_array($config) || ! isset($config['options']) || ! is_array($config['options'])) {
                            return;
                        }

                        $options = $config['options'];

                        if (empty($options) || ! isset($options[0]) || ! is_array($options[0]) || ! isset($options[0]['value'])) {
                            return;
                        }

                        $oldOptions = [];

                        foreach ($options as $option) {
                            if (isset($option['value']) && isset($option['label'])) {
                                $oldOptions[$option['value']] = $option['label'];
                            }
                        }

                        $config['options'] = $oldOptions;

                        DB::table('service_request_form_fields')
                            ->where('id', $field->id)
                            ->update(['config' => json_encode($config)]);
                    }
                });
        });
    }
};
