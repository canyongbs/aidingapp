<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

return [
    'label' => 'Importar',

    'modal' => [
        'heading' => 'Importar :label',

        'form' => [
            'file' => [
                'label' => 'Archivo',

                'placeholder' => 'Cargar un archivo CSV',

                'rules' => [
                    'duplicate_columns' => '{0} El archivo no debe contener más de un encabezado de columna vacío.|{1,*} El archivo no debe contener encabezados de columna duplicados: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Columnas',
                'placeholder' => 'Seleccionar una columna',
            ],
        ],

        'actions' => [
            'download_example' => [
                'label' => 'Descargar archivo CSV de ejemplo',
            ],

            'import' => [
                'label' => 'Importar',
            ],
        ],
    ],

    'notifications' => [
        'completed' => [
            'title' => 'Importación completada',

            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => 'Descargar información de la fila fallida|Descargar información de las filas fallidas',
                ],
            ],
        ],

        'max_rows' => [
            'title' => 'El archivo CSV cargado es demasiado grande',
            'body' => 'No se puede importar más de una fila a la vez.|No se pueden importar más de :count filas a la vez.',
        ],

        'started' => [
            'title' => 'Importación iniciada',
            'body' => 'Su importación ha comenzado y se procesará 1 fila en segundo plano.|Su importación ha comenzado y se procesarán :count filas en segundo plano.',
        ],
    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Error del sistema, póngase en contacto con el servicio de asistencia.',
        'column_mapping_required_for_new_record' => 'La columna :attribute no se asignó a una columna del archivo, pero esto es necesario para crear nuevos registros.',
    ],
];
