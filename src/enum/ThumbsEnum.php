<?php

namespace kamaelkz\yii2cdnuploader\enum;

use concepture\yii2logic\enum\Enum;

/**
 * Перечисление префиксов тумбочек по стратегияим
 *
 * Основано на конфиге проекта cdn
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class ThumbsEnum extends Enum
{
    const RETINA_X2 = 'half9';
    # todo: пока не используется
    const RETINA_X3 = 'third9';
}