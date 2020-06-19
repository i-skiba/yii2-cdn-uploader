<?php
#todo: рефактор - забрал как есть ;
use yii\helpers\Html;
?>
<!-- color by logo -->
<div class="row color-selector" data-color-source="#<?= $wrapperId;?>" data-color="<?= ($model->{$attribute}) ?? '000000' ?>">
    <?php
        $colorTemplate = '<div class="selected-color" data-modal-size="modal-lg"' . ($model->{$attribute} ? ' style="background-color:#' . $model->{$attribute} . ';"' : '') . '></div><div class="mt-1"><span class="text-muted">' . Yii::t('yii2admin', 'Для того что бы выбрать цвет, нужно загрузить лого') . '</span></div>';
    ?>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <?= $colorTemplate;?>
        <?= Html::activeHiddenInput($model, $attribute);?>
        <div class="cs-modal-template d-none">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="form-group field-bookmakerform-color">
                        <label class="control-label" for="bookmakerform-color">
                            <?= Yii::t('yii2admin', 'Выберите цвет')?>
                        </label>
                        <div class="selector-container">
                            <!-- will be generated <div class="cs-color"></div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="form-group field-bookmakerform-color">
                        <label class="control-label" for="bookmakerform-color">
                            <?= Yii::t('yii2admin', 'Или введите код цвета ниже')?>
                        </label>
                        <div class="selector-container">
                            <input type="text" class="color-code" style="float:left;width:108px;height:35px;cursor:pointer;margin-right:3%;padding-left:10px;padding-right:10px;font-size:20px;box-sizing:border-box;"><div class="cs-color"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.cs-modal-template -->
    </div>
</div>