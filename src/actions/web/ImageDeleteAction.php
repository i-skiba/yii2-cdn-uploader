<?php
namespace kamaelkz\yii2cdnuploader\actions\web;

use concepture\yii2logic\actions\Action;

/**
 * Class ImageDeleteAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageDeleteAction extends Action
{
    public $redirect = 'index';

    public function run($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        \Yii::$app->filesService->deleteImage($id);
        return
            [
                'success' => [
                    [

                    ]
                ]
            ];
    }
}