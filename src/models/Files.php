<?php
namespace kamaelkz\yii2cdnuploader\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;


class Files extends ActiveRecord
{
    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('uploader', 'Файлы');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->path;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{files}}';
    }

    /**
     * {@inheritdoc}
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
