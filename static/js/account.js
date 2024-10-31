(function ($) {
    $(document).ready(function () {
        $.ajaxCall({
            action: 'check_account',
            success: function (response) {
                response = response.data;
                $.each(response.organisation, function(key, value) {
                    var item = $('*[data-name='+key+']');
                    switch (key) {
                        case 'email':
                            item.attr("href", "mailto:" + value);
                            break;
                        case 'pricing':
                            if (value === 'free' || value === 'trial') {
                                $('.upgrade-button-container').show();
                            }
                            break;
                        default:
                            break;
                    }
                    item.text(value);
                });
            },
            error: function (message) {
                $.errorReport(message.error);
            }
        });
        $('#change_application').on('click', function () {
            $("#dialog").dialog({
                resizable: false,
                modal: true,
                width: 400,
                buttons: {
                    "Continue": function () {
                        $.ajaxCall({
                            action: 'change_app_pfx',
                            success: function () {
                                window.location.href = "/wp-admin/";
                            },
                            error: function (message) {
                                $.errorReport(message.error);
                            }
                        });
                    },
                    "Cancel": function () {
                        $(this).dialog("close");
                    }
                }
            });
        });
    });
}(jQuery));

