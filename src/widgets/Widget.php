<?php

namespace kamaelkz\yii2cdnuploader\widgets;

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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(! $this->hasModel()) {
            throw new InvalidConfigException("'model' and 'attribute' properties must be specified.");
        }

        $this->options['plugin-options']['url'] = Url::to([$this->url]);
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
}