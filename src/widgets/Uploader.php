<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use concepture\yii2logic\widgets\WidgetTrait;
use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;

/**
 * Аплодер
 *
 * @todo дополнительные настройки (upload_path, width, height ...)
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Uploader extends Widget
{
    /**
     * @var string
     */
    public $url = '/cdn/default/upload';

    /**
     * @var string
     */
    public static $autoIdPrefix = 'uploader';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->setOptions();
        $this->options['id'] = $this->getId();
        $this->options['class'] = 'uploader';
        if($this->model->{$this->attribute}) {
            $this->options['hiddenOptions'] = [
                'value' => $this->model->{$this->attribute}
            ];
        }

        $input = Html::activeFileInput($this->model, $this->attribute, $this->options);

        # todo: адаптировать под файл, пока изображения хватит
        $pojo = new CdnImagePojo();
        $pojo->load($this->model->{$this->attribute}, '');

        echo  $this->render($this->template, [
            'input' => $input,
            'id' => $this->getId(),
            'hint' => $this->hint,
            'buttonWrapClass' => $this->buttonWrapClass,
            'buttonIconClass' => $this->buttonIconClass,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'pojo' => $pojo,
        ]);

        $this->registerBundle();
        $this->registerScript();
    }

    /**
     * Установка атрибута data-options, передача настроек загрузки
     */
    protected function setOptions()
    {
        # todo: дополнительные настройки
        $params = [

        ];

        $this->options = $this->options + ['data-options' => $params];
    }
}