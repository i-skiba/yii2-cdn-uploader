<?php
namespace kamaelkz\yii2cdnuploader\forms;

use kamaelkz\yii2cdnuploader\enum\FileTypeEnum;
use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class FilesForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class FilesForm extends Form
{
    public $path;
    public $type = FileTypeEnum::IMAGE;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'path',
                    'type',
                ],
                'required'
            ],
        ];
    }
}
