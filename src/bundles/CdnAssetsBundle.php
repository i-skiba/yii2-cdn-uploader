<?php

namespace kamaelkz\yii2cdnuploader\bundles;

use Yii;
use yii\helpers\Json;
use common\helpers\AppHelper;
use concepture\yii2logic\bundles\Bundle as CoreBundle;

/**
 * Бандл для отдачи ресурсов с cdn
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class CdnAssetsBundle extends CoreBundle
{
    /**
     * @var array
     */
    private $assetHash;

    /**
     * @var string
     */
    private $originSourcePath;

    /**
     * Адрес цдн
     *
     * @return string
     */
    abstract function getRemoteHost();

    /**
     * Директория хранения файлов
     *
     * @return string
     */
    abstract function getRemoteFolder();

    /**
     * Условие включения отдачи ресурсов с цдн
     *
     * @return boolean
     */
    abstract function enableCriteria();

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if($this->enableCriteria()) {
            $this->originSourcePath = $this->sourcePath;
            $this->setAssetHash();
            $this->modifyResourcesPath();
            $this->baseUrl = "{$this->getRemoteHost()}/assets/{$this->getRemoteFolder()}";
        }
    }

    /**
     * Изменение путя к js ресурсу
     *
     * @param string $path
     * @param int $index
     *
     * @return boolean
     *
     * @throws \Exception
     */
    protected function modifyJsPath($path, $index = null)
    {
        $cdnPath = $this->getBuildPath($path);
        if($cdnPath === false) {
            return false;
        }

        $this->js[$index] = $cdnPath;

        return true;
    }

    /**
     * Изменение путя к css ресурсу
     *
     * @param string $path
     * @param int $index
     *
     * @return boolean
     *
     * @throws \Exception
     */
    protected function modifyCssPath($path, $index = null)
    {
        $cdnPath = $this->getBuildPath($path);
        if($cdnPath === false) {
            return false;
        }

        $this->css[$index] = $cdnPath;

        return true;
    }

    /**
     * Установка асет хэша
     *
     * @throws \Exception
     */
    private function setAssetHash()
    {
        if($this->assetHash) {
            return;
        }

        $dir = Yii::getAlias('@frontend');
        $content = file_get_contents("{$dir}/config/assets_hash.json");
        if($content === false) {
            throw new \Exception('`assets_hash.json` is not found');
        }

        $this->assetHash = Json::decode($content);
    }

    /**
     * Изменение путей к ресурсам
     *
     * @throws \Exception
     */
    private function modifyResourcesPath()
    {
        foreach ($this->js as $index => $path) {
            if (!$this->modifyJsPath($path, $index)) {
                continue;
            }
        }

        foreach ($this->css as $index => $path) {
            if (!$this->modifyCssPath($path, $index)) {
                continue;
            }
        }

        $this->sourcePath = null;
    }

    /**
     * Получение пути к билду
     *
     * @param string $path
     *
     * @return boolean|string
     */
    private function getBuildPath($path)
    {
        $resourcePath  = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $this->originSourcePath);
        if(is_array($path) && isset($path[0])) {
            $path = $path[0];
        }

        $fullPath = ( $resourcePath . '/' . $path);
        if(! isset($this->assetHash[$fullPath])) {
            return false;
        }

        $item = $this->assetHash[$fullPath];

        return "{$item['build']}/{$fullPath}";
    }
}