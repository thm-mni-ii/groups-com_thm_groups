/**
 * Created by ezgoing on 14/9/2014.
 */
'use strict';

/**
 * Binds a imageCropper to the given elements of the bootstrap modal.
 *
 * @return null
 **/
var cropboxEventElements = [];

function bindImageCropper(element, attributeID, uID)
{
	var options, cropper, filename = null, previousIndex = false;

	options =
		{
			imageBox: '#' + element + '_imageBox',
			thumbBox: '#' + element + '_thumbBox',
			spinner: '#' + element + '_spinner'
		};

	cropper = new cropbox(options);

	for (var i = 0; i < cropboxEventElements.length; i++)
	{
		if (cropboxEventElements[i] === element)
		{
			previousIndex = i;
		}
	}

	// No previous bind has occurred for the button
	if (previousIndex === false)
	{
		// Add element to event listener list to prevent multiple bindings
		cropboxEventElements.push(element);

		document.querySelector('#jform_' + element).addEventListener('change', function () {
			var reader = new FileReader();

			reader.onload = function (e) {
				options.imgSrc = e.target.result;
				cropper = new cropbox(options);
			};

			var file = this.files[0];
			reader.readAsDataURL(file);

			filename = file.name;
		});

		// Bind save action to 'Upload cropped' button
		document.querySelector('#' + element + '_saveChanges').addEventListener('click', function () {

			// Get current picture
			var blob = cropper.getBlob(),
				fd = new FormData();
			fd.append('fname', 'test.pic');
			fd.append('data', blob);

			jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_thm_groups&controller=profile&task=profile.saveCropped&tmpl=component&profileID="
				+ uID + "&element=" + element + "&attributeID=" + attributeID + "&filename="
				+ filename + "",
				data: fd,
				dataType: 'html',
				processData: false,
				contentType: false
			}).success(function (response) {
				var fileType, value;

				if (response)
				{
					document.getElementById(element + "_IMG").innerHTML = response;
					jQuery('#' + element + "_Modal").modal('toggle');
					fileType = filename.split('.').pop();
					value = uID + '_' + attributeID + '.' + fileType;
					jQuery("#jform_" + element + "_value").val(value);
				}
				else
				{
					jQuery("#" + element + "_IMG").html('');
					jQuery("#jform_" + element + "_value").val('');
				}
			});
		});

		document.querySelector('#' + element + '_switch').addEventListener('click', function () {
			var box = document.getElementById(element + '_thumbBox');

			// Get old values:
			var style = window.getComputedStyle(box);
			var height = style.getPropertyValue('height');
			var width = style.getPropertyValue('width');

			// Set new values:
			box.style.height = width;
			box.style.width = height;
		});

		document.querySelector('#' + element + '_btnZoomIn').addEventListener('click', function () {
			cropper.zoomIn();
		});
		document.querySelector('#' + element + '_btnZoomOut').addEventListener('click', function () {
			cropper.zoomOut();
		});
	}
}

function deletePic(name, attributeID, profileID)
{
	jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_thm_groups&task=profile.deletePicture&tmpl=component&profileID="
		+ profileID + "&attributeID=" + attributeID + "&tmpl=component",
		datatype: "HTML"
	}).success(function (response) {
		if (response === '')
		{
			jQuery("#" + name + "_IMG").html('');
			jQuery("#jform_" + name + "_value").val('');
		}
	});
}

/* Notice: cropbox works with the chopped image shown in the preview box, not the actual image file
 * as a result the cropped image can be considered like a snipped from a screen-shot that is converted into a blob.
 */
var cropbox = function (options) {
	var el = document.querySelector(options.imageBox),
		obj =
			{
				state: {},
				ratio: 1,
				options: options,
				imageBox: el,
				thumbBox: el.querySelector(options.thumbBox),
				spinner: el.querySelector(options.spinner),
				image: new Image(),
				getDataURL: function () {
					var width = this.thumbBox.clientWidth,
						height = this.thumbBox.clientHeight,
						canvas = document.createElement("canvas"),
						dim = el.style.backgroundPosition.split(' '),
						size = el.style.backgroundSize.split(' '),
						dx = parseInt(dim[0]) - el.clientWidth / 2 + width / 2,
						dy = parseInt(dim[1]) - el.clientHeight / 2 + height / 2,
						dw = parseInt(size[0]),
						dh = parseInt(size[1]),
						sh = parseInt(this.image.height),
						sw = parseInt(this.image.width),
						imageData;

					if (this.ratio < 0.5)
					{
						// Smooth image quality when zoomed out:
						var ctx = canvas.getContext("2d");

						/// step 1
						var oc = document.createElement('canvas'),
							octx = oc.getContext('2d');
						oc.width = this.image.width * 0.5;
						oc.height = this.image.height * 0.5;
						octx.drawImage(this.image, 0, 0, oc.width, oc.height);

						/// step 2
						octx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5);

						canvas.width = width;
						canvas.height = height;
						ctx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5,
							parseInt(dx), parseInt(dy), dw, dh);
						imageData = canvas.toDataURL('image/png');
					}
					else
					{
						// Sharp image when not zoomed:
						canvas.width = width;
						canvas.height = height;

						var context = canvas.getContext("2d");

						context.drawImage(this.image, 0, 0, sw, sh, parseInt(dx), parseInt(dy), dw, dh);

						imageData = canvas.toDataURL('image/png');

					}

					return imageData;
				},
				getBlob: function () {
					var imageData = this.getDataURL();
					var b64 = imageData.replace('data:image/png;base64,', '');
					var binary = atob(b64);
					var array = [];
					for (var i = 0; i < binary.length; i++)
					{
						array.push(binary.charCodeAt(i));
					}
					return new Blob([new Uint8Array(array)], {type: 'image/png'});
				},
				zoomIn: function () {
					this.ratio *= 1.1;
					setBackground();
				},
				zoomOut: function () {
					this.ratio *= 0.5;
					setBackground();
				}
			},
		attachEvent = function (node, event, cb) {
			if (node.attachEvent)
			{
				node.attachEvent('on' + event, cb);
			}
			else if (node.addEventListener)
			{
				node.addEventListener(event, cb);
			}
		},
		detachEvent = function (node, event, cb) {
			if (node.detachEvent)
			{
				node.detachEvent('on' + event, cb);
			}
			else if (node.removeEventListener)
			{
				node.removeEventListener(event, render);
			}
		},
		stopEvent = function (e) {
			if (window.event)
			{
				e.cancelBubble = true;
			}
			else
			{
				e.stopImmediatePropagation();
			}
		},
		setBackground = function () {
			var w = parseInt(obj.image.width) * obj.ratio;
			var h = parseInt(obj.image.height) * obj.ratio;

			var pw = (el.clientWidth - w) / 2;
			var ph = (el.clientHeight - h) / 2;

			el.setAttribute('style',
				'background-image: url(' + obj.image.src + '); ' +
				'background-size: ' + w + 'px ' + h + 'px; ' +
				'background-position: ' + pw + 'px ' + ph + 'px; ' +
				'background-repeat: no-repeat');
		},
		imgMouseDown = function (e) {
			stopEvent(e);

			obj.state.dragable = true;
			obj.state.mouseX = e.clientX;
			obj.state.mouseY = e.clientY;
		},
		imgMouseMove = function (e) {
			stopEvent(e);

			if (obj.state.dragable)
			{
				var x = e.clientX - obj.state.mouseX;
				var y = e.clientY - obj.state.mouseY;

				var bg = el.style.backgroundPosition.split(' ');

				var bgX = x + parseInt(bg[0]);
				var bgY = y + parseInt(bg[1]);

				el.style.backgroundPosition = bgX + 'px ' + bgY + 'px';

				obj.state.mouseX = e.clientX;
				obj.state.mouseY = e.clientY;
			}
		},
		imgMouseUp = function (e) {
			stopEvent(e);
			obj.state.dragable = false;
		},
		zoomImage = function (e) {
			var evt = window.event || e;
			var delta = evt.detail ? evt.detail * (-120) : evt.wheelDelta;
			delta > -120 ? obj.ratio *= 1.1 : obj.ratio *= 0.9;
			setBackground();
		};

	obj.image.onload = function () {
		obj.spinner.style.display = 'none';
		setBackground();

		attachEvent(el, 'mousedown', imgMouseDown);
		attachEvent(el, 'mousemove', imgMouseMove);
		attachEvent(document.body, 'mouseup', imgMouseUp);
		var mousewheel = (/Firefox/i.test(navigator.userAgent)) ? 'DOMMouseScroll' : 'mousewheel';
		attachEvent(el, mousewheel, zoomImage);
	};

	if (options.imgSrc !== undefined)
	{
		obj.image.src = options.imgSrc;
	}

	attachEvent(el, 'DOMNodeRemoved', function () {
		detachEvent(document.body, 'DOMNodeRemoved', imgMouseUp)
	});

	return obj;
};
