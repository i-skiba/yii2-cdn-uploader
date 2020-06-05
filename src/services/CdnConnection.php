<?php

namespace kamaelkz\yii2cdnuploader\services;

use concepture\yii2logic\exceptions\Exception;
use concepture\yii2logic\traits\ConfigAwareTrait;
use concepture\yii2logic\traits\ErrorsAwareTrait;
use concepture\yii2logic\enum\RegexPatternEnum;
use concepture\yii2logic\parsers\RegexParser;
use GuzzleHttp\Client;

/**
 * Шина соединения с цдн
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnConnection
{
    use ConfigAwareTrait;
    use ErrorsAwareTrait;

    /**
     * @var CdnConnection
     */
    private static $instance = null;

    /**
     * Получение экземпляра
     *
     * @param array $config
     *
     * @return CdnConnection
     */
    public static function getInstance(array $config)
    {
        if (! self::$instance) {
            self::$instance = new static($config);
        }

        self::$instance->setConfig($config);

        return self::$instance;
    }

    /**
     * Сброс экземпляра
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    private function __construct() {}

    private function __clone() {}

    /**
     * Загрузка файла
     *
     * @param string $authToken
     * @param string $destinationFilename
     * @param string $departureFilename
     * @return array|bool
     * @throws CdnConnectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($authToken, $destinationFilename, $departureFilename)
    {
        if (null == self::$instance->getConfigItem('upload')) {
            throw new CdnConnectionException('Upload url must be set.');
        }

        $pathParts = explode('/', $destinationFilename);
        $filename = array_pop($pathParts);
        $params = [
            'path' => implode('/', $pathParts),
            'name' => $filename
        ];
        $regexParser = new RegexParser();
        $absoluteUrl = $regexParser->parse($departureFilename, RegexPatternEnum::ABSOLUTE_URL);
        #идентификация абсолютного адреса
        if($absoluteUrl == false) {
            $fileContent = file_get_contents($departureFilename);
        } else {
            @list(, $schema, $host, $url) = $absoluteUrl;
            if(! empty($url)) {
                $url = rawurlencode(trim($url, '/'));
            }

            $departureFilename = "{$schema}://{$host}/{$url}";
            $client = new Client(['timeout' => 0]);
            $res = null;
            try {
                $res = $client->request(
                    'GET',
                    $departureFilename
                );

                if($res->getStatusCode() == 200) {
                    $fileContent = $res->getBody()->getContents();
                }
            } catch (\Exception $e) {
                $this->addError($e->getMessage());

                return false;
            }
        }
        #файл
        $multipartParams = [
            [
                'name' => 'file[]',
                'contents' => $fileContent,
                'filename' => $filename
            ]
        ];
        #дополнительные параметры запроса
        foreach ($params as $name => $contents) {
            $multipartParams[] = [
                'name' => $name,
                'contents' => $contents
            ];
        }

        try {
            $res = $client->request(
                'POST',
                $this->getUploadUrl(),
                [
                    'headers' => $this->getAuthHeader($authToken),
                    'multipart' => $multipartParams
                ]
            );
        } catch (\Exception $e) {
            $this->addError($e->getMessage());

            return false;
        }

        $expectedCode = 201;
        $responseContent = @json_decode($res->getBody()->getContents());
        if($res->getStatusCode() != $expectedCode) {
            $this->addError($this->getExpectedStatusCodeMessage($res->getStatusCode(), $expectedCode));

            return false;
        }

        if (
            ! isset($responseContent->success)
            || count($responseContent->success) == 0
        ) {
            $this->addError("Response success not defined: " . json_encode($responseContent));

            return false;
        }

        if (! isset($responseContent->failure)) {
            return (array) $responseContent;
        }

        if (! is_array($responseContent->failure)) {
            $this->addError($responseContent->failure);

            return false;
        }

        foreach ($responseContent->failure as $failure) {
            $this->addError($failure);
        }

        if($this->getErrors()) {

            return false;
        }

        return (array) $responseContent;
    }

    /**
     * Удаление файла
     *
     * @param string $authToken
     * @param int|string $token
     * @return bool
     * @throws CdnConnectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($authToken, $token)
    {
        if (null == self::$instance->getConfigItem('delete')) {
            throw new CdnConnectionException('Delete url must be set.');
        }

        $client = new Client(['timeout' => 0]);
        try {
            $res = $client->request(
                'DELETE',
                $this->getDeleteUrl($token),
                [
                    'headers' => $this->getAuthHeader($authToken),
                ]
            );
        } catch (\Exception $e) {
            $this->addError($e->getMessage());

            return false;
        }

        $expectedCode = 204;
        if($res->getStatusCode() != $expectedCode) {
            $this->addError($this->getExpectedStatusCodeMessage($res->getStatusCode(), $expectedCode));

            return false;
        }

        return true;
    }

    /**
     * Информация о файле
     *
     * @param string $authToken
     * @param int|string $token
     * @return bool|mixed
     * @throws CdnConnectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info($authToken, $token)
    {

        if (null == self::$instance->getConfigItem('info')) {
            throw new CdnConnectionException('Info url must be set.');
        }

        $client = new Client(['timeout' => 0]);
        try {
            $res = $client->request(
                'GET',
                $this->getInfoUrl($token),
                [
                    'headers' => $this->getAuthHeader($authToken),
                ]
            );
        } catch (\Exception $e) {
            $this->addError($e->getMessage());

            return false;
        }

        $expectedCode = 200;
        if($res->getStatusCode() !== $expectedCode) {
            $this->addError($this->getExpectedStatusCodeMessage($res->getStatusCode(), $expectedCode));

            return false;
        }

        return @json_decode($res->getBody()->getContents(), true);
    }

    /**
     * Список файлов в директории
     *
     * @param string $authToken
     * @param string $directory
     * @return bool|mixed
     * @throws CdnConnectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list($authToken, $directory)
    {
        if (null == self::$instance->getConfigItem('list')) {
            throw new CdnConnectionException('List url must be set.');
        }

        $client = new Client(['timeout' => 0]);
        $res = $client->request(
            'GET',
            $this->getListUrl($directory),
            [
                'headers' => $this->getAuthHeader($authToken)
            ]
        );

        $expectedCode = 200;
        if($res->getStatusCode() != $expectedCode) {
            $this->addError($this->getExpectedStatusCodeMessage($res->getStatusCode(), $expectedCode));

            return false;
        }

        return @json_decode($res->getBody()->getContents(), true);
    }

    /**
     * Адрес загрузки файлов
     *
     * @return string
     */
    private function getUploadUrl()
    {
        return $this->getConfigItem('upload');
    }

    /**
     * Адрес получения информации о файле
     *
     * @param mixed $authToken
     *
     * @return string
     */
    private function getInfoUrl($token)
    {
        return "{$this->getConfigItem('info')}/{$token}";
    }

    /**
     * Адрес получения информации о файле
     *
     * @param mixed $authToken
     *
     * @return string
     */
    private function getDeleteUrl($token)
    {
        return "{$this->getConfigItem('delete')}/{$token}";
    }

    /**
     * Адрес получения списка файлов по директории
     *
     * @param string $directory
     *
     * @return string
     */
    private function getListUrl($directory)
    {
        return "{$this->getConfigItem('list')}/{$directory}";
    }

    /**
     * Возвращает заголовок авторизации
     *
     * @param string $authToken
     * @return array
     */
    private function getAuthHeader($authToken)
    {
        return [
            'Authorization' => 'Bearer ' . $authToken
        ];
    }

    /**
     * Сообщение о не соответсвии кода ответа и ожидаемого
     *
     * @param int $responseCode
     * @param int $expectedCode
     * @return string
     */
    protected function getExpectedStatusCodeMessage($responseCode, $expectedCode)
    {
        return "Response status code does is {$responseCode} expected {$expectedCode}";
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnConnectionException extends Exception {}