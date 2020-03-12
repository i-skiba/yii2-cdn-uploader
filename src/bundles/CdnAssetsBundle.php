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
            $this->modifyResourcesPath();
            $this->baseUrl = "{$this->getRemoteHost()}/assets/{$this->getRemoteFolder()}";
        }
    }

    /**
     * Установка асет хэша
     *
     * @throws \Exception
     */
    private function setAssetHash()
    {
        $dir = Yii::getAlias('@frontend');
        $content = file_get_contents("{$dir}/config/assets_hash.json");
        if($content === false) {
            throw new \Exception('`assets_hash.json` is not found');
        }

        $this->assetHash =  Json::decode($content);
    }

    /**
     * Изменение путей к ресурсам
     *
     * @throws \Exception
     */
    private function modifyResourcesPath()
    {
        $this->setAssetHash();
        $suffix = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $this->sourcePath);
        foreach ($this->js as $index => $path) {
            $cdnPath = $this->getBuildPath($suffix, $path);
            if($cdnPath === false) {
                continue;
            }

            $this->js[$index] = $cdnPath;
        }

        foreach ($this->css as $index => $path) {
            $cdnPath = $this->getBuildPath($suffix, $path);
            if($cdnPath === false) {
                continue;
            }

            $this->css[$index] = $cdnPath;
        }

        $this->sourcePath = null;
    }

    /**
     * Получение пути к билду
     *
     * @param string $suffix
     * @param string $path
     * @return bool|string
     */
    private function getBuildPath($suffix, $path)
    {
        if(is_array($path) && isset($path[0])) {
            $path = $path[0];
        }

        $fullPath = ( $suffix . '/' . $path);
        if(! isset($this->assetHash[$fullPath])) {
            return false;
        }

        $item = $this->assetHash[$fullPath];

        return "{$item['build']}/{$fullPath}";
    }
}