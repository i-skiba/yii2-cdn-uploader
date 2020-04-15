<?php

namespace kamaelkz\yii2cdnuploader\modules\uploader\controllers;

use kamaelkz\yii2cdnuploader\services\CdnService;
use Yii;
use yii\web\Response;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\filters\AjaxFilter;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'token';

    /**
     * @inheritDoc
     */
    protected function getAccessRules()
    {
        return [
            [
                'actions' => [
                    'upload',
                    'delete',
                    'token',
                ],
                'allow' => true,
                'roles' => [
                    '@'
                ],
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if(! YII_DEBUG) {
            $behaviors['onlyAjax'] = [
                'class' => AjaxFilter::class,
                'except' => ['options'],
            ];
        }

        return $behaviors;
    }

    /**
     * @return CdnService
     */
    public function getService()
    {
        return Yii::$app->cdnService;
    }

    public function actionUpload()
    {

    }

    public function actionDelete()
    {

    }

    /**
     * Возвращает токен для авторизации
     */
    public function actionToken()
    {
        try {
            $payload = Yii::$app->request->post();
            $host = $this->getService()->getConfigItem('api.host');
            $result = [
                'status' => 'success',
                'token' => $this->getService()->generateToken($payload),
                'staticDomain' => $this->getService()->getConfigItem('proxy.static'),
                'uploadUrl' => "{$host}{$this->getService()->getConfigItem('api.routes.upload')}",
                'deleteUrl' => "{$host}{$this->getService()->getConfigItem('api.routes.delete')}",
                'cropUrl' => "{$host}{$this->getService()->getConfigItem('api.routes.crop')}",
            ];
        } catch( \Exception $e) {
            $result = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }
}