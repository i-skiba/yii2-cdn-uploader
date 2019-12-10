<?php

namespace kamaelkz\yii2cdnuploader\traits;

use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;

/**
 * Вспомогательные функции для работы с цдн
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait ModelTrait
{
    /**
     * Возвращает значение ресурса модели по ключу
     *
     * @param string $modelAttribute
     * @param string $key
     * @return |null
     */
    public function getImageAttribute($modelAttribute = 'image', $key = 'path')
    {
        static $pojo;

        if(! $pojo) {
            $pojo = new CdnImagePojo();
        }

        if(! $this->{$modelAttribute}) {
            return null;
        }

        $pojo->load($this->{$modelAttribute}, '');

        if (!isset($pojo->{$key})) {
            return null;
        }

        return $pojo->{$key};
    }
}