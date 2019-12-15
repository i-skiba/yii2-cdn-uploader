<?php
namespace kamaelkz\yii2cdnuploader\actions\web;

use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\ClassHelper;
use yii\web\UploadedFile;
use Yii;

/**
 * Class ImageUploadAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageUploadAction extends Action
{
    public function run()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $form = $this->getForm();
        $name = ClassHelper::getShortClassName($form);
        if (! isset($_FILES[$name]['name'])){

            return [];
        }

        $namePart = $_FILES[$name]['name'];
        $attribute = array_keys($namePart)[0];

        return Yii::$app->filesService->loadImage($form, $attribute);
    }
}