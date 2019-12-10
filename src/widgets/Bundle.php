<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use concepture\yii2logic\bundles\Bundle as BaseBundle;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Bundle extends BaseBundle
{
    public $js = [
        'script/jquery.ui.widget.js',
        'script/jquery.fileupload.js',
        'script/uploader.js',
    ];

    public $publishOptions = [
        'forceCopy'=> YII_DEBUG  ? true : false,
        'except' => [
            'server/*',
            'test'
        ],
    ];

    public $depends = [
        'kamaelkz\yii2cdnuploader\widgets\CdnHelperBundle'
    ];
}
