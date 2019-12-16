<?php
namespace kamaelkz\yii2cdnuploader\actions\web;

use concepture\yii2logic\helpers\DataLoadHelper;
use Yii;
use concepture\yii2logic\actions\Action;
use Exception;

/**
 * Class ImageDeleteAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageDeleteAction extends Action
{
    public function run($id, $attribute, $model_id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model_id){
            $form = $this->getForm();
            $model = $this->getService()->findById($model_id);
            if (! $model) {
                throw new Exception("model not found");
            }
            $form = DataLoadHelper::loadData($model, $form);
            $form->{$attribute} = null;
            $this->getService()->update($form, $model);
        }

        return Yii::$app->filesService->deleteImage($id);
    }
}