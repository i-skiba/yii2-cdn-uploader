<?php

namespace kamaelkz\yii2cdnuploader\services;

use Yii;
use concepture\yii2logic\exceptions\Exception;
use concepture\yii2logic\traits\ConfigAwareTrait;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\components\jwt\services\JWTService;

/**
 * Сервис для работы с CDN
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnService extends Service
{
    use ConfigAwareTrait;

    /**
     * @var string
     */
    public $projectName;

    /**
     * @var CdnConnection
     */
    private $connector;

    /**
     * @var JWTService
     */
    private $tokenService;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if(! $this->projectName) {
            throw new CndServiceException('Property $projectName must be set.');
        }

        $this->setConfig(Yii::$app->params['yii2-cdn-uploader']);
        $this->tokenService = Yii::createObject([
            'class' => JWTService::class,
            'config' => $this->getConfigItem('jwt_settings'),
        ]);
    }

    /**
     * @return CdnConnection
     * @throws CdnConnectionException
     */
    public function getConnector()
    {
        if($this->connector) {
            return $this->connector;
        }

        $this->connector = CdnConnection::getInstance($this->getConfigItem('api.routes'));

        return $this->connector;
    }

    /**
     * @return JWTService
     * @throws \yii\base\InvalidConfigException
     */
    public function getJwtService()
    {
        return $this->tokenService;
    }

    /**
     * Возвращает токен авторизации
     *
     * @param array $payload
     * @return string
     */
    public function generateToken(array $payload)
    {
        $payload['project'] = $this->projectName;
        if(! isset($payload['env'])) {
            $payload['env'] = YII_ENV;
        }

        $payload['cdnDomain'] = $this->getConfigItem('proxy.static');
        #подмена стартегии обработки файла
        if(isset($payload['source'])) {
            $source = $payload['source'];
            unset($payload['source']);
            $payload['strategy'] = $source;
        }

        return $this->getJwtService()->encode($payload);
    }

    /**
     * Возвращает абсолютный путь до изображения
     *
     * @param string $path
     *
     * @return string|null
     */
    public function path(string $path)
    {
        if(strpos($path, '/static/') !== false) {
            return $this->getConfigItem('proxy.static') . str_replace('/static', null, $path);
        }

        if(strpos($path, '/web/') !== false) {
            return $this->getConfigItem('proxy.web') . str_replace('/web', null, $path);
        }

        return null;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CndServiceException extends Exception {}