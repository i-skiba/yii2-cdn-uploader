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
class CdnConnector
{
    use ConfigAwareTrait;
    use ErrorsAwareTrait;

    /**
     * @var CdnConnection
     */
    private static $instance = null;
    /**
     * @var string
     */
    private $host;

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
        self::$instance->host = self::$instance->getConfigItem('host');

        return self::$instance;
    }

    /**
     * Сброс экземпляра
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    private function __construct($config) {}

    private function __clone() {}

    /**
     * Загрузка файла
     *
     * @param string $authToken
     * @param string $destinationFilename
     * @param string $departureFilename
     * @param bool $httpClient - бывают редкие случаи когда нужно отрубить http client
     * @return array|bool
     * @throws CdnConnectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($authToken, $destinationFilename, $departureFilename)
    {
        $pathParts = explode('/', $destinationFilename);
        $filename = array_pop($pathParts);
        $params = [
            'path' => implode('/', $pathParts),
            'name' => $filename
        ];
        $fileContent = $this->getUploadContent($departureFilename);

        if(! $fileContent) {
            $this->addError('Failed to get resource content');

            return false;
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
            $client = new Client(['timeout' => 0]);
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
     * Получение содержимого загружаемого файла
     *
     * @param string $departureFilename
     * @param bool $viaHttpClient
     * @return false|string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getUploadContent($departureFilename)
    {
        $result = null;
        $regexParser = new RegexParser();
        $absoluteUrl = $regexParser->parse($departureFilename, RegexPatternEnum::ABSOLUTE_URL);
        #идентификация абсолютного адреса
        if($absoluteUrl == false) {
            $result = file_get_contents($departureFilename);
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
                    $result = $res->getBody()->getContents();
                }
//
//                $headers = $res->getHeaders();
//                if(isset($headers['Content-Type'])) {
//                    if($headers['Content-Type'] === 'application/octet-stream') {
//                        $result = readfile($departureFilename);
//                    }
//                }
            } catch (\Exception $e) {
                $this->addError($e->getMessage());
            }
        }

        return $result;
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
        if (null == ($route = self::$instance->getConfigItem('routes.upload'))) {
            throw new CdnConnectionException('Upload url must be set.');
        }

        return "{$this->host}{$route}";
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
        if (null == ($route = self::$instance->getConfigItem('routes.info'))) {
            throw new CdnConnectionException('Info url must be set.');
        }

        return "{$this->host}{$route}/{$token}";
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
        if (null == ($route = self::$instance->getConfigItem('routes.delete'))) {
            throw new CdnConnectionException('Delete url must be set.');
        }

        return "{$this->host}{$route}/{$token}";
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
        if (null == ($route = self::$instance->getConfigItem('routes.list'))) {
            throw new CdnConnectionException('List url must be set.');
        }

        return "{$this->getConfigItem('host')}{$route}/{$directory}";
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