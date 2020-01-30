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
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnUploader extends Widget
{
    /**
     * @var string
     */
    public $url = '/cdn/default/token';

    /**
     * @var int
     */
    public $width = 0;

    /**
     * @var int
     */
    public $height = 0;

    /**
     * @var string
     */
    public $strategy = 'default';

    /**
     * @var int
     */
    public $resizeBigger = true;

    /**
     * @var
     */
    public $small = false;

    /**
     * @var string
     */
    public static $autoIdPrefix = 'cdnUploader';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->setOptions();
        $this->options['id'] = $this->getId();
        $this->options['class'] = 'cdnuploader';
        if(! empty($this->model) && ! empty($this->attribute)) {
            if($this->model->{$this->attribute}) {
                $this->options['hiddenOptions'] = [
                    'value' => $this->model->{$this->attribute}
                ];
            }

            $input = Html::activeFileInput($this->model, $this->attribute, $this->options);
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
            'attribute' => $this->attribute,
            'pojo' => $pojo,
            'small' => $this->small,
        ]);

        $this->registerBundle();
        $this->registerScript();
    }

    /**
     * Установка атрибута data-options, передача настроек загрузки
     */
    protected function setOptions()
    {
        $params = [
            'source' => $this->strategy,
            'resize_bigger' => (int) $this->resizeBigger,
            'files_limit' => 1
        ];

        if($this->width > 0 && $this->height > 0) {
            $params['size'] = "{$this->width}x{$this->height}";
            if(! $this->hint) {
                $this->hint = Yii::t(
                    'yii2admin', 'Допустимый размер изображения: {w}x{h} px',
                    [
                        'w' => $this->width,
                        'h' => $this->height
                    ]
                );
            }
        }

        $this->options = $this->options + ['data-options' => $params];
    }
}