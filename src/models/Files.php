<?php

namespace kamaelkz\yii2cdnuploader\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Локальные файлы
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Files extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function label()
    {
        return Yii::t('uploader', 'Файлы');
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{files}}';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'type'
                ],
                'integer'
            ],
            [
                [
                    'path'
                ],
                'string',
                'max'=>512
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('uploader','#'),
            'path' => Yii::t('uploader','Путь'),
            'type' => Yii::t('uploader','Тип файла'),
            'created_at' => Yii::t('uploader','Дата создания'),
            'updated_at' => Yii::t('uploader','Дата обновления'),
        ];
    }
}
