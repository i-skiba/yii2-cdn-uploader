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
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $config = $this->getConfigurations();
        if(
            isset(Yii::$app->components)
            && isset($config['components'])
        ) {
            $components = &$config['components'];
            $components = ArrayHelper::merge(Yii::$app->components, $components);
        }

        if(
            isset(Yii::$app->params)
            && $config['params']
        ) {
            $params = &$config['params'];
            $params = ArrayHelper::merge(Yii::$app->params, $params);
        }

        Yii::configure(Yii::$app, $config);
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