<?php

namespace kamaelkz\yii2cdnuploader\validators;

use Yii;
use yii\base\InvalidConfigException;
use concepture\yii2logic\validators\ModelValidator;
use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;

/**
 * Валидатор ресурсов цдн
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class ResourceValidator extends ModelValidator
{
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

    /**
     * @var string
     */
    public $type;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->modelClass = $this->getModelClass();
        if(! $this->message) {
            $this->message = Yii::t('uploader', 'Некорректный формат данных.');
        }

        $this->modifySource = false;
    }

    /**
     * Получение класса для валидации по типу
     *
     * @return string|null
     * @throws InvalidConfigException
     */
    private function getModelClass()
    {
        $result = null;

        switch ($this->type) {
            case self::TYPE_FILE :
                # todo : не реализовывалось
                break;
            case self::TYPE_IMAGE:
                $result = CdnImagePojo::class;
                break;
            default:
                throw new InvalidConfigException('Type is not valid.');
        }

        return $result;
    }
}
