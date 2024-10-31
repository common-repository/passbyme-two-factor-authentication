(function ($) {
    $(document).ready(function () {
        $('#pbm_settings_form').ajaxForm({
            url: 'admin-post.php?action=add_app_pfx',
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'json',
            contentType: 'json',
            success: function (response) {
                if (response.error) {
                    $.errorReport(response.error);
                } else if (response.success) {
                    window.location.reload();
                } else {
                    $.errorReport('Invalid response!');
                }
            },
            error: function () {
                $.errorReport('Ajax error occurred while uploading application certificate!');
            }
        });

        $('#pbm_mng_form').ajaxForm({
            url: 'admin-post.php?action=set_mng_pfx',
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'json',
            contentType: 'json',
            success: function (response) {
                if (response.error) {
                    $.errorReport(response.error);
                } else if (response.success) {
                    $("#dialog").dialog({
                        resizable: false,
                        modal: true,
                        buttons: {
                            "Continue": function () {
                                $(this).dialog("close");
                                window.location.reload();
                            }
                        },
                        close: function () {
                            window.location.reload();
                        }
                    });
                } else {
                    $.errorReport('Invalid response!');
                }
            },
            error: function () {
                $.errorReport('Ajax error occurred while uploading management certificate!');
            }
        });
    });
}(jQuery));