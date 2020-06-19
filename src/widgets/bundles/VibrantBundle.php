<?php

namespace kamaelkz\yii2cdnuploader\widgets\bundles;

use concepture\yii2logic\bundles\Bundle;


/**
 * Vibrant определение цветов на изображении
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class VibrantBundle extends Bundle
{
    /**
     * @var array
     */
    public $js = [
        'scripts/vibrant.min.js',
        'scripts/color-selector.js',
    ];
    /**
     * @var array
     */
    public $css = [
        'styles/main.css',
    ];

    /**
     * @return string|void
     */
    public function extendPath()
    {
        return '/plugins/vibrant';
    }
}