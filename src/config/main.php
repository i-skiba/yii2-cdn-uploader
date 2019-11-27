<?php

return [
    'bootstrap' => [
        'log',
        'kamaelkz\yii2cdnuploader\Yii2CdnUploader',
    ],
    'modules' => [
        'cdn' => [
            'class' => 'kamaelkz\yii2cdnuploader\modules\uploader\Module'
        ],
    ],
    'components' => [
        'cdnService' => [
            'class' => 'kamaelkz\yii2cdnuploader\services\CdnService',
            'projectName' => getenv('CDN_PROJECTNAME')
        ],
    ],
    'params' => require __DIR__ . '/params.php'
];