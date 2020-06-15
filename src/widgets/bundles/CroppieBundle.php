<?php

namespace kamaelkz\yii2cdnuploader\widgets\bundles;

use concepture\yii2logic\bundles\Bundle;

/**
 * Бандл кропа изображений
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CroppieBundle extends Bundle
{
    /**
     * @var array
     */
    public $css = [
        "croppie.css",
    ];
    /**
     * @var array
     */
    public $js = [
        'croppie.min.js',
        'helpers/croppie.helper.js',
    ];

    /**
     * @inheritDoc
     */
    public function extendPath()
    {
        return '/plugins/croppie';
    }
}
