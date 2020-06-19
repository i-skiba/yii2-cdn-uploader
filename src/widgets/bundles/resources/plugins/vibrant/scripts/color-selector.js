$(function(){
	
	colorSelector.init(true);
	
	$('#list-pjax').on('pjax:end', function(){
		colorSelector.init(true);      
	});
	
	// open modal window
	$('body').on('click', '.selected-color.clickable', function() {
		if(!$(this).hasClass('clickable')) {
			return false;
		}

		magicModal.setTitle(yii2admin.t('Color choice'));
		magicModal.setBody($('.cs-modal-template').html());
		magicModal.onClose(function() {
			$('.selected-color.cs-m-open').removeClass('cs-m-open');
		});
		magicModal.show();
		$(this).addClass('cs-m-open');
	});
	
	// select color in modal window
	$('body').on('click', '.cs-color', function() {
		let sc = $('.selected-color.cs-m-open');
		if(sc.length) {
			let color = colorSelector.rgb2hex($(this).css('background-color')).replace('#', '');
			let cs = sc.parents('.color-selector');
			cs.attr('data-color', color);
			cs.find('[type=hidden]').val(color);
			cs.find('.cs-modal-template').html(magicModal.getBody());
			sc.css('background-color', '#' + color);
		}

		magicModal.close();
	});
	
	// color selector inside modal
	$('body').on('keyup', '.color-code', function() {
		let val = ($(this).val().toUpperCase().replace(/[^0-9ABCDEF]/g, '')).substr(0,6);
		$(this).val(val);
		$(this).next('div').css('background-color', '#' + val);
	})
});

var colorSelector = {
	init: function (withEvent) {
		$('.color-selector').each(function () {
			let source = $(this).attr('data-color-source');
			if ($(source).length == 0) {
				return null;
			}
			// find input with image file path
			let input = $(source).find('input[type=hidden]');
			if (withEvent) {
				input.on('change', function (event, param) {
					colorSelector.init(false);
				});
			}

			if (input.val()) {
				// get image path
				let imgSource = $(source).find("img[src$='" + ($.parseJSON(input.val())['path']).split('/').pop(-1) + "']");
				if (imgSource.length) {
					let img = document.createElement('img');
					var el = $(this);
					img.crossOrigin = "Anonymous";
					img.setAttribute('src', imgSource.attr('src'));
					img.addEventListener('load', function () {
						var vibrant = new Vibrant(img, 128, 2);
						var swatches = vibrant.swatches()
						let modalTemplate = el.find('.cs-modal-template');
						let i = 0;
						let cs = modalTemplate.find('.selector-container:first');
						cs.html('');
						for (var swatch in swatches) {
							if (swatches.hasOwnProperty(swatch) && swatches[swatch]) {
								cs.append($('<div class="cs-color"></div>').css('background-color', swatches[swatch].getHex()));
								i++;
							}
						}
					});
				}

				let color = $(this).attr('data-color');
				let cc = $(this).find('.color-code');
				cc.attr('value', color);
				cc.next('.cs-color').css('background-color', '#' + color);
				$(this).find('.selected-color').addClass('clickable');
			} else {
				$(this).find('.selected-color').removeClass('clickable');
			}
		});

	},
	rgb2hex: function (orig) {
		var rgb = orig.replace(/\s/g, '').match(/^rgba?\((\d+),(\d+),(\d+)/i);
		return (rgb && rgb.length === 4) ? "#" +
			("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : orig;
	}
}