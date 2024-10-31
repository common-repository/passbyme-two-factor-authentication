(function ($) {
    $(document).ready(function () {
        $('#login').hide();
        $('#pbm-auth-request').show();

        function showError(msg) {
            $('#login').show();
            $('#pbm-auth-request').hide();
            $('#login_error').html(msg);
        }
        var passbyme = $.passbyme2FaClientJs({
            url: ajax_object.ajax_url,
            data: {'action': 'pbm_polling'},
            type: 'GET',
            waiting: function () {
                $('#pbm-identifier').text(passbyme.getSecureIdentifier());
            },
            success: function (resp) {
                switch (resp) {
                    case 'PENDING':
                    case 'DOWNLOADED':
                    case 'NOTIFIED':
                    case 'NOT_NOTIFIED':
                        break;
                    case 'APPROVED':
                        passbyme.stopPolling();
                        $( ".pbm-cubic-pulse" ).removeClass("pbm-cubic-pulse").addClass("pbm-cubic-complete");
                        setTimeout(function() {
                            window.location = window.location.href;
                        }, 1000);
                        break;
                    case 'FAILED':
                        passbyme.stopPolling();
                        showError('Login failed because of an error!');
                        break;
                    case 'NO_DEVICE':
                        passbyme.stopPolling();
                        showError('The user has no PassBy[ME] ready device that supports messaging!');
                        break;
                    case 'DISABLED':
                        passbyme.stopPolling();
                        showError('The message could not be sent because the recipient is disabled.');
                        break;
                    case 'CANCELLED':
                        passbyme.stopPolling();
                        showError('The message was cancelled by the sender.');
                        break;
                    case 'DENIED':
                        passbyme.stopPolling();
                        showError('Login request cancelled.');
                        break;
                    case 'NOT_SEEN':
                    case 'NOT_DOWNLOADED':
                        passbyme.stopPolling();
                        showError('Login request timed out!');
                        break;
                    default:
                        passbyme.stopPolling();
                        showError('Unknown response error: ' + resp);
                        break;
                }
            },
            error: function (msg) {
                showError(msg);
            }
        });
    });
}(jQuery));
