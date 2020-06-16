<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use yii\helpers\Html;
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
     * @inheritDoc
     */
    public function beforeRun()
    {
        return parent::beforeRun();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
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