<?php

return [
    'yii2-cdn-uploader' => [
        'proxy' => [
            'static' => getenv('CDN_STATIC_HOST'),
            'web' => getenv('CDN_WEB_HOST'),
        ],
        'api' => [
            'host' => getenv('CDN_HOST'),
            'routes' => [
                'upload' => '/api/v1/upload', #POST
                'delete' => '/api/v1/delete', #DELETE
                'info' => '/api/v1/info', #GET
                'list' => '/api/v1/list', #GET
                'crop' => '/api/v1/crop', #PUT
            ]
        ],
        'jwt_settings' => [
            'secret' => getenv('CDN_SECRET'),
            'expire' => 1200,
            'algo' => 'HS256',
        ]
    ]
];