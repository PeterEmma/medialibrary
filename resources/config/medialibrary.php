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

        'owner'      => 'App\Models\Tenant',
        'user'       => 'App\Models\User', // or null

        'attachment' => [

            //'post' => 'App\Models\Post',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Files types
    |--------------------------------------------------------------------------
    |
    | Supported: "images", "videos", "docs", "audio"
    |
    | {name} => [
    |       'mimes' => [
    |           {extention} => {mime type}
    |       ],
    |       'transformations' => [
    |           {name} => [
    |               'sizes' => [
    |                   {width}x{height} => [
    |                       'width'  => {(int) pixels},
    |                       'height' => {(int) pixels},
    |                       'queue'  => {(boolean,string|optional|default:true,uses (if set) transformation.{name}.queue)
    |                                    false when size must not be queued and created directly or special queue name}
    |                   ]
    |               ],
    |               'queue' => {(string|optional|default:medialibrary_{files.{name}}) queue name}
    |           ]
    |
    |       ],
    |
    |       'max_file_size' => {(int) the max filesize in bytes, when exceeded a \Exception is thrown}
    | ]
    */

    'files_types' => [

        'image'    => [

            'mimes'           => [

                'gif'  => 'image/gif',
                'jpeg' => 'image/jpeg',
                'jpg'  => 'image/jpeg',
                'ico'  => 'image/x-icon',
                'png'  => 'image/png'

            ],

            'transformations' => [

                'thumbs' => [

                    'sizes' => [

                        '200x200' => [

                            'width'  => 200,
                            'height' => 200,
                            'queue'  => false

                        ],

                        '280x240' => [

                            'width'  => 280,
                            'height' => 240

                        ]

                    ]

                ],

                'retina' => [

                    'sizes' => [

                        '900x900' => [

                            'width'  => 900,
                            'height' => 900

                        ],

                        '980x940' => [

                            'width'  => 980,
                            'height' => 940

                        ]

                    ],

                    'queue' => 'medialibrary_retina'

                ]

            ],

            'max_file_size'   => 10 * 1024 * 1024

        ],

        'video'    => [

            'mimes'         => [

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

            'convert_to'    => [

                'mp4' => 'video/mp4'
                //'ogg' => 'video/ogg',
                //'webm' => 'video/webm'

            ],

            'max_file_size' => 500 * 1024 * 1024

        ],

        'document' => [

            'mimes'         => [

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

            'max_file_size' => 10 * 1024 * 1024

        ],

        'audio'   => [

            'mimes'         => [

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

            'convert_to'    => [
                'mp3' => 'audio/mpeg3'
                //'ogg' => 'application/ogg',
            ],

            'max_file_size' => 50 * 1024 * 1024

        ]

    ]

];
