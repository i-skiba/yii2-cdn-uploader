var componentCdnUploader = (function() {
    'use strict';

    var Component = {
        getAuthUrl : function(options) {
            if(
                typeof options !== "object"
                || typeof options.url === 'undefined'
            ) {
                return null;
            }

            return options.url;
        },
        getPluginOptions : function($uploader) {
            var options = $uploader.attr('plugin-options');
            if(options === undefined) {
                return null;
            }

            return JSON.parse(options);
        },
        getCropOptions : function($uploader) {
            var options = $uploader.data('crop-options');
            if(options === undefined) {
                return null;
            }

            return options;
        },
        helpers : {},
        initialization : function () {
            var $elements = $('.cdnuploader').not('.initialization');

            $elements.each(function() {
                var $wrapper = $(this).closest('.cdn-upload-wrapper');

                $(this).fileupload({
                        add : function (e, data) {
                            var authUrl,
                                $self = $(this),
                                options = Component.getPluginOptions($self);

                            if( typeof options == "object") {
                                $.each(options, function(key, value){
                                    $self.fileupload('option', key, value);
                                });
                                authUrl = Component.getAuthUrl(options);
                            }


                            $self.parent().addClass('disabled');
                            $self.attr('disabled', 'disabled');
                            var infoContainer = $wrapper.find('.file-info');
                            var formData = $.parseJSON($(this).attr('data-options')),
                                strategy = formData.source || null;

                            infoContainer.addClass('d-none');
                            if(strategy !== null && strategy === CdnHelper.strategies.trusted) {
                                formData.name = data.files[0].name;
                            }

                            CdnHelper.auth(
                                authUrl,
                                formData,
                                function(response)
                                {
                                    if(response.status !== 'success') {
                                        componentNotify.pNotify(componentNotify.statuses.error, response.message);

                                        return;
                                    }
                                    data.url = response.uploadUrl;
                                    data.headers = {
                                        'Authorization': 'Bearer ' + response.token
                                    };
                                    data.formData = formData;
                                    if(formData.name !== undefined) {
                                        delete formData['name'];
                                    }

                                    data.submit();
                                }
                            );
                        },
                        done : function(e, data) {
                            if(data.result.failure) {
                                componentNotify.pNotify(componentNotify.statuses.error, data.result.failure);

                                return;
                            }

                            var $self = $(this);
                            if(data.result.success && data.result.success.length > 0) {
                                var files = CdnHelper.parseResponse(data.result.success),
                                    info = files[0],
                                    cropOptions = Component.getCropOptions($self);

                                if(cropOptions !== null) {
                                    Component.helpers.crop.open($wrapper, info, cropOptions);
                                } else {
                                    var $infoContainer = $wrapper.find('.file-info'),
                                        $displayContainer = $wrapper.find('.file-display'),
                                        $nameContainer = $wrapper.find('.file-name'),
                                        $sizeContainer = $wrapper.find('.file-size'),
                                        $deleteControl = $wrapper.find('.uploader-delete-control'),
                                        $image = $('<img\/>', {
                                            src: info.url,
                                            'class': 'card-img img-fluid'
                                        });

                                    delete (info.url);
                                    $deleteControl.attr('data-file-id', info.id);
                                    $displayContainer.html($image);
                                    $nameContainer.html(info.path);
                                    $sizeContainer.html(info.size);
                                    $infoContainer.removeClass('d-none');

                                    var result = JSON.stringify(info),
                                        inputs = $wrapper.find('input[type=\'hidden\']'),
                                        $input = $(inputs.get(0));

                                    $input.val(result);
                                    $input.trigger('change');
                                }
                            }
                        },
                        fail : function(e, data) {
                            var messaga = data.jqXHR.status + ' : ' + data.jqXHR.statusText;

                            componentNotify.pNotify(componentNotify.statuses.error, messaga);
                        },
                        progressall : function(e, data) {
                            var progress = parseInt(data.loaded / data.total * 100, 10),
                                progressBox = $wrapper.find('.progress');

                            progressBox.removeClass('d-none');
                            var progressBar = progressBox.find('.progress-bar');
                            progressBar.css({'width': progress + '%'});
                            progressBar.find('span').html(progress + '%');
                        },
                        always : function(e, data) {
                            var $self = $(this),
                                progressBox = $wrapper.find('.progress');

                            progressBox.addClass('d-none');
                            var progressBar = progressBox.find('.progress-bar'),
                                percent = 0 + '%';

                            progressBar.css({'width': percent});
                            progressBar.find('span').html(percent);
                            $self.parent().removeClass('disabled');
                            $self.removeAttr('disabled');
                        }
                    });
                    $(this).addClass('initialization');
            });

            // удаление изображения
            $('.cdn-upload-wrapper .uploader-delete-control').off('click');
            $('.cdn-upload-wrapper .uploader-delete-control').on('click', function(e) {
                e.preventDefault();
                var file_id = $(this).attr('data-file-id');
                if(file_id === undefined) {
                    return false;
                }

                var $wrapper = $(this).closest('.cdn-upload-wrapper'),
                    $uploader = $wrapper.find('.cdnuploader'),
                    options = Component.getPluginOptions($uploader),
                    formData = $.parseJSON($uploader.attr('data-options'));

                CdnHelper.auth(
                    Component.getAuthUrl(options),
                    formData,
                    function (response) {
                        if (response.status !== 'success') {
                            componentNotify.pNotify(componentNotify.statuses.error, response.message);

                            return;
                        }

                        $.ajax({
                            url: response.deleteUrl + '/'  + file_id,
                            type: 'DELETE',
                            headers: {
                                'Authorization': 'Bearer ' + response.token
                            },
                            success: function () {
                                var $infoContainer = $wrapper.find('.file-info');
                                var $displayContainer = $wrapper.find('.file-display');
                                var $nameContainer = $wrapper.find('.file-name');
                                var $sizeContainer = $wrapper.find('.file-size');
                                var $deleteControll = $wrapper.find('.file-delete');

                                $infoContainer.addClass('d-none');
                                $displayContainer.html('');
                                $deleteControll.removeAttr('data-file-id');
                                $nameContainer.html('');
                                $sizeContainer.html('');
                                var input = $wrapper.find('input[type=\'hidden\']');
                                input.val('');
                                input.trigger('change');
                            }
                        });
                    }
                );
            });
            var cropSaveControlSelector = '.uploader-crop-save-control';
            // кроп изображения
            $(document).off('click', cropSaveControlSelector);
            $(document).on('click', cropSaveControlSelector, function () {
                var uploaderId = $(this).closest('[data-uploader-id]').attr('data-uploader-id');
                if(typeof uploaderId === 'undefined') {
                    return;
                }

                var $uploader = $('#' + uploaderId);
                if($uploader.length === 0) {
                    return;
                }

                var $wrapper = $uploader.closest('.cdn-upload-wrapper'),
                    options = Component.getPluginOptions($uploader),
                    uploadOptions = $.parseJSON($uploader.attr('data-options')),
                    formData = Component.helpers.crop.getRequestParams(uploadOptions.source),
                    filePath = CdnHelper.parsePath(Component.helpers.crop.resources.origin.path);

                CdnHelper.auth(
                    Component.getAuthUrl(options),
                    formData,
                    function (response) {
                        if (response.status !== 'success') {
                            componentNotify.pNotify(componentNotify.statuses.error, response.message);

                            return;
                        }

                        $.ajax({
                            url: response.cropUrl + filePath,
                            type: 'PUT',
                            headers: {
                                'Authorization': 'Bearer ' + response.token
                            },
                            data: formData,
                            success: function (response) {
                                var info = response[0];
                                if(typeof info.id !== 'undefined') {
                                    info.path = CdnHelper.parsePath(info.path);
                                    var $infoContainer = $wrapper.find('.file-info'),
                                        $displayContainer = $wrapper.find('.file-display'),
                                        $nameContainer = $wrapper.find('.file-name'),
                                        $sizeContainer = $wrapper.find('.file-size'),
                                        $deleteControl = $wrapper.find('.file-delete'),
                                        $image = $('<img\/>', {
                                            src: CdnHelper.parseUrl(info.url),
                                            'class': 'card-img img-fluid'
                                        });

                                    $deleteControl.attr('data-file-id', info.id);
                                    $displayContainer.html($image);
                                    $nameContainer.html(info.path);
                                    $sizeContainer.html(info.size);
                                    $infoContainer.removeClass('d-none');

                                    var inputs = $wrapper.find('input[type=\'hidden\']'),
                                        $originInput = $(inputs.get(0)),
                                        $cropInput = $(inputs.get(1));

                                    if(Component.helpers.crop.resources.origin !== '') {
                                        $originInput
                                            .val(JSON.stringify(Component.helpers.crop.resources.origin))
                                            .trigger('change');
                                    }

                                    $cropInput
                                        .val(JSON.stringify(info))
                                        .trigger('change');

                                    Component.helpers.crop.$modal.modal('hide');
                                }
                            }
                        });
                    }
                );
            });
            // редиктирование кропа изображения
            var cropEditControlSelector = '.cdn-upload-wrapper .uploader-crop-edit-control';
            $(document).off('click', cropEditControlSelector);
            $(document).on('click', cropEditControlSelector, function (e) {
                var $wrapper = $(this).closest('.cdn-upload-wrapper'),
                    inputs = $wrapper.find('input[type=\'hidden\']'),
                    $uploader = $wrapper.find('.cdnuploader'),
                    cropOptions = Component.getCropOptions($uploader),
                    $originInput = $(inputs.get(0)),
                    info = JSON.parse($originInput.val());

                Component.helpers.crop.open($wrapper, info, cropOptions);

                return false;
            });

            $('.uploader').off();
            $('.uploader').fileupload({
                add : function (e, data) {
                    var $self = $(this);
                    var options = Component.getPluginOptions($(this));
                    if( typeof options == "object") {
                        $.each(options, function(key, value){
                            $self.fileupload('option', key, value);
                        });
                    }


                    $self.parent().addClass('disabled');
                    $self.attr('disabled', 'disabled');
                    var wrapper = $self.closest('.cdn-upload-wrapper');
                    var infoContainer = wrapper.find('.file-info');
                    infoContainer.addClass('d-none');
                    var formData = $.parseJSON($(this).attr('data-options'));
                    data.formData = formData;
                    data.submit();
                },
                done : function(e, data) {
                    if(data.result.failure) {
                        componentNotify.pNotify(componentNotify.statuses.error, data.result.failure);

                        return;
                    }

                    var $self = $(this);
                    if(data.result.success && data.result.success.length > 0) {
                        var files = CdnHelper.parseResponse(data.result.success);
                        var file = files[0];
                        var info = file;
                        if(file.thumbs !== undefined && file.thumbs.thumb !== undefined) {
                            info = file.thumbs.thumb;
                        }

                        var $wrapper = $self.closest('.cdn-upload-wrapper');
                        var $infoContainer = $wrapper.find('.file-info');
                        var $displayContainer = $wrapper.find('.file-display');
                        var $nameContainer = $wrapper.find('.file-name');
                        var $sizeContainer = $wrapper.find('.file-size');
                        var $deleteControll = $wrapper.find('.file-delete');

                        var image = $('<img\/>', {
                            src: info.url,
                            'class': 'card-img img-fluid'
                        });

                        delete (info.url);
                        $deleteControll.attr('data-file-id', info.id);
                        $displayContainer.html(image);
                        $nameContainer.html(info.path);
                        $sizeContainer.html(info.size);
                        $infoContainer.removeClass('d-none');

                        var result = JSON.stringify(info);
                        let input = $wrapper.find('input[type=\'hidden\']');
                        input.val(result);
                        input.trigger('change');
                    }
                },
                fail : function(e, data) {
                    var messaga = data.jqXHR.status + ' : ' + data.jqXHR.statusText;

                    componentNotify.pNotify(componentNotify.statuses.error, messaga);
                },
                progressall : function(e, data) {
                    var $self = $(this);
                    var wrapper = $self.closest('.cdn-upload-wrapper');
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    var progressBox = wrapper.find('.progress');
                    progressBox.removeClass('d-none');
                    var progressBar = progressBox.find('.progress-bar');
                    progressBar.css({'width': progress + '%'});
                    progressBar.find('span').html(progress + '%');
                },
                always : function(e, data) {
                    var $self = $(this);
                    var wrapper = $self.closest('.cdn-upload-wrapper');
                    var progressBox = wrapper.find('.progress');
                    progressBox.addClass('d-none');
                    var progressBar = progressBox.find('.progress-bar');
                    var percent = 0 + '%';
                    progressBar.css({'width': percent});
                    progressBar.find('span').html(percent);
                    $self.parent().removeClass('disabled');
                    $self.removeAttr('disabled');
                }
            });

            // удаление изображения
            $('.cdn-upload-wrapper .local-file-delete').off('click');
            $('.cdn-upload-wrapper .local-file-delete').on('click', function(e) {
                e.preventDefault();
                var file_id = $(this).attr('data-file-id');
                var delete_url = $(this).attr('data-delete-url');
                var model_id = $(this).attr('data-model-id');
                var model_attribute = $(this).attr('data-model-attribute');
                if(file_id === undefined) {
                    return false;
                }

                var $wrapper = $(this).closest('.cdn-upload-wrapper');
                var $uploader = $wrapper.find('.uploader');
                var options = Component.getPluginOptions($uploader);

                var formData = $.parseJSON($uploader.attr('data-options'));
                $.ajax({
                    url:  delete_url + "/?id=" + file_id+ "&model_id=" + model_id+ "&attribute=" + model_attribute,
                    success: function () {
                        var $infoContainer = $wrapper.find('.file-info');
                        var $displayContainer = $wrapper.find('.file-display');
                        var $nameContainer = $wrapper.find('.file-name');
                        var $sizeContainer = $wrapper.find('.file-size');
                        var $deleteControll = $wrapper.find('.file-delete');

                        $infoContainer.addClass('d-none');
                        $displayContainer.html('');
                        $deleteControll.removeAttr('data-file-id');
                        $nameContainer.html('');
                        $sizeContainer.html('');
                        let input = $wrapper.find('input[type=\'hidden\']');
                        input.val('');
                        input.trigger('change');
                    }
                });
            });
        }
    };

    Component.helpers.crop = new CroppieHelper();

    $(document).ready(function () {
        Component.initialization();
    });

    return Component;
})();