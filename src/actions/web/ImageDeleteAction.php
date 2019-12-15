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

        Yii::$app->filesService->deleteImage($id);
        return
            [
                'success' => [
                    [
                        'id' => 1,
                        'path' => 'dfsfds',
                        'url' => '/assets/d0307205/placeholders/placeholder.jpg',
                        'size' => 20,
                        'height' => 20,
                        'width' => 20,
                        'ratio' => 20,
                        'thumbs' => [
                            'sdfasd'
                        ]
                    ]
                ]
            ];
    }
}