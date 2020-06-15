<?php

namespace kamaelkz\yii2cdnuploader\widgets\bundles;

use concepture\yii2logic\bundles\Bundle as BaseBundle;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnHelperBundle extends BaseBundle
{
    public $js = [
        'helpers/cdn.helper.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    /**
     * @inheritDoc
     */
    public function extendPath()
    {
        return '/plugins/fileupload';
    }
}
