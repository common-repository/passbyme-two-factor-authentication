(function ($) {
    $(document).ready(function () {
        function showLoading(checkbox) {
            checkbox.hide();
            checkbox.closest('td').find('.loading-img').show();
        }

        function hideLoading(checkbox) {
            checkbox.show();
            checkbox.closest('td').find('.loading-img').hide();
        }

        function checkAccount() {
            $.ajaxCall({
                action: 'check_account',
                success: function (response) {
                    response = response.data;
                    if (response.organisation.hasOwnProperty('pricing') && response.account.hasOwnProperty('maxNumberOfUsers')) {
                        var pricing = response.organisation.pricing;
                        var maxNumberOfUsers = response.account.maxNumberOfUsers;
                        switch (pricing) {
                            case 'trial':
                            case 'free':
                                $('.pbm-account-warning').show();
                                $('#pbm-pricing-value').html(pricing);
                                $('#pbm-account-number-of-users').html(maxNumberOfUsers);
                                break;
                            default:
                                break;
                        }
                    } else {
                        $.errorReport('Invalid response when trying to check account information!');
                    }
                },
                error: function (message) {
                    $.errorReport(message.error);
                }
            });
        }

        function removeClass(element, regexp) {
            element.removeClass (function (index, css) {
                return (css.match(regexp) || []).join(' ');
            });
        }

        function setStatus(response) {
            $.each(response.data, function (index, obj) {
                var statusButton = $('button[data-userid="' + obj.id + '"]'),
                    statusIcon = statusButton.closest('td').prev('td').find('.glyphicon');
                removeClass(statusButton, /\bbtn-\S+/g);
                removeClass(statusIcon, /\bglyphicon-\S+/g);
                if (obj.hasOwnProperty('status')) {
                    statusButton.attr("data-status", obj.status);
                    switch (obj.status) {
                        case 'active':
                            statusButton.toggleClass("btn-danger", true);
                            statusButton.text('Disable');
                            statusIcon.toggleClass("glyphicon-ok-circle", true);
                            statusButton.attr("title", "Disable 2FA authentication.");
                            break;
                        case 'no_device':
                            statusButton.toggleClass("btn-warning", true);
                            statusButton.text('Re-Enroll');
                            statusIcon.toggleClass("glyphicon-phone", true);
                            statusButton.attr('title', "User has no enrolled device. Send a new enrollment by clicking on the \"Re-Enroll\" button.");
                            break;
                        case 'no_user':
                            statusButton.toggleClass("btn-success", true);
                            statusButton.text('Add User');
                            statusIcon.toggleClass("glyphicon-user", true);
                            statusButton.attr("title", "Create user in the PassBy[ME] system.");
                            break;
                        case 'inactive':
                            statusButton.text('Enable');
                            statusButton.toggleClass("btn-success", true);
                            statusIcon.toggleClass("glyphicon-ban-circle", true);
                            statusButton.attr("title", "Enable 2FA authentication. Affect users next login attempt.");
                            break;
                        case 'enrolling':
                            statusButton.toggleClass("btn-warning", true);
                            statusButton.text('Enrolling...');
                            statusIcon.toggleClass("glyphicon-envelope", true);
                            statusButton.attr("title", "Waiting for user to use then enrollment sheet sent via email.");
                            break;
                        default:
                            $.errorReport('Unknown response!');
                            return false;
                    }
                } else {
                    $.errorReport('Unexpected response while changing user\'s status information!');
                    return false;
                }
            });
        }

        function setStatusQuery(button, extraParam) {
            var objArray = {};
            button.each(function (i) {
                objArray[i] = {
                    'id': $(this).attr('data-userid'),
                    'user_nicename': $(this).attr('data-login'),
                    'user_email': $(this).attr('data-email')
                };
            });

            var query = $('#user_settings_form').serializeArray();
            if (!$.isEmptyObject(objArray)) {
                query.push({name: "user_to_2fa", value: JSON.stringify(objArray)});
                if (typeof extraParam === 'object') {
                    query.push(extraParam);
                }
            }
            return query;
        }

        function refreshStatusAuto() {
            var checkbox = $('.pbm_2fa_button');
            $.ajaxCall({
                action: 'users_2fa',
                data: setStatusQuery(checkbox, {
                    name: "reload",
                    value: true
                }),
                success: function (response) {
                    setStatus(response);
                },
                error: function (response) {
                    $.errorReport(response.error);
                }
            });
        }

        function refreshStatusPaging() {
            var ctaButtons = $('.pbm_2fa_button');
            $.ajaxCall({
                action: 'users_2fa',
                data: setStatusQuery(ctaButtons, {
                    name: "reload",
                    value: true
                }),
                beforeSend: function () {
                    showLoading(ctaButtons);
                },
                success: function (response) {
                    setStatus(response);
                },
                error: function (response) {
                    $.errorReport(response.error);
                },
                complete: function () {
                    hideLoading(ctaButtons);
                }
            });
        }

        function changeUserStatus(ctaButtons) {
            $.ajaxCall({
                action: 'users_2fa',
                data: setStatusQuery(ctaButtons, ''),
                beforeSend: function () {
                    showLoading(ctaButtons);
                },
                success: function (response) {
                    setStatus(response);
                    var status = response.data[0].status;
                    switch (status) {
                        case "enrolling":
                            $.successReport("Enrollment email is sent out to user's email address.");
                            break;
                        case "inactive":
                            $.warningReport("User's 2FA authentication is <b>DISABLED</b>!");
                            break;
                        case "active":
                            $.infoReport("User's 2FA authentication is <b>ENABLED</b>!");
                            break;
                        default:
                            break;
                    }
                    if (response.data[0].status === "enrolling") {

                    }
                },
                error: function (response) {
                    $.errorReport(response.error);
                },
                complete: function () {
                    hideLoading(ctaButtons);
                }
            });
        }

        function autoRefresh(ms) {
            var sec = (parseInt(ms) / 1000);
            var autoRefreshTimer = $("#auto_refresh_timer");
            autoRefreshTimer.text(sec);
            var refresh = setInterval(function() {
                --sec;
                if (sec < 0) {
                    clearInterval(refresh);
                    refreshStatusAuto();
                    autoRefresh(ms);
                }
            }, 1000);
        }

        // ---------------------------------------------------------
        // ACTIONS
        // ---------------------------------------------------------
        autoRefresh(15000);
        checkAccount();
        //Icons tooltip.
        $('[data-toggle="tooltip"]').tooltip();

        $('.pbm_2fa_button').on('click', function () {
            if ($(this).attr("data-status") === "enrolling") {
                $("#dialog").dialog({
                    resizable: false,
                    modal: true,
                    width: 400,
                    close: function () {
                        $(this).dialog("close");
                    }
                });
            } else {
                changeUserStatus($(this));
            }
        });

        $('#user_settings_refresh').on('click', function () {
            window.location.reload();
        });

        $('#users-table').DataTable({
            "columnDefs": [
                {"orderable": false, "targets": 4},
                {"orderable": false, "targets": 5}
            ],
            "fnDrawCallback": function () {
                refreshStatusPaging()
            }
        });
    });
}(jQuery));
