/**
 * AQUANTUM Demo Application 1.0
 *
 * Copyright 2010, Sebastian Tschan, AQUANTUM
 * http://www.aquantum.de
 */

/*jslint browser: true, regexp: false */
/*global $, File */

var Application = function (settings, locale) {
    var tmplHelper,

        TemplateHelper = function (locale, settings) {
            var roundDecimal = function (num, dec) {
                    return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
                };
            this.locale = locale;
            this.settings = settings;
            this.formatFileSize = function (bytes) {
                if (isNaN(bytes) || bytes === null) {
                    return '';
                }
                if (bytes >= 1000000000) {
                    return roundDecimal(bytes / 1000000000, 2) + ' GB';
                }
                if (bytes >= 1000000) {
                    return roundDecimal(bytes / 1000000, 2) + ' MB';
                }
                return roundDecimal(bytes / 1000, 2) + ' KB';
            };
            this.formatFileName = function (fileName) {
                // Remove any path information:
                return fileName.replace(/^.*[\/\\]/, '');
            };
        },

        getAuthenticityToken = function (singleValue) {
            var name = settings.authenticity_token.name,
                parts = $.cookie(name).split('|'),
                obj;
            if (singleValue) {
                obj = {};
                obj[name] = parts[0];
                return obj;
            }
            return {name: name, value: parts[0]};
        },

        addUrlParams = function (url, data) {
            return url + (/\?/.test(url) ? '&' : '?') + $.param(data);
        },

        getFileNode = function (key) {
            return $('#file_' + key);
        },

        deleteItem = function (node, url, callBack) {
            var dialog = $('#dialog_confirm_delete'),
                options,
                form;
            if (!dialog.length) {
                dialog = $('#template_confirm_delete').tmpl(locale).attr('id', 'dialog_confirm_delete');
                options = {
                    modal: true,
                    show: 'fade',
                    hide: 'fade',
                    width: 400,
                    buttons: {}
                };
                options.buttons[locale.buttons.destroy] = function () {
                    $(this).find('form:first').submit();
                };
                options.buttons[locale.buttons.cancel] = function () {
                    $(this).dialog('close');
                };
                dialog.dialog(options);
            }
            form = dialog.find('form').bind('submit', function () {
                dialog.dialog('close');
                $('#loading').fadeIn();
                $.ajax({
                    url: addUrlParams(url, getAuthenticityToken(true)),
                    type: 'DELETE',
                    success: function (data) {
                        $('#loading').fadeOut();
                        callBack(data);
                    }
                });
                return false;
            });
            node.addClass('ui-state-highlight');
            dialog.bind('dialogclose', function () {
                $(this).find('form').unbind('submit').unbind('dialogclose');
                node.removeClass('ui-state-highlight');
            }).dialog('open');
        },

        deleteFile = function (key) {
            var node = getFileNode(key);
            deleteItem(node, '/file-upload/files/' + key + '.json', function (data) {
                node.fadeOut(function () {
                    $(this).remove();
                });
            });
        },

        fileUploadOptions = {
            uploadTable: $('#demo .files'),
            downloadTable: $('#demo .files'),
            buildUploadRow: function (files, index) {
                return $('#template_upload').tmpl(files[index], tmplHelper);
            },
            buildDownloadRow: function (data) {
                var downloadRow = $('#template_download').tmpl(data, tmplHelper);
                if (data.error) {
                    setTimeout(function () {
                        downloadRow.fadeOut(function () {
                            downloadRow.remove();
                        });
                    }, 10000);
                }
                return downloadRow;
            },
            beforeSend: function (event, files, index, xhr, handler, callBack) {
                if (files[index].size > settings.max_file_size) {
                    setTimeout(function () {
                        handler.removeNode(handler.uploadRow);
                    }, 10000);
                    return;
                }
                $.get('/file-upload/upload' + (xhr.upload ? '.json' : ''), function (data) {
                    handler.url = data.replace(/http(s)?:\/\/[^\/]+/, '');
                    callBack();
                });
            },
            previewSelector: null
        },
        
        uploadDemos = {
            auto: {
                beforeSend: fileUploadOptions.beforeSend,
                previewSelector: null
            },
            queue: {
                beforeSend: function (event, files, index, xhr, handler, callBack) {
                    var fileUploadStart = handler.uploadRow.find('.file_upload_start'),
                        fileUploadStartClone;
                    if ($.browser.msie && $.browser.version >= 9) {
                        // IE9 beta crashes when calling show() on the hidden table cell,
                        // but replacing it with a clone and calling show() on this clone works:
                        fileUploadStartClone = fileUploadStart.clone().show();
                        fileUploadStart.replaceWith(fileUploadStartClone);
                        fileUploadStart = fileUploadStartClone;
                    } else {
                        fileUploadStart.show();
                    }
                    fileUploadOptions.beforeSend(event, files, index, xhr, handler, function () {
                        handler.uploadRow.find('.file_upload_start button').click(function () {
                            fileUploadStart.remove();
                            callBack();
                        });
                    });
                },
                previewSelector: '.file_upload_preview'
            }
        },

        initEventHandlers = function () {
            var getKey = function (node) {
                return node.attr('id').replace(/\w+?_/, '');
            };
            $('.file_delete button').live('click', function () {
                deleteFile(getKey($(this).closest('tr')));
                return false;
            });
            $('button.ui-state-default').live(
                'mouseenter mouseleave',
                function () {
                    $(this).toggleClass('ui-state-hover');
                }
            );
            $('#radio input').click(function (e) {
                var val = $(this).val();
                $('#file_upload').fileUploadUI('option', uploadDemos[val]);
                if (val === 'queue') {
                    $('#upload_buttons').show('fast');
                } else {
                    $('#upload_buttons').hide('fast');
                }
            });
            $('#start_uploads').click(function () {
                $('.file_upload_start button').click();
            });
            $('#cancel_uploads').click(function () {
                $('.file_upload_cancel button').click();
            });
        };

    this.initialize = function () {
        tmplHelper = new TemplateHelper(locale, settings);
        initEventHandlers();
        $('#tabs').tabs();
        $('#radio').show().buttonset();
        $('#file_upload').each(function () {
            // Fix for browsers which support multiple file selection but not the File API:
            // https://github.com/blueimp/jQuery-File-Upload/issues#issue/36
            if (typeof File === 'undefined') {
                $(this).find('input:file').each(function () {
                    $(this).removeAttr('multiple')
                        // Fix for Opera, which ignores just removing the multiple attribute:
                        .replaceWith($(this).clone(true));
                });
            }
        }).fileUploadUI(fileUploadOptions);
        $('#loading').fadeOut();
    };
};