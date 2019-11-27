<?php

namespace kamaelkz\yii2cdnuploader;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

/**
 * Первичная настройка компонента
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Yii2CdnUploader implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app) {}

    /**
     * Конфигурация компонента админки
     *
     * @return array
     */
    public static function getConfiguration()
    {
        return require __DIR__ . '/config/main.php';
    }
}