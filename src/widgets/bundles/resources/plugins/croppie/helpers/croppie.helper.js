var CroppieHelper = function() {
    this.instance = null;
    this.resources = {
        origin: '',
        crop : ''
    };
    this.$modal = null;
};

CroppieHelper.prototype.setInstance = function(instance) {
    this.instance = instance;
};

CroppieHelper.prototype.getParams = function () {
    let params = {};
    if (this.instance !== null) {
        let iter = 1;
        let points = this.instance.get().points;

        for (let item in points) {
            if (iter > 4) {
                break;
            }

            let key = '';
            switch (iter) {
                case 1:
                    key = 'x1';
                    break;
                case 2:
                    key = 'y1';
                    break;
                case 3:
                    key = 'x2';
                    break;
                case 4:
                    key = 'y2';
                    break;
            }

            params[key] = parseInt(points[item]);
            iter++;
        }

        params['width'] = params.x2 - params.x1;
        params['height'] = params.y2 - params.y1;
    }
    return params;
};

CroppieHelper.prototype.getRequestParams = function(strategy) {
    var  object = this.getParams();

    return {
        positionX: object.x1,
        positionY: object.y1,
        cropWidth: object.width,
        cropHeight: object.height,
        resizeWidth: this.instance.options.viewport.width,
        resizeHeight: this.instance.options.viewport.height,
        source: strategy
    };
};

CroppieHelper.prototype.open = function($wrapper, info, options) {
    var self = this,
        $modalContainer = $wrapper.find('#uploader-crop-wrappper'),
        image = new Image();

    image.src = info.url;
    image.onload = function () {
        $modalContainer.html(image);
        var instance = new Croppie($(this)[0], options);
        self.setInstance(instance);

        self.resources.origin = info;
        self.instance.bind({
            url: $(this).attr('src'),
        }).then(function () {});

        self.$modal = $wrapper.find('#uploader-crop-modal');
        self.$modal.modal('show');
    };
}