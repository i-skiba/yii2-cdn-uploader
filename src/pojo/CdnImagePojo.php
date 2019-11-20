<?php

namespace kamaelkz\yii2cdnuploader\pojo;

use concepture\yii2logic\pojo\Pojo;

/**
 * Модель данных изобржения с цдн
 *
 * @property integer $id
 * @property string $path
 * @property integer $size
 * @property integer $width
 * @property integer $height
 * @property double $ratio
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnImagePojo extends Pojo
{
    public $id;
    public $path;
    public $size;
    public $height;
    public $width;
    public $ratio;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'path',
                    'size',
                    'height',
                    'width',
                    'ratio',
                ],
                'required'
            ],
            [
                [
                    'path',
                ],
                'string'
            ],
            [
                [
                    'id',
                    'size',
                    'height',
                    'width',
                ],
                'integer'
            ],
            [
                [
                    'ratio'
                ],
                'double'
            ]
        ];
    }
}