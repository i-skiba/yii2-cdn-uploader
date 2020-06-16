<?php

namespace kamaelkz\yii2cdnuploader\widgets;

use kamaelkz\yii2cdnuploader\widgets\bundles\CroppieBundle;
use kamaelkz\yii2cdnuploader\widgets\bundles\UploaderBundle;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\Url;
use concepture\yii2logic\widgets\WidgetTrait;
use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;

/**
 * Базовый виджет загрузчика
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class Widget extends InputWidget
{
    use WidgetTrait;
    
    /**
     * @var string
     */
    public $hint;
    /**
     * @var string
     */
    public $buttonWrapClass = 'bg-primary';
    /**
     * @var string
     */
    public $buttonIconClass = 'icon-upload';
    /**
     * @var array
     */
    public $clientOptions = [];
    /**
     * @var array
     */
    public $clientEvents = [];
    /**
     * @var string
     */
    public $template = 'view';
    /**
     * @var string
     */
    public $cropAttribute;
    /**
     * @var array
     */
    public $cropOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->options['plugin-options']['url'] = Url::to([$this->url]);
    }

    public function run()
    {
        $view = $this->getView();
        if($this->cropAttribute) {
            if(! $this->cropOptions) {
                $this->cropOptions = $this->getDefaultCropOptions();
            }

            $this->options['data-crop-options'] = $this->cropOptions;
            CroppieBundle::register($view);
        }

        UploaderBundle::register($view);

        parent::run();
    }

    /**
     * @inheritDoc
     */
    public function registerScript()
    {
        $view = $this->getView();
        $id = $this->getId();
        $js = [];
        if (! empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
        }

        if(! $js) {
            return null;
        }

        $view->registerJs(implode("\n", $js));
    }

    /**
     * @return array
     */
    private function getDefaultCropOptions()
    {
        return [
            'viewport' => [
                'width' => 124,
                'height' => 124
            ],
            'boundary' => [
                'width' => '100%',
                'height' => 316
            ],
            'showZoomer' => true,
            'enableResize' => false,
            'enableOrientation' => true
        ];
    }
}