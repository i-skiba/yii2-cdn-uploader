var CroppieHelper = function() {

};

CroppieHelper.getParams = function (instance) {
    let params = {};
    if (instance !== null) {
        let iter = 1;
        let points = instance.get().points;

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

CroppieHelper.getCdnParams = function(instance, resizeSize) {
    let object = CroppieHelper.getCropParams(instance);
    return {
        positionX: object.x1,
        positionY: object.y1,
        cropWidth: object.width,
        cropHeight: object.height,
        resizeWidth: resizeSize.width,
        resizeHeight: resizeSize.height,
        source: 'profile'
    };
};