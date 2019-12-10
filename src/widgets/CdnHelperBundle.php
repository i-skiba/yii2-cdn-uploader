<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use concepture\yii2logic\bundles\Bundle as BaseBundle;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnHelperBundle extends BaseBundle
{
    public $js = [
        'script/helper.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
