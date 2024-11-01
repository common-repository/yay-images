var yay = (function ($, yay) {

    var baseId = 0;

    var imageEdition = {
        featherEditor: new Aviary.Feather({
            apiKey: 'bb86decd680eb1f8',
            apiVersion: 3,
            theme: 'light', // Check out our new 'light' and 'dark' themes!
            tools: 'all',
            appendTo: '',
            maxSize: 1200,
            onSave: function (imageID, newURL) {
                if (yay.getUserData().id > 0) {
                    if ($('#yi-currentid').length > 0) {

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                id: baseId,
                                url: newURL,
                                token: yay.getUserData().token,
                                method: 'saveEditedImage',
                                action: 'my_action'
                            },
                            success: function (edited) {

                                if (!edited.linkdata) {
                                    yay.getFlash().showError('Unexpected error.');
                                }
                                else if (edited.linkdata.error) {

                                    if (edited.linkdata.message == "INVALID_USER_TOKEN") {
                                        yay.getFlash().showError('Sorry, you have to log in to edit a photo.');
                                        yay.getLoginForm().setUser(null);
                                        yay.getLoginForm().loggedOutState();
                                    } else if (edited.linkdata.message == "NO_ACTIVE_SUBSCRIPTION") {
                                        yay.getFlash().showError('Sorry, you need a subscription to edit a photo. Please click <a target="_blank" href="https://yayimages.com/pricing">here</a> to buy a subscription.');
                                    } else {
                                        yay.getFlash().showError('Unexpected error');
                                    }

                                } else {
                                    var url = edited.linkdata.streamingUrl;
                                    $('#yi-image-src').attr('src', url);
                                    yay.getFreePhotosCounter().setNewValue(edited.freeImagesCounter);

                                    $('#yayimages-photo-url').val(edited.linkdata.thumbnails['200x200']);

                                    var img = document.getElementById(imageID),
                                        preview = document.getElementById("yi-image");

                                    imageEdition.editedUrl = url;
                                    img.src = imageEdition.editedUrl;

                                    preview.style.backgroundImage = 'url(' + url + ')';
                                    imageEdition.featherEditor.close();
                                }

                            }
                        });
                    } else {
                        yay.getFlash().showWarning('Please select an image');
                    }
                } else {
                    yay.getLoginForm().show();
                }

               baseId = 0;

            },
            onError: function (errorObj) {
                baseId = 0;
                alert(errorObj.message);
            }
        }),
        editedUrl: '',
        launchEditor: function (id, src, checksum) {

            this.editedUrl = src;

            baseId = checksum;

            this.featherEditor.launch({
                image: id,
                url: src
            });
            return false;
        }
    };

    yay.imageEdition = imageEdition;

    return yay;

})(jQuery, yay || {});


