<?php

namespace kamaelkz\yii2cdnuploader\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class FileTypeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class FileTypeEnum extends Enum
{
    const IMAGE = 0;

    public static function labels()
    {
        return [
            self::IMAGE => Yii::t('uploader', "Изображение")
        ];
    }
}
