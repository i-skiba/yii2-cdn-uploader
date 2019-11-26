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
        if(! $config || ! is_array($config)) {
            return;
        }

        foreach ($config as $key => $value) {
            switch ($key) {
                case 'aliases' :
                    foreach ($config['aliases'] as $alias => $path) {
                        Yii::setAlias($alias, $path);
                    }

                    break;
            }

            if(! property_exists(Yii::$app, $key)) {
                continue;
            }

            $item = &$config[$key];
            $item = ArrayHelper::merge(Yii::$app->{$key}, $item);
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