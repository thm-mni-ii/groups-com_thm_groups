/**
 * Created by ezgoing on 14/9/2014.
 */
'use strict';

/**
 * Binds a imageCropper to the given elements of the bootstrap modal.
 *
 * @return null
 **/
const cropboxEventElements = [];

function bindImageCropper(attributeID, uID, mode)
{
    let cropper, filename = null, options, previousIndex = false;

    options =
        {
            imageBox: '#imageBox-' + attributeID,
            mode: mode,
            spinner: null,
            thumbBox: '#thumbBox-' + attributeID
        };

    if (mode === '-1')
    {
        options.spinner = '#spinner-' + attributeID;
    }

    cropper = new cropbox(options);

    for (let i = 0; i < cropboxEventElements.length; i++)
    {
        if (cropboxEventElements[i] === attributeID)
        {
            previousIndex = i;
        }
    }

    // A previous bind has occurred for the button
    if (previousIndex !== false)
    {
        return;
    }

    cropboxEventElements.push(attributeID);
    document.querySelector('#jform_' + attributeID).addEventListener('change', function () {
        const reader = new FileReader();
        let file;

        reader.onload = function (e) {
            options.imgSrc = e.target.result;
            cropper = new cropbox(options);
        };

        file = this.files[0];
        reader.readAsDataURL(file);

        filename = file.name;
    });

    document.querySelector('#saveChanges-' + attributeID).addEventListener('click', function () {

        // Get current picture
        const blob = cropper.getBlob(), fd = new FormData();
        fd.append('fname', 'test.pic');
        fd.append('data', blob);

        jQuery.ajax({
            type: "POST",
            url: rootURI + "?option=com_thm_groups&task=profile.saveCropped&tmpl=component&profileID="
                + uID + "&attributeID=" + attributeID + "&filename="
                + filename + "",
            data: fd,
            dataType: 'html',
            processData: false,
            contentType: false
        }).success(function (response) {
            let fileType, value;

            if (response)
            {
                document.getElementById('image-' + attributeID).innerHTML = response;
                jQuery('#modal-' + attributeID).modal('toggle');
                fileType = filename.split('.').pop();
                value = uID + '_' + attributeID + '.' + fileType;
                jQuery("#jform_" + attributeID + "_value").val(value);
            }
            else
            {
                jQuery("#image" + attributeID).html('');
                jQuery("#jform_" + attributeID + "_value").val('');
            }
        });
    });

    if (mode === '-1')
    {
        document.querySelector('#switch-' + attributeID).addEventListener('click', function () {
            const thumbBox = document.getElementById('thumbBox-' + attributeID),
                style = window.getComputedStyle(thumbBox),
                height = style.getPropertyValue('height'),
                width = style.getPropertyValue('width');

            // Set new values:
            thumbBox.style.height = width;
            thumbBox.style.width = height;
        });
    }

    document.querySelector('#btnZoomIn-' + attributeID).addEventListener('click', function () {
        cropper.zoomIn();
    });

    document.querySelector('#btnZoomOut-' + attributeID).addEventListener('click', function () {
        cropper.zoomOut();
    });
}

function deletePic(attributeID, profileID)
{
    jQuery.ajax({
        type: "POST",
        url: rootURI + "?option=com_thm_groups&task=profile.deletePicture&tmpl=component&profileID="
            + profileID + "&attributeID=" + attributeID + "&tmpl=component",
        datatype: "HTML"
    }).success(function (response) {
        if (response === '')
        {
            jQuery("#image-" + attributeID).html('');
            jQuery("#jform_" + attributeID + "_value").val('');
        }
    });
}

/* Notice: cropbox works with the chopped image shown in the preview box, not the actual image file
 * as a result the cropped image can be considered like a snipped from a screen-shot that is converted into a blob.
 */
const cropbox = function (options) {
    const el = document.querySelector(options.imageBox),
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
        if (options.mode === '-1')
        {
            obj.spinner.style.display = 'none';
        }
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
