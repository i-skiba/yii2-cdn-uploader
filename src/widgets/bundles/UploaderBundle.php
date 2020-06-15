<?php

namespace kamaelkz\yii2cdnuploader\widgets\bundles;

use concepture\yii2logic\bundles\Bundle;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class UploaderBundle extends Bundle
{
    public $js = [
        'jquery.ui.widget.js',
        'jquery.fileupload.js',
        'uploader.js',
    ];

    public $publishOptions = [
        'forceCopy'=> YII_DEBUG  ? true : false,
        'except' => [
            'server/*',
            'test'
        ],
    ];

    public $depends = [
        'kamaelkz\yii2cdnuploader\widgets\bundles\CdnHelperBundle',
    ];

    /**
     * @inheritDoc
     */
    public function extendPath()
    {
        return '/plugins/fileupload';
    }

}
