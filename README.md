Подключение :

backend|frontend/config/main.php
```php 
    # конфигурация приложения (из коробки return)
    $config = [
        ...
    ];

    return yii\helpers\ArrayHelper::merge(
        ...
        kamaelkz\yii2cdnuploader\Yii2CdnUploader::getConfiguration(),
        $config
        ....
    );
```




//`Для использования локальной загрузки в фаил bootstrap надо добавить альяс @public