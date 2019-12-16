<?php
namespace kamaelkz\yii2cdnuploader\actions\web;

use Yii;
use yii\web\Response;
use concepture\yii2logic\actions\Action;

/**
 * Class ImageDeleteAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageDeleteAction extends Action
{
    public function run($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return Yii::$app->filesService->deleteImage($id);
    }
}