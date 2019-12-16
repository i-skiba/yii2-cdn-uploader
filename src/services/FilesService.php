<?php
namespace kamaelkz\yii2cdnuploader\services;

use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
use kamaelkz\yii2cdnuploader\enum\FileTypeEnum;
use kamaelkz\yii2cdnuploader\forms\FilesForm;
use Yii;
use Imagine\Image\ManipulatorInterface;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * Class FilesService
 * @package concepture\yii2uploader\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class FilesService extends Service
{
    /**
     * Загрузка изображения
     *
     * @param $model
     * @param $attribute
     * @return array
     * @throws Exception
     */
    public static function loadImage($model, $attribute)
    {
        $imageFile = UploadedFile::getInstance($model, $attribute);
        if (! $imageFile) {

            return [];
        }

        $name = ClassHelper::getShortClassName($model);
        $name = str_replace("Form", "", $name);
        $name = strtolower($name);
        $directory =  Yii::getAlias('@public') . "/uploads/{$name}";
        if (! is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }

        $uid = uniqid(time(), true);
        $fileName = $uid . '.' . $imageFile->extension;
        $filePath = $directory . "/" . $fileName;
        $imageFile->saveAs($filePath);
        $sizeInfo = getimagesize($filePath);
        $form = new FilesForm();
        $form->path = $filePath;
        $form->type = FileTypeEnum::IMAGE;
        $fileModel = Yii::$app->filesService->create($form);
        if (! $fileModel){

            return [];
        }

        return [
            'success' => [
                [
                    'id' => $fileModel->id,
                    'path' => "/uploads/{$name}/{$fileName}",
                    'url' => "/uploads/{$name}/{$fileName}",
                    'size' => $imageFile->size,
                    'height' => $sizeInfo[1],
                    'width' => $sizeInfo[0],
                    'ratio' => 0,
                    'thumbs' => [

                    ]
                ]
            ]
        ];
    }

    public function deleteImage($id)
    {
        $model = $this->findById($id);
        if (! $model){
            
            return [
                'success' => [
                ]
            ];
        }

        if (file_exists($model->path)) {
            FileHelper::unlink($model->path);
        }
        $this->delete($model);

        return [
            'success' => [
            ]
        ];
    }
}
