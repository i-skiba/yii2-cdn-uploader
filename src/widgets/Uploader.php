<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use yii\helpers\Html;
use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;
use kamaelkz\yii2cdnuploader\widgets\bundles\CroppieBundle;
use kamaelkz\yii2cdnuploader\widgets\bundles\UploaderBundle;

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
     * @var integer
     */
    public $modelId;

    /**
     * @var string
     */
    public static $autoIdPrefix = 'uploader';

    /**
     * @var string
     */
    public $template = 'uploader_view';
    /**
     * @var bool
     */
    public $croppie = false;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        UploaderBundle::register($view);
        if($this->croppie) {
            CroppieBundle::register($view);
        }

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
            'modelId' => $this->modelId,
            'attribute' => $this->attribute,
            'pojo' => $pojo
        ]);

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