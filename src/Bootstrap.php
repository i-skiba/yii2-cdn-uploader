<?php

namespace kamaelkz\yii2cdnuploader;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Первичная настройка компонента
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        Yii::configure(Yii::$app, $this->getConfigurations());
    }

    /**
     * Конфигурация компонента админки
     *
     * @return array
     */
    private function getConfigurations()
    {
        return require __DIR__ . '/config/main.php';
    }
}