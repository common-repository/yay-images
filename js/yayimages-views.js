/**
 * Main front-end admin code
 *
 * @package Yay Images
 * @author  pgs
 */


var yay = (function($, yay) {

    var yiUser = {
        id: 0,
        email: '',
        token: ''
    },
    defSearchParams = {
        phrase: '',
        category: '',
        colors: {},
        exclude: '',
        people: -1,
        vector: 0,
        orientation: 'all',
        photographer: '',
        explicit: 1,
        offset: 0,
        total: 0,
        similarid: 0,
        similarweight: 0,
        similarurl: ''
    },
    searchParams = $.extend(true, {}, defSearchParams),
            standardLimit = 60,
            yayListLoader = false,
            yayAjaxRequest;

    var workflowEvents = {
        init: function() {

            loginForm.initEvents();
            toolbarButtons.initEvents();
        }
    }

    var viewEvents = {
        init: function() {
            advancedFilters.initEvents();
            imageDetails.initEvents();
            searchForm.initEvents();
            flash.initEvents();
            photoPreview.initEvents();
            photographerSearch.initEvents();
        }
    };

    var toolbarButtons = {
        initEvents: function() {
            $('.btn-ok-yay, .btn-thumbnail-yay').die('click').live('click', function(event) {
                if (yiUser.id > 0) {
                    if ($('#yi-currentid').length > 0) {
                        if ($('#yi-image-src').attr('src') == '') {
                            $.ajax({
                                url: ajaxurl,
                                type: 'post',
                                dataType: 'json',
                                data: {id: $('#yi-currentid').val(), token: yiUser.token, method: 'downloadLink', action: 'my_action'},
                                beforeSend: function() {
                                    contentLoader.show($('.media-modal'));
                                },
                                success: function(result) {

                                    if (!result.linkdata) {
                                        flash.showError('Unexpected error.');
                                    }
                                    else if (result.linkdata.error) {

                                        if (result.linkdata.message == "INVALID_USER_TOKEN") {
                                            flash.showError('Sorry, you have to log in to insert a photo.');
                                            loginForm.setUser(null);
                                            loginForm.loggedOutState();
                                        } else if (result.linkdata.message == "NO_ACTIVE_SUBSCRIPTION") {
                                            flash.showError('Sorry, you need a subscription to insert a photo. Please click <a target="_blank" href="https://yayimages.com/pricing">here</a> to buy a subscription.');
                                        } else {
                                            flash.showError('Unexpected error');
                                        }
                                    }
                                    else {
                                        freePhotosCounter.setNewValue(result.linkdata.freeImagesCounter);
                                        $('#yayimages-photo-url').val(result.linkdata.thumbnails['200x200']);
                                        toolbarButtons.insertPhoto(event, result.linkdata.streamingUrl);
                                    }

                                    contentLoader.hide($('.media-modal'));
                                },
                                error: function() {
                                    flash.showError('Unexpected error');
                                    contentLoader.hide($('.media-modal'));
                                }
                            });
                        } else {
                            toolbarButtons.insertPhoto(event, $('#yi-image-src').attr('src'));
                        }
                    } else {
                        flash.showWarning('Please select an image');
                    }
                } else {
                    loginForm.show();
                }

                photoPreview.hide();
            });

            $('#yi-logo').live('click', function() {
                window.open('http://yayimages.com');
            });
        },
        insertPhoto: function(event, url) {

            if ($(event.target).hasClass('btn-ok-yay')) {

                toolbarButtons.loadToEditor(url, $('.yi-image-details h4').html(), $('#yi-photographer').val());
            } else if ($(event.target).hasClass('btn-thumbnail-yay')) {

                toolbarButtons.loadThumbnail(url, $('#yayimages-photo-url').val());
            }
        },
        loadThumbnail: function(url, thumb) {
            var dataS = {
                action: 'my_action',
                method: 'SaveFeaturedImage',
                thumbUrl: url,
                smallThumbUrl: thumb,
                postId: $('#post_ID').val()
            };
            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: dataS,
                success: function(result) {
                    if (window.tb_remove)
                        try {
                            window.tb_remove();
                        } catch (e) {
                        }

                    location.reload();
                },
                error: function() {
                    flash.showError('Unexpected error');
                }
            });
        },
        loadToEditor: function(url, title, photographer) {

            var imageData = {
                'title': title.replace(/\"/g, '\''),
                'src': url,
                'photographer': photographer,
                'photographerUrl': 'http://yayimages.com/#search.photographer=' + photographer
            },
            captionTempl = ($('#yay_setting_yay_image_caption').val() == 'none') ?
                    '<img  title="%(title)s" alt="%(title)s" src="%(src)s" />' :
                    '[caption align="alignnone" width="300"]<img class="size-medium" title="%(title)s"  alt="%(title)s" src="%(src)s" width="300"/>' +
                    'Licensed from: <a target="_blank" href="%(photographerUrl)s">%(photographer)s</a> /' +
                    ' <a target="_blank" href="http://yayimages.com/">yayimages.com</a>[/caption]';
            wp.media.editor.insert(sprintf(captionTempl, imageData));
        }
    }

    var loginForm = {
        initEvents: function() {

            $('.yay-search-images-button').die('click').live('click', function() {
                loginForm.hide();
            });
            $('#yay-login').die('click').live('click', function() {
                loginForm.show();
            });
            $('#yi-login-username, #yi-login-password').die('keypress').live('keypress', function(e) {
                if (e.which == 13) {
                    $('#yi-login-button').trigger('click');
                }
            });
            $('#yi-login-button').die('click').live('click', function() {
                if (loginForm.checkForm()) {

                    var username = $('#yi-login-username').val();
                    var userpass = $('#yi-login-password').val();
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        dataType: 'json',
                        data: {username: username, userpass: userpass, action: 'my_action', method: 'login'},
                        success: function(result) {

                            if (result.login == null) {
                                flash.showError("Unexpected error.");
                            } else if (result.login.error == true) {
                                var message = result.login.message;
                                message = message.replace("INVALID_EMAIL_OR_PASSWORD", "Invalid email or password.");
                                flash.showError(message);
                            } else {
                                loginForm.setUser(result.login);
                                loginForm.loggedInState();
                                loginForm.hide();
                                flash.show("You are logged in.");
                            }
                        },
                        error: function() {
                            flash.showError('Unexpected error');
                        }
                    });
                }
            });
            $('#yay-logout').die('click').live('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {action: 'my_action', method: 'logout'},
                    success: function(result) {

                        if (result != true) {
                            flash.showError('Logout error');
                        } else {
                            loginForm.setUser(null);
                            loginForm.loggedOutState();
                            flash.show("You are logged out.");
                        }
                    },
                    error: function() {
                        flash.showError('Unexpected error');
                    }
                });
            });
            $('#yi-register-button').die('click').live('click', function() {
                window.open('https://yayimages.com/signup');
            });
            $('#yi-plans-button').die('click').live('click', function() {
                window.open('https://yayimages.com/pricing');
            });
        },
        show: function() {
            $('.yay-full').hide();
            $('#yay-up-login').show();
        },
        hide: function() {
            $('.yay-full').hide();
        },
        loggedInState: function() {
            $('#yay-login').hide();
            $('#yay-login-param span').html(yiUser.email);
            $('#yay-login-param').show();
        },
        loggedOutState: function() {
            $('#yay-login').show();
            $('#yay-login-param span').html('');
            $('#yay-login-param').hide();
        },
        setUser: function(userData) {
            if (userData) {
                yiUser.id = userData.id;
                yiUser.email = userData.email;
                yiUser.token = userData.token;
            } else {
                yiUser.id = 0;
                yiUser.email = '';
                yiUser.token = '';
            }
        },
        checkForm: function() {
            var username = $('#yi-login-username').val();
            var userpass = $('#yi-login-password').val();
            $('#yi-login-username, #yi-login-password').removeClass('error');
            if (username === '' || userpass === '') {
                $('#yi-login-username').addClass(username ? '' : 'error');
                $('#yi-login-password').addClass(userpass ? '' : 'error');
                return false;
            }

            return true;
        },
        checkIfLogged: function() {
            var data = {
                action: 'my_action',
                method: 'checkusertoken',
                token: yiUser.token
            }
            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (!result || result.error) {
                        loginForm.setUser(null);
                        loginForm.loggedOutState();
                    }
                },
                error: function() {
                    flash.showError('Unexpected error');
                }
            });
        }
    };
    var advancedFilters = {
        initEvents: function() {

            $('.yi-filters-on').live('change', function() {

                var input = $(this);

                $('.yi-filters-box').css('overflow', 'hidden');

                if (input.is(':checked')) {
                    $('.yi-filters-on').not(this).prop('checked', false);

                    setTimeout(function() {

                        $('.' + input.prop('id') + '-box').css('overflow', 'visible');
                    }, 500);

                }
            });

            // clicking tab event
            $('.yi-search-tab').die('click').live('click', function() {

                $('.yi-search-tab').removeClass('yi-tabs-active');
                $(this).addClass('yi-tabs-active');

                if ($(this).hasClass('yi-tabs-down')) {
                    $('.yi-search-tab').removeClass('yi-tabs-down');
                } else {
                    $('.yi-search-tab').removeClass('yi-tabs-down');
                    $(this).addClass('yi-tabs-down');
                }
            });

            // clicking "reset filters" button event
            $('.yay_resetfilters').die('click').live('click', function() {
                searchForm.clearSearch();
                searchForm.performSearch(true);
                advancedFilters.filtersCount();

            });

            // opening filters layer
            $('.yi-filters .options label').die('click').live('click', function() {
                $(this).parent().parent().parent().find('.yi-filter-type').trigger('click');
            });

            $('.yi-filter-type').die('click').live('click', function() {
                if ($(this).is(':checked')) {
                    $('.yi-filter-type').not(this).prop('checked', false);
                    var newTop = (-1) * $(this).siblings('.options').find(':checked').siblings('label').find('span').position().top;
                    $(this).siblings('.options').css({top: newTop - 1});
                }
            });

            // selecting filters option
            $('.yi-filters .options input').die('change').live('change', function() {
                searchForm.initSearch($(this).attr('rel'), $(this).val());
                advancedFilters.fitersSet();
            });

            // clicking out of the filters layer
            $(document).click(function(event) {
                if (!$(event.target).closest('.yi-filters').length) {
                    $('.yi-filter-type').prop('checked', false);
                }
            })


            // selecting color event
            $('.yi-adv-color').die('click').live('click', function() {

                var status = $(this).attr('status');
                var relval = $(this).attr('relval');
                var colors = searchParams.colors;

                if (status == '0') {
                    $(this).attr('status', 1);
                    colors[relval] = 1;
                } else if (status == '1') {
                    $(this).attr('status', -1);
                    colors[relval] = -1;
                } else if (status == '-1') {
                    $(this).attr('status', 0);
                    delete colors[relval];
                }

                searchForm.initSearch('colors', colors);
            });

            $('#yi-search-adv-visual-close').die('click').live('click', function() {
                searchForm.showSimilar(0, '');
            });

        },
        filtersCount: function() {

            var filtersParams = {
                'visual': {
                    counter: true,
                    params: ['similarid']
                },
                'color': {
                    counter: true,
                    params: ['colors']
                },
                'category': {
                    counter: false,
                    params: ['category']
                },
                'vector': {
                    counter: false,
                    params: ['vector']
                },
                'orientation': {
                    counter: false,
                    params: ['orientation']
                },
                'people': {
                    counter: false,
                    params: ['people']
                },
                'explicit': {
                    counter: false,
                    params: ['explicit']
                }

            },
            totalCount = 0,
            resetCount = 0;

            for (var filter in filtersParams) {

                var count = 0;
                for (i = 0; i < filtersParams[filter].params.length; i++) {

                    var currentValue = searchParams[filtersParams[filter].params[i]],
                            defValue = defSearchParams[filtersParams[filter].params[i]];
                    if (currentValue != defValue) {
                        count = (typeof currentValue == "object") ? Object.keys(currentValue).length : 1;
                    }
                }

                if(filtersParams[filter].counter)
                    totalCount += count;

                resetCount += count;

                $('.yi-search-adv-' + filter).find('.title .count').html((count > 0) ? count : '');
                if (count > 0) {
                    $('.yi-search-adv-' + filter).find('.title .count').show();
                } else {
                    $('.yi-search-adv-' + filter).find('.title .count').hide();
                }
            }

            $('#yi-search-tab-adv').find('.count').html((totalCount > 0) ? totalCount : '');
            if (totalCount > 0) {
                $('#yi-search-tab-adv').find('.count').show();
            } else {
                $('#yi-search-tab-adv').find('.count').hide();
            }

            if (resetCount > 0) {
                $('.yay_resetfilters').show();
            } else {
                $('.yay_resetfilters').hide();
            }

            return totalCount;
        },
        fitersSet: function() {
            var optionsWrapper = $('.yi-filters').find('.options');
            optionsWrapper.removeClass('yi-filter-on');
            optionsWrapper.each(function() {
                if (!$(this).find('input:first').is(':checked')) {
                    $(this).addClass('yi-filter-on');
                }
            });
        }

    };
    var photographerSearch = {
        options: {
            label: '#yi-photos-loaded-photographer',
            labelName: '#yi-photos-loaded-photographer-name',
            closeButton: '#yi-photos-loaded-photographer-close'
        },
        initEvents: function() {
            $(photographerSearch.options.closeButton).die('click').live('click', function() {
                photographerSearch.clearSearch();
            });
        },
        initSearch: function(name) {
            searchForm.initSearch('photographer', name);
            photographerSearch.showLabel(name);
        },
        showLabel: function(name) {
            $(photographerSearch.options.labelName).html(name);
            $(photographerSearch.options.label).show();
        },
        hideLabel: function() {
            $(photographerSearch.options.labelName).html('');
            $(photographerSearch.options.label).hide();
        },
        clearSearch: function() {
            this.hideLabel();
            searchForm.initSearch('photographer', '');
        }
    };



    var imageDetails = {
        status: 0,
        imageId: 0,
        initEvents: function() {

            $('.yay_imagedetails').die('click').live('click', function() {

                var relid = $(this).attr('relid'),
                        imageData = JSON.parse($('#imageData_' + relid).text().trim());

                $('#yi-side-section').html('');
                $('.yay_imagedetails').removeClass('yay-image-selected');
                $('.yay_imagedetails[relid=' + relid + ']').addClass('yay-image-selected');

                var data = {
                    action: 'my_action',
                    method: 'imagedetails',
                    id: relid
                }

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        contentLoader.show($('#yi-side-section'));
                    },
                    success: function(result) {
                        contentLoader.hide($('#yi-side-section'));
                        $('#yi-side-section').html(result);

                        imageDetails.populateData(relid, imageData);
                        imageDetails.status = 0;
                        imageDetails.imageId = relid;
                        imageDetails.loadAdvanced();
                    },
                    error: function() {
                        flash.showError('Unexpected error');
                        contentLoader.hide($('#yi-side-section'));
                    }
                });
            });

            $('.similarimages_lt').die('click').live('click', function() {
                var y_offset = $('#yi-similar-offset').val();
                var y_limit = $('#yi-similar-limit').val();
                var y_total = $('#yi-similar-total').val();
                var y_id = $('#yi-currentid').val();
                if (y_offset > 0) {
                    y_offset = parseInt(y_offset) - parseInt(y_limit);
                    var data = {
                        action: 'my_action',
                        method: 'similarimages',
                        offset: y_offset,
                        limit: y_limit,
                        total: y_total,
                        id: y_id
                    }
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        beforeSend: function() {
                            contentLoader.showInside($('#yi-similar-photos'));
                        },
                        success: function(result) {
                            $('#yi-similar-photos').html(result);
                        },
                        error: function() {
                            contentLoader.hide($('#yi-similar-photos'));
                            flash.showError('Unexpected error');
                        }
                    });
                }
            });
            $('.similarimages_gt').die('click').live('click', function() {
                var y_offset = $('#yi-similar-offset').val();
                var y_limit = $('#yi-similar-limit').val();
                var y_total = $('#yi-similar-total').val();
                var y_id = $('#yi-currentid').val();
                if (y_offset < (y_limit + y_total)) {
                    y_offset = parseInt(y_limit) + parseInt(y_offset);
                    var data = {
                        action: 'my_action',
                        method: 'similarimages',
                        offset: y_offset,
                        limit: y_limit,
                        total: y_total,
                        id: y_id
                    }
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        beforeSend: function() {
                            contentLoader.showInside($('#yi-similar-photos'));
                        },
                        success: function(result) {
                            $('#yi-similar-photos').html(result);
                        },
                        error: function() {
                            contentLoader.hide($('#yi-similar-photos'));
                            flash.showError('Unexpected error');
                        }
                    });
                }
            });
            $('.photographerimages_lt').die('click').live('click', function() {
                var y_offset = $('#yi-photographer-offset').val();
                var y_limit = $('#yi-photographer-limit').val();
                var y_total = $('#yi-photographer-total').val();
                var y_ph = $('#yi-photographer').val();
                if (y_offset > 0) {
                    y_offset = parseInt(y_offset) - parseInt(y_limit);
                    var data = {
                        action: 'my_action',
                        method: 'photographerimages',
                        offset: y_offset,
                        limit: y_limit,
                        total: y_total,
                        photographer: y_ph
                    }
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        beforeSend: function() {
                            contentLoader.showInside($('#yi-photographer-photos'));
                        },
                        success: function(result) {
                            $('#yi-photographer-photos').html(result);
                        },
                        error: function() {
                            contentLoader.hide($('#yi-photographer-photos'));
                            flash.showError('Unexpected error');
                        }
                    });
                }
            });
            $('.photographerimages_gt').die('click').live('click', function() {
                var y_offset = $('#yi-photographer-offset').val();
                var y_limit = $('#yi-photographer-limit').val();
                var y_total = $('#yi-photographer-total').val();
                var y_ph = $('#yi-photographer').val();
                if (y_offset < (y_limit + y_total)) {
                    y_offset = parseInt(y_limit) + parseInt(y_offset);
                    var data = {
                        action: 'my_action',
                        method: 'photographerimages',
                        offset: y_offset,
                        limit: y_limit,
                        total: y_total,
                        photographer: y_ph
                    }
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        beforeSend: function() {
                            contentLoader.showInside($('#yi-photographer-photos'));
                        },
                        success: function(result) {
                            $('#yi-photographer-photos').html(result);
                        },
                        error: function() {
                            contentLoader.hide($('#yi-photographer-photos'));
                            flash.showError('Unexpected error');
                        }
                    });
                }
            });
            $('.yay_photographer').die('click').live('click', function() {
                photographerSearch.initSearch($(this).attr('relname'));
            });
            $('.yay-similarimages').die('click').live('click', function() {
                searchForm.clearSearch();
                searchForm.showSimilar($(this).attr('relid'), $(this).attr('relurl'));
            });
            $('#yi-image-aviary-edit').die('click').live('click', function() {
                if (yiUser.id > 0) {
                    if ($('#yi-currentid').length > 0) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function() {
                                contentLoader.show($('.media-modal'));
                            },
                            data: {id: $('#yi-currentid').val(), token: yiUser.token, method: 'editBase', action: 'my_action'},
                            success: function(result) {

                                if (!result.linkdata) {
                                    flash.showError('Unexpected error.');
                                    contentLoader.hide($('.media-modal'));
                                }
                                else if (result.linkdata.error) {

                                    if (result.linkdata.message == "INVALID_USER_TOKEN") {
                                        flash.showError('Sorry, you have to log in to edit a photo.');
                                        loginForm.setUser(null);
                                        loginForm.loggedOutState();
                                    } else if (result.linkdata.message == "NO_ACTIVE_SUBSCRIPTION") {
                                        flash.showError('Sorry, you need a subscription to edit a photo. Please click <a target="_blank" href="https://yayimages.com/pricing">here</a> to buy a subscription.');
                                    } else {
                                        flash.showError('Unexpected error');
                                    }

                                    contentLoader.hide($('.media-modal'));
                                } else {
                                    var url = result.linkdata.url;
                                    $('#yi-image-src').attr('src', url);
                                    contentLoader.hide($('.media-modal'));
                                    freePhotosCounter.setNewValue(result.freeImagesCounter);
                                    return yay.imageEdition.launchEditor('yi-image-src', url, result.linkdata.id);
                                }

                            },
                            error: function() {
                                contentLoader.hide($('.media-modal'));
                            }
                        });
                    } else {
                        flash.showWarning('Please select an image');
                    }
                } else {
                    loginForm.show();
                }
            });
            $('#yi-side-section').scroll(function(pos) {
                imageDetails.loadAdvanced();
            });
            $('.yay-keyword').die('click').live('click', function() {
                searchForm.initSearch('phrase', $(this).text());
                $('#yi-search-key').val($(this).text());
            });
            $('#yi-image-preview-button').die('click').live('click', function() {
                var url = $("#yi-image-src").attr('src') ? $("#yi-image-src").attr('src') : $(this).attr('relurl');
                photoPreview.show(url);
            });
        },
        populateData: function(id, data) {
            $('#yi-image').css('background-image', "url('" + data.thumbnails['300px'] + "')");
            $('#yi-image-src').attr('title', data.title);

            $('.yi-image-details .yi-image-info-aviary').attr('relpath', data.thumbnails['300px']);
            $('.yi-image-details h4').html(data.title);
            $('.yi-image-details .yi-image-info-description').html(data.description);
            $('.yi-image-details .yi-image-info-photographer').html(data.photographer);
            $('.yi-image-details .yi-image-info-photographer').attr('relname', data.photographer);

            $('.yay-similarimages').attr('relid', id);
            $('.yay-similarimages, #yi-similar-button').attr('relurl', data.thumbnails['300px']);
            $('.yay_photographer').attr('relname', data.photographer);
            $('#yi-photographer').val(data.photographer);

            $('#yi-image-preview-button').attr('relurl', data.previews['512px']);

            if (data.category === 'celebrity') {
                $('.yi-image-details .yay-celebrity-icon').show();
                $('#yi-image-aviary-edit').hide();
            }
        },
        loadAdvanced: function() {
            var similarBoxPosition = $('#yi-image-details-additional').position(),
                    detailsBoxHeight = $('#yi-side-section').height();
            if (similarBoxPosition && detailsBoxHeight > similarBoxPosition.top && imageDetails.status == 0) {
                imageDetails.status = 1;
                var data1 = {
                    action: 'my_action',
                    method: 'imagedetailsadditional',
                    id: imageDetails.imageId
                }
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: data1,
                    beforeSend: function() {
                        contentLoader.showInside($('#yi-similar-photos'));
                        contentLoader.showInside($('#yi-photographer-photos'));
                        contentLoader.showInside($('#yi-keywords-content'));
                        contentLoader.showInside($('#yi-license-content'));
                    },
                    success: function(result) {
                        $('#yi-similar-photos').html(result.similarimages);
                        $('#yi-photographer-photos').html(result.photographerimages);
                        $('#yi-keywords-content').html(result.imagekeywords);
                        $('#yi-license-content').html(result.imagedetails.license);
                    },
                    error: function() {
                        contentLoader.hide($('#yi-similar-photos'));
                        contentLoader.hide($('#yi-photographer-photos'));
                        contentLoader.hide($('#yi-keywords-content'));
                        contentLoader.hide($('#yi-license-content'));
                        imageDetails.status = 0;
                    }
                });
            }

        }
    };
    var photoPreview = {
        initEvents: function() {
            $('#yi-photo-preview-close').die('click').live('click', function() {
                photoPreview.hide();
            })
        },
        show: function(url) {
            $('#yi-photo-preview-img-bg').css('background-image', "url('" + url + "')");
            $('#yi-photo-preview-img-ins').attr('src', url);
            $('#yi-photo-preview').addClass('active');
        },
        hide: function() {
            $('#yi-photo-preview').removeClass('active');
        }
    }

    var searchForm = {
        initEvents: function() {
            $('#yay_search').die('click').live('click', function() {
                searchForm.initSearch($('#yi-search-key').attr('rel'), $('#yi-search-key').val());
            });
            $('#yi-search-key').die('keypress').live('keypress', function(e) {
                if (e.which == 13) {
                    searchForm.initSearch($('#yi-search-key').attr('rel'), $('#yi-search-key').val());
                }
            });
        },
        initSearch: function(relField, relValue) {
            searchForm.clearOffset();
            searchForm.appendSearchParam(relField, relValue);
            searchForm.performSearch(true);
            advancedFilters.filtersCount();
        },
        clearSearch: function() {
            searchParams = $.extend(true, {}, defSearchParams);
            $('#yi-search-key').val('');
            $('.yi-adv-color').attr('status', 0);
            $('#yi-exclude-keywords').val('');
            $('input[name = "yi-search-category"]').first().prop('checked', true);
            $('input[name = "yi-search-people"]').first().prop('checked', true);
            $('input[name = "yi-search-vector"]').first().prop('checked', true);
            $('input[name = "yi-search-orientation"]').first().prop('checked', true);
            $('input[name = "yi-search-explicit"]:nth-child(2)').prop('checked', true);
            $('#yi-search-adv-visual-image').prop('src', '');
            $('.yi-search-adv-visual').removeClass('active');
            $('.yi-filters-adv-box').removeClass('yi-similar-search-active');
            photographerSearch.hideLabel();
            advancedFilters.fitersSet();
        },
        appendSearchParam: function(param, value) {
            searchParams[param] = value;
        },
        performSearch: function(force) {

            var dataS = searchParams,
                    forceLoading = force || false;
            dataS.action = 'my_action';
            dataS.method = 'imagesearch';
            if (!yayListLoader || forceLoading) {

                if (typeof yayAjaxRequest === "object") {
                    yayAjaxRequest.abort();
                    contentLoader.hide($('#yi-photo-list'));
                }

                yayAjaxRequest = $.ajax({
                    url: ajaxurl,
                    type: 'post', dataType: 'json',
                    data: dataS,
                    beforeSend: function() {
                        yayListLoader = true;
                        contentLoader.show($('#yi-photo-list'));
                    },
                    success: function(result) {

                        if (result.offset == 0) {
                            $('#yi-photo-infinite-list').html('');
                            $('#yi-photo-list').animate({
                                scrollTop: 0
                            });
                        }

                        $('#yi-photo-infinite-list').append(result.content);

                        if (result.similarurl != '') {
                            $('#yi-search-adv-visual-image').attr('src', result.similarurl);
                            $('.yi-search-adv-visual').addClass('active');
                        }

                        $('#yi-photos-loaded-start').html((result.total > 0) ? 1 : 0);
                        $('#yi-photos-loaded-total').html(result.total);
                        $('#yi-photos-loaded-end').html(searchParams.offset + result.count);
                        $('#yi-photos-loaded').css('visibility', 'visible');
                        searchParams.total = result.total;
                        contentLoader.hide($('#yi-photo-list'));
                        yayListLoader = false;
                    },
                    error: function(jqXHR, exception) {
                        if (exception != "abort") {
                            flash.showError('Unexpected error');
                        }
                        contentLoader.hide($('#yi-photo-list'));
                    }
                });
            }
        },
        addSearchOffset: function() {
            var offs = searchParams.offset;
            var total = searchParams.total;
            if (offs + standardLimit < total) {
                offs += standardLimit;
                searchParams.offset = offs;
                searchForm.performSearch(true);
            }

        },
        clearOffset: function() {
            searchParams.offset = 0;
        },
        showSimilar: function(id, url) {

            searchForm.appendSearchParam('similarid', id);
            searchForm.appendSearchParam('similarurl', url);
            searchForm.performSearch(true);
            advancedFilters.filtersCount();

            $('#yi-search-adv-visual-image').prop('src', url);

            if (id) {
                $('.yi-filters-adv-box').addClass('yi-similar-search-active');
            } else {
                $('.yi-filters-adv-box').removeClass('yi-similar-search-active');
            }
        }
    }

    var contentLoader = {
        show: function($content) {
            $content.prepend('<div class="loading-bar-container loading-bar-animation"><div class="loading-bar-section"><div class="loading-bar clearfix"><div class="loading-icon"><div class="frame"></div><div class="graphic"></div></div><div class="loading-text"><p>Loading...</p></div></div></div></div>');
            $(".loading-bar-container").css({width: $content.width()}).fadeIn();
        },
        showInside: function($content) {
            $content.prepend('<div id="loading-bar" class="loading-bar-container inside loading-bar-animation"><div class="loading-bar-section"><div class="loading-bar clearfix"><div class="loading-icon"><div class="frame"></div><div class="graphic"></div></div><div class="loading-text"><p>Loading...</p></div></div></div></div>');
            $(".loading-bar-container").fadeIn();
        },
        hide: function($content) {
            $content.find(".loading-bar-container").remove();
        }
    };
    var flash = {
        initEvents: function() {
            $('#yi-message-close').die('click').live('click', function() {
                $('#yi-flash-msg').fadeOut('slow');
            });
        },
        showError: function(message, title) {
            if (message == "")
                return;
            title = title || '';
            $('#yi-flash-msg dd').removeClass('warning').addClass('error');
            $('#yi-flash-msg li .message-title').html(title);
            $('#yi-flash-msg li .message-content').html(message);
            $('#yi-flash-msg').show();
        },
        showWarning: function(message, title) {
            if (message == "")
                return;
            title = title || '';
            $('#yi-flash-msg dd').removeClass('error').addClass('warning');
            $('#yi-flash-msg li .message-title').html(title);
            $('#yi-flash-msg li .message-content').html(message);
            $('#yi-flash-msg').show();
            this._fadeOut();
        },
        show: function(message, title) {
            if (message == "")
                return;
            title = title || '';
            $('#yi-flash-msg dd').removeClass('error').removeClass('warning');
            $('#yi-flash-msg li .message-title').html(title);
            $('#yi-flash-msg li .message-content').html(message);
            $('#yi-flash-msg').show();
            this._fadeOut();
        },
        _fadeOut: function() {

            setTimeout(function() {

                $('#yi-flash-msg').fadeOut('slow');
            }, 3000);
        }
    };
    var visualSlider = {
        currentSimilarWeightValue: 90,
        init: function() {

            var minSlide = 30,
                    maxSlide = 100,
                    initialStep = visualSlider.currentSimilarWeightValue;
            $("#yi-search-adv-visual-slider").slider({
                min: minSlide,
                max: maxSlide,
                range: "min",
                value: initialStep,
                change: function(event, ui) {
                    visualSlider.currentSimilarWeightValue = ui.value;
                    searchForm.appendSearchParam('similarweight', visualSlider.currentSimilarWeightValue);
                    searchForm.performSearch(true);
                }
            });
        }
    }

    var sizeSlider = {
        maxSquare: 200,
        minSquare: 100,
        maxSlide: 50,
        init: function() {
            var resizeStep = (sizeSlider.maxSquare - sizeSlider.minSquare) / sizeSlider.maxSlide,
                    initialStep = (120 - sizeSlider.minSquare) / resizeStep;
            $("#yi-slider").slider({
                min: 1,
                max: sizeSlider.maxSlide,
                range: "min",
                value: initialStep,
                slide: function(event, ui) {
                    var maxS = sizeSlider.minSquare + ui.value * resizeStep;
                    yay.css.addRule('.yay-image-thumb', 'width', maxS + 'px');
                    yay.css.addRule('.yay-image-thumb', 'height', maxS + 'px');
                }
            });
        }
    }

    var freePhotosCounter = {
        init: function() {
            var counter = $('#yi-photo-free-images-counter').val();

            if (counter > 0) {
                $('#yi-title-bar-message-box').html('You have got ' + counter + ' free photos.');
                $('#yi-title-bar-message-box').show();
            } else {
                $('#yi-title-bar-message-box').hide();
            }
        },
        setNewValue: function(val) {
            $('#yi-photo-free-images-counter').val(val);
            freePhotosCounter.init();
        }
    }


    $.fn.liveDraggable = function(opts) {
        this.live("mouseover", function() {
            if (!$(this).data("init")) {
                $(this).data("init", true).draggable(opts);
                if (!$('.yi-filters-box').data("init")) {
                    $('.yi-filters-box').data("init", true).droppable({
                        over: function() {
                            if (!$('#yi-search-tab-adv').hasClass('yi-tabs-down')) {
                                $('#yi-search-tab-adv').trigger('click');
                            }
                        },
                        drop: function(ev, ui) {
                            var element = $(ui.draggable).clone(),
                                    imageData = JSON.parse(element.find('.imageData').text().trim());

                            searchForm.showSimilar(element.attr('relid'), imageData.thumbnailUrl);
                        }
                    });
                }
            }
        });
        return $();
    };
    $('.yay-image-thumb').liveDraggable({
        appendTo: '#yi-main',
        containment: '#yi-main',
        helper: function() {
            return $(this).clone().addClass('dragged');
        }
    });

    var pluginLayout = {
        init: function() {
            var activatedView = false;
            var media = wp.media;
            /**
             * The title bar, containing the logo, "privacy" and "About" links
             */
            media.view.YayTitleBar = media.View.extend({template: media.template('yay-title-bar')});
            media.view.YayContent = media.View.extend({template: media.template('yay-content'),
                ready: function() {

                    if (!activatedView) {

                        sizeSlider.init();
                        pluginLayout.initView();
                        $('#yi-photo-list').scroll(function(pos) {
                            var pos = $('#yi-photo-list').scrollTop();
                            var max = $('#yi-photo-list').prop('scrollHeight');
                            var height = $('#yi-photo-list').height();
                            if (height + pos >= max) {
                                searchForm.addSearchOffset();
                            }
                        });
                        pluginLayout.setContentInit();
                        if ($('#yay_login_id').length > 0) {
                            yiUser.id = $('#yay_login_id').val();
                            yiUser.email = $('#yay_login_email').val();
                            yiUser.token = $('#yay_login_token').val();
                            $('#yay-login').hide();
                            $('#yay-login-param span').html(yiUser.email);
                            $('#yay-login-param').show();
                        }

                        viewEvents.init();
                        workflowEvents.init();
                        freePhotosCounter.init();

                        activatedView = true;
                    }

                }});
            media.view.YayToolbar = media.View.extend({template: media.template('yay-toolbar')});
            media.controller.YayImages = media.controller.State.extend({
                // Keep track of create / render handlers to ensure that they
                // are all registered / dereigstered whenever this state is
                // activated or deactivated
                handlers: {
                    'content:create:yay-images-browse': 'createBrowser',
                    'title:create:yay-title-bar': 'createTitleBar',
                    'toolbar:create:yay-images-toolbar': 'createToolbar',
                },
                initialize: function() {
                    activatedView = false;
                },
                turnBindings: function(method) {
                    var frame = this.frame;
                    _.each(this.handlers, function(handler, event) {
                        this.frame[method](event, this[handler], this);
                    }, this);
                },
                activate: function() {
                    this.turnBindings('on');
                },
                deactivate: function() {
                    activatedView = false;
                    this.turnBindings('off');
                },
                createTitleBar: function(title) {
                    title.view = new media.view.YayTitleBar({
                        controller: this,
                    });
                },
                createBrowser: function(content) {

                    content.view = new media.view.YayContent({
                        controller: this,
                    });
                },
                createToolbar: function(toolbar) {
                    toolbar.view = new media.view.YayToolbar({
                        controller: this,
                    });
                }


            });
            var YayImagesFrame = function(parent) {
                return {
                    createStates: function() {
                        parent.prototype.createStates.apply(this, arguments);
                        this.states.add([
                            new media.controller.YayImages({
                                id: 'yayimages',
                                title: 'YAY Images',
                                titleMode: 'yay-title-bar',
                                multiple: true,
                                content: 'yay-images-browse',
                                router: false,
                                menu: 'default',
                                toolbar: 'yay-images-toolbar',
                                selection: new media.model.Selection(null, {multiple: true}),
                                edge: 120,
                                gutter: 8
                            })
                        ]);
                    }
                }
            };
            media.view.MediaFrame.Post = media.view.MediaFrame.Post.extend(YayImagesFrame(media.view.MediaFrame.Post));

            $(window).resize(function() {
                pluginLayout.initView()
            });
        },
        setContentInit: function() {
            setTimeout(function() {

                $('#yi-search-key').val(searchParams.phrase);
                $('#yi-exclude-keywords').val(searchParams.exclude);
                $('input[name=yi-search-people][value="' + searchParams.people + '"]').prop("checked", 'checked');
                $('input[name=yi-search-vector][value="' + searchParams.vector + '"]').prop("checked", 'checked');
                $('input[name=yi-search-orientation][value="' + searchParams.orientation + '"]').prop("checked", 'checked');
                $('input[name=yi-search-explicit][value="' + searchParams.explicit + '"]').prop("checked", 'checked');
                if (searchParams.photographer) {
                    photographerSearch.showLabel(searchParams.photographer);
                }

                var searchCategory = searchParams.category;
                if (searchParams.category == 'all')
                    searchCategory = '';
                $('input[name=yi-search-category][value="' + searchCategory + '"]').attr('checked', 'checked');
                var colorsSet = ["Red", "Orange", "Yellow", "Green", "Cyan", "Blue", "Purple", "Pink", "White", "Grey", "Black", "Brown"];
                for (i = 0; i < colorsSet.length; i++) {
                    $('.yi-adv-color[relval=' + colorsSet[i] + ']').attr('status', searchParams.colors[colorsSet[i]]);
                }

                visualSlider.init();
                searchForm.performSearch();
                advancedFilters.fitersSet();
                advancedFilters.filtersCount();
            }, 500);
        },
        initView: function() {
            pluginLayout.wblock();
            pluginLayout.hblock();
        },
        hblock: function() {
            var elheight = $('.media-modal:visible').height();
            var elh1 = $('.media-frame-title:visible').height();
            var elh2 = $('.media-frame-toolbar:visible').outerHeight();

            $('#yi-main').css('height', (elheight - elh1 - elh2 - 3) + 'px');
        },
        wblock: function() {

            var scrollWidth = pluginLayout.getScrollWidth();
            $('#yi-side-section').css('width', (299 + scrollWidth) + 'px');

            var elwidth = $('#yi-main').width();
            var elw = $('#yi-side-section').width();
            $('#yi-subcontent').css('width', (elwidth - elw - 1) + 'px');

        },
        getScrollWidth: function() {
            var inner = document.createElement('p');
            inner.style.width = "100%";
            inner.style.height = "100%";
            var outer = document.createElement('div');
            outer.style.position = "absolute";
            outer.style.top = "0px";
            outer.style.left = "0px";
            outer.style.visibility = "hidden";
            outer.style.width = "100px";
            outer.style.height = "100px";
            outer.style.overflow = "hidden";
            outer.appendChild(inner);
            document.body.appendChild(outer);
            var w1 = inner.offsetWidth;
            outer.style.overflow = 'scroll';
            var w2 = inner.offsetWidth;
            if (w1 == w2)
                w2 = outer.clientWidth;
            document.body.removeChild(outer);
            return (w1 - w2);
        }
    };



    $(document).ready(function() {

        var media = wp.media;
        pluginLayout.init();

        $(document.body).on('click', '.yayimages', function(e) {
            e.preventDefault();
            if (!media.frames.yay) {
                media.frames.yay = wp.media.editor.open(wpActiveEditor, {
                    state: 'yayimages',
                    frame: 'post'
                });
                workflowEvents.init();
            }
            loginForm.checkIfLogged();
            media.frames.yay.open();
        });
    });

    yay.getUserData = function () {
        return yiUser;
    };

    yay.getFlash = function () {
        return flash;
    };

    yay.getLoginForm = function () {
        return loginForm;
    };

    yay.getFreePhotosCounter = function () {
        return freePhotosCounter;
    };

    return yay;
})(jQuery, yay || {});