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
     * @var
     */
    public $small = false;

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
        if(! empty($this->model) && ! empty($this->attribute)) {
            if($this->model->{$this->attribute}) {
                $this->options['hiddenOptions'] = [
                    'value' => $this->model->{$this->attribute}
                ];
            }

            $input = Html::activeFileInput($this->model, $this->attribute, $this->options);
            if($this->cropAttribute) {
                $options['value'] = $this->model->{$this->cropAttribute};
                $input .= Html::activeHiddenInput($this->model, $this->cropAttribute, $options);
            }
        } else {
            $this->options['hiddenOptions'] = [
                'value' => $this->value
            ];
            $input = Html::hiddenInput($this->name, $this->value, $this->options['hiddenOptions'])
                . Html::fileInput($this->name, $this->value, $this->options);
        }

        # todo: адаптировать под файл, пока изображения хватит
        $pojo = new CdnImagePojo();
        $pojo->load($this->model->{$this->attribute} ?? $this->value, '');

        echo  $this->render($this->template, [
            'input' => $input,
            'id' => $this->getId(),
            'hint' => $this->hint,
            'buttonWrapClass' => $this->buttonWrapClass,
            'buttonIconClass' => $this->buttonIconClass,
            'model' => $this->model,
            'modelId' => $this->modelId,
            'attribute' => $this->attribute,
            'pojo' => $pojo,
            'small' => $this->small,
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