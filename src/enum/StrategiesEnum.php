<?php

namespace kamaelkz\yii2cdnuploader\enum;

use concepture\yii2logic\enum\Enum;

/**
 * Стратегии загрузки файлов на цдн
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class StrategiesEnum extends Enum
{
    const DEFAULT = 'default';
    const TRUSTED = 'trusted';
    const BY_REQUEST = 'by_request';
    const PROFILE = 'profile';
    const BLOG = 'blog';
    const COMMENT = 'comment';
    const CAROUSEL = 'carousel';
    const RETINA = 'retina';
}