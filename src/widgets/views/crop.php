<div id="uploader-crop-modal" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= Yii::t('yii2admin', 'Загрузка изображения');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="uploader-crop-wrappper" class="modal-body pb-0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <?= Yii::t('yii2admin', 'Отменить');?>
                </button>
                <button type="button" class="uploader-crop-save-control btn bg-success btn-labeled btn-labeled-left ml-1">
                    <b><i class="icon-checkmark3"></i></b>
                    <?= Yii::t('yii2admin', 'Сохранить');?>
                </button>
            </div>
        </div>
    </div>
</div>