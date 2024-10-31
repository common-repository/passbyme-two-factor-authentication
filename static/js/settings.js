(function ($) {
    $(document).ready(function () {
        $('#general_settings_submit').on('click', function () {
            $.ajaxCall({
                action: 'pbm_general_settings',
                data: $("#general_settings_form").serialize(),
                success: function (response) {
                    $.successReport(response.data);
                },
                error: function (message) {
                    $.errorReport(message.error);
                }
            });
        });

        // Textarea character counter
        var maxLength = 4094,
            textareaSelector = $('textarea');
        if (textareaSelector.val()) {
            $('#chars').text(maxLength - textareaSelector.val().length + ' Characters Left');
            $('#pbm_message').val(textareaSelector.val());
        }
        textareaSelector.bind('input propertychange', function () {
            $('#pbm_message').val($(this).val());
            $('#chars').text(maxLength - $(this).val().length + ' Characters Left');
        });

        var settingsSelector = $('#advanced-settings');
        //Show advanced settings
        $('#expand-advanced-settings').on('click', function () {
            $('#expand-advanced-settings').hide();
            settingsSelector.slideToggle(300);
        });

        //Hide advanced settings
        $('#close-advanced-settings').on('click', function () {
            settingsSelector.slideToggle(300, function () {
                $('#expand-advanced-settings').show();
            });
        });
    });
}(jQuery));
