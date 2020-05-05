<?php

namespace kamaelkz\yii2cdnuploader\modules\uploader\controllers;


use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use concepture\yii2logic\filters\AjaxFilter;
use kamaelkz\yii2cdnuploader\services\CdnService;

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
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if(! YII_DEBUG) {
            $behaviors['onlyAjax'] = [
                'class' => AjaxFilter::class,
                'only' => [
                    'token'
                ],
            ];
        }
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => [
                'token',
            ],
            'rules' => [
                [
                    'actions' => [
                        'token',
                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'token' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return CdnService
     */
    public function getService()
    {
        return Yii::$app->cdnService;
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

            \Yii::$app->response->format = Response::FORMAT_JSON;
        } catch( \Exception $e) {
            $result = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }
}