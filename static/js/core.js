(function ($) {
    "use strict";
    $.showReport = function (options) {
        var settings;
        settings = $.extend({
            text: "",
            icon: "",
            container: $('.response-container'),
            animation: function () {
                return undefined;
            }
        }, options);
        settings.container.html(
            '<div class="alert collapse">'
            + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
            + '<span class="glyphicon ' + settings.icon + '"></span>'
            + settings.text
            + '</div>'
        );
        settings.animation(settings.container);
    };

    $.errorReport = function (text) {
        $.showReport({
            text: text,
            icon: "glyphicon-remove-circle",
            animation: function (container) {
                container.find('.alert').removeClass('alert-success');
                container.find('.alert').addClass('alert-danger');
                container.find('.alert').fadeIn('fast', function () {
                    container.effect('shake');
                });
            }
        });
    };

    $.warningReport = function (text) {
        $.showReport({
            text: text,
            icon: "glyphicon-warning-sign",
            animation: function (container) {
                container.find('.alert').removeClass('alert-warning');
                container.find('.alert').addClass('alert-warning');
                container.find('.alert').fadeIn('fast');
            }
        });
    };

    $.infoReport = function (text) {
        $.showReport({
            text: text,
            icon: "glyphicon-info-sign",
            animation: function (container) {
                container.find('.alert').removeClass('alert-info');
                container.find('.alert').addClass('alert-info');
                container.find('.alert').fadeIn('fast');
            }
        });
    };

    $.successReport = function(text) {
        $.showReport({
            text: text,
            icon: "glyphicon-ok-circle",
            animation: function (container) {
                container.find('.alert').removeClass('alert-danger');
                container.find('.alert').addClass('alert-success');
                container.find('.alert').fadeIn('fast');
            }
        });
    };

    $.ajaxCall = function (options) {
        var settings;
        settings = $.extend({
            action: "",
            data: "",
            beforeSend: function () {
                return undefined;
            },
            complete: function () {
                return undefined;
            },
            success: function (response) {
                return response;
            },
            error: function (msg) {
                return msg;
            }
        }, options);

        $.ajax({
            url: 'admin-post.php?action=' + settings.action,
            data: settings.data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function () {
                settings.beforeSend();
            },
            success: function (response) {
                if (response.length !== 0) {
                    if (response.hasOwnProperty('data')) {
                        settings.success(response);
                    } else if (response.hasOwnProperty('error')) {
                        settings.error(response);
                    } else {
                        $.errorReport('Unknown response when calling ' + settings.action + ' action method!');
                    }
                } else {
                    //Stay silent
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $.errorReport('Ajax error occurred: ' + jqXHR.status +
                    ' ' + errorThrown + '. Status: ' + textStatus
                );
            },
            complete: function () {
                settings.complete();
            },
            timeout: 10000
        });
    };
}(jQuery));
