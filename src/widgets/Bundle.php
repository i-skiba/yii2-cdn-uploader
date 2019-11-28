<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use concepture\yii2logic\bundles\Bundle as BaseBundle;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Bundle extends BaseBundle
{
    public $js = [
        'js/jquery.ui.widget.js',
        'js/jquery.fileupload.js',
        'js/script.js',
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
