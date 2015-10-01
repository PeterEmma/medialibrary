<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */

    'disk'      => 'media',

    'generator' => [

        'url' => CipeMotion\Medialibrary\Generators\AzureUrlGenerator::class

    ],

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    'relations' => [

        'owner'      => 'App\User',
        'user'       => null,

        'attachment' => [

//            'post' => 'App\Models\Post',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Files types
    |--------------------------------------------------------------------------
    |
    | {name} => [
    |
    |       'mimes'           => [
    |
    |           {extension} => {mime type}
    |
    |       ],
    |
    |       'transformations' => [
    |
    |           {name} => [
    |
    |               'transformer' => ITransformer::class,
    |               'queued'      => {(bool|string) boolean to indicate queue or not, string if on a custom queue}
    |               'config'      => {(array) an array with configuration}
    |
    |           ]
    |
    |       ],
    |
    |       'max_file_size'   => {(int) the max filesize in bytes, when exceeded a \Exception is thrown}
    | ]
    |
    */

    'file_types' => [

        'image'    => [

            'mimes'           => [

                'gif'  => 'image/gif',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg'

            ],

            'transformations' => [

                'thumb' => [

                    'transformer' => CipeMotion\Medialibrary\Transformers\ResizeTransformer::class,

                    'queued'      => false,

                    'config'      => [

                        'size' => [

                            'w' => 280,
                            'h' => 280

                        ]

                    ]

                ]

            ],

            'max_file_size'   => 10 * 1024 * 1024

        ],

        'video'    => [

            'mimes'           => [

                'avi'   => [

                    'video/avi',
                    'video/msvideo',
                    'video/x-msvideo'

                ],
                'mpeg'  => 'video/mpeg',
                'mpeg4' => 'video/mp4v-es',
                'mp4'   => [

                    'video/mp4v-es',
                    'video/mp4'

                ],
                'mov'   => 'video/quicktime',
                'wmv'   => 'video/x-ms-wmv',
                'flv'   => 'video/x-flv',
                '3gpp'  => 'video/3gpp',
                'webm'  => 'video/webm',
                'ogg'   => [

                    'video/ogg',
                    'application/ogg'

                ],
                'ogv'   => 'video/ogg'

            ],

            'transformations' => [],

            'max_file_size'   => 500 * 1024 * 1024

        ],

        'document' => [

            'mimes'           => [

                'pdf'  => [

                    'application/pdf',
                    'application/x-pdf',
                    'application/acrobat',
                    'applications/vnd.pdf',
                    'text/pdf',
                    'text/x-pdf',
                    'application/download',
                    'application/x-download',
                    'application/save-as'

                ],
                'doc'  => 'application/msword',
                'dot'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'txt'  => 'text/plain',
                'pot'  => [

                    'application/mspowerpoint',
                    'application/vnd.ms-powerpoint'

                ],
                'ppa'  => [

                    'application/mspowerpoint',
                    'application/vnd.ms-powerpoint'

                ],
                'pps'  => [

                    'application/mspowerpoint',
                    'application/vnd.ms-powerpoint'

                ],
                'pws'  => 'application/vnd.ms-powerpoint',
                'ppt'  => [

                    'application/mspowerpoint',
                    'application/powerpoint',
                    'application/vnd.ms-powerpoint',
                    'application/x-mspowerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                ],
                'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'xls'  => 'application/vnd.ms-excel',
                'xlt'  => 'application/vnd.ms-excel',
                'xla'  => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ],

            'transformations' => [],

            'max_file_size'   => 10 * 1024 * 1024

        ],

        'audio'   => [

            'mimes'           => [

                'mp3' => [

                    'audio/mpeg3',
                    'audio/x-mpeg-3'

                ],
                'wav' => [

                    'audio/wav',
                    'audio/x-wav'

                ],
                'ogg' => 'application/ogg',
                'm4a' => [

                    'audio/mp4',
                    'audio/x-m4a'

                ]

            ],

            'transformations' => [],

            'max_file_size'   => 50 * 1024 * 1024

        ]

    ]

];