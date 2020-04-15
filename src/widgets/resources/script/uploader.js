var componentCdnUploader = {
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
        var pluginOptions = $uploader.attr('plugin-options');
        if(pluginOptions === undefined) {
            return null;
        }

        return JSON.parse(pluginOptions);
    },
    initialization : function () {
        $('.cdnuploader').not('.initialization').fileupload({
            add : function (e, data) {
                var authUrl;
                var $self = $(this);
                var options = componentCdnUploader.getPluginOptions($(this));
                if( typeof options == "object") {
                    $.each(options, function(key, value){
                        $self.fileupload('option', key, value);
                    });
                    authUrl = componentCdnUploader.getAuthUrl(options);
                }


                $self.parent().addClass('disabled');
                $self.attr('disabled', 'disabled');
                var wrapper = $self.closest('.cdn-upload-wrapper');
                var infoContainer = wrapper.find('.file-info');
                infoContainer.addClass('d-none');
                var formData = $.parseJSON($(this).attr('data-options'));
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
                    $wrapper.find('input[type=\'hidden\']').val(result);
                    $(window).trigger('cdnuploader:change', {file:result});
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

        $('.cdnuploader').addClass('initialization');

        // удаление изображения
        $('.cdn-upload-wrapper .file-delete').off('click');
        $('.cdn-upload-wrapper .file-delete').on('click', function(e) {
            e.preventDefault();
            var file_id = $(this).attr('data-file-id');
            if(file_id === undefined) {
                return false;
            }

            var $wrapper = $(this).closest('.cdn-upload-wrapper');
            var $uploader = $wrapper.find('.cdnuploader');
            var options = componentCdnUploader.getPluginOptions($uploader);

            var formData = $.parseJSON($uploader.attr('data-options'));
            CdnHelper.auth(
                componentCdnUploader.getAuthUrl(options),
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
                            $wrapper.find('input[type=\'hidden\']').val('');
                            $(window).trigger('cdnuploader:change', {file:''});
                        }
                    });
                }
            );

        });

        $('.uploader').off();
        $('.uploader').fileupload({
            add : function (e, data) {
                var $self = $(this);
                var options = componentCdnUploader.getPluginOptions($(this));
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
                    $wrapper.find('input[type=\'hidden\']').val(result);
                    $(window).trigger('cdnuploader:change', {file:result});
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
            var options = componentCdnUploader.getPluginOptions($uploader);

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
                    $wrapper.find('input[type=\'hidden\']').val('');
                    $(window).trigger('cdnuploader:change', {file:''});
                }
            });
        });
    }
}
$(document).ready(function () {
    componentCdnUploader.initialization();
});