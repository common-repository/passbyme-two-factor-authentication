<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin\Pages;

class General {
	public static function configAuth() {
		?>
        <h4>Enter to your PassBy[ME] account</h4>
        <div class="row">
            <div class="col-md-3">
                <a target="_blank" href="https://admin.passbyme.com/register/login">
                    <div class="box">
                        <span class="glyphicon glyphicon-user"></span>
                        <div class="box-text">Login</div>
                    </div>
                </a>
            </div>
            <div class="col-md-1 text-center">
                OR
            </div>
            <div class="col-md-3">
                <a target="_blank" href="https://admin.passbyme.com/register/registration">
                    <div class="box">
                        <span class="glyphicon glyphicon-plus"></span>
                        <div class="box-text">Register new organization</div>
                    </div>
                </a>
            </div>
        </div>
        <br>
        <h4>Set your application certificate file</h4>
        <ol class="pfx-config-steps">
            <li>Create a new application for your WordPress plugin at "<b>Applications</b>"
                > "<b>New application</b>".
            </li>
            <li>On the list of applications click on your newly created
                application <span class="glyphicon glyphicon-lock glyphicon-in-text"></span> icon.
            </li>
            <li>
                Download the application certificate file in PEM format by hitting the "<b>Download PEM</b>" button
                box.<br><i>Note that the certificate file is protected with a password, which you can get by clicking on
                    the "<b>Show password</b>" button box on the certificate download window.</i>
            </li>
            <li>
                Upload your application certificate file to allow authentication between the PassBy[ME] service and
                your
                plugin.
            </li>
        </ol>
        <form id="pbm_settings_form">
			<?php wp_nonce_field( 'ajax_file_nonce', 'security' ); ?>
            <table class="pbm-table">
                <tr>
                    <th>Certificate file:</th>
                    <td><input type="file" name="pbm_app_pfx" id="pbm_app_pfx"/>
                    </td>
                </tr>
                <tr>
                    <th>Certificate password:</th>
                    <td>
                        <input title="Application certificate" type="password" value="" name="pbm_app_pfx_pwd"
                               id="pbm_app_pfx_pwd">
                    </td>
                </tr>
            </table>
            <hr>
            <div class="response-container">
                <div class="response-message"></div>
            </div>
            <input type="submit" id="save-settings" value="Upload" class="button button-primary button-large">
        </form>
        <script type="text/javascript"
                src="<?php print plugins_url( 'passbyme-two-factor-authentication/static/js/config.js' ); ?>"></script>
		<?php
	}

	public static function configMng() {
		?>
        <h4>Set your management certificate file to access PassBy[ME] management functions</h4>
        <ol class="pfx-config-steps">
            <li>In your PassBy[ME] administration page go to "<b>Account Settings</b>" click on the "<b>Key Details</b>"
                box.
            </li>
            <li>
                Download the management certificate file in PEM format by hitting the "<b>Download PEM</b>"
                button box.<br><i>Note that the certificate file is protected with a password, which you can copy from the
                    download page as before.</i>
            </li>
            <li>
                Upload the downloaded certificate file:
            </li>
        </ol>
        <form id="pbm_mng_form">
			<?php wp_nonce_field( 'ajax_file_nonce', 'security' ); ?>
            <table class="pbm-table">
                <tr>
                    <th>Certificate file:</th>
                    <td><input type="file" name="pbm_mng_pfx"></td>
                </tr>
                <tr>
                    <th>Certificate Password:</th>
                    <td><input title="Certificate Password" type="password" name="pbm_mng_pwd" value=""></td>
                </tr>
            </table>
            <div class="response-container">
                <div class="response-message"></div>
            </div>
            <hr>
            <input class="button button-primary button-large" type="submit"
                   value="Finish">
        </form>
        <div id="dialog" class="collapse" title="Congratulation!">
            <p>Your PassBy[ME] plugin configuration is complete.</p>
            <p>Happy Authenticating!</p>
        </div>
        <script type="text/javascript"
                src="<?php print plugins_url( 'passbyme-two-factor-authentication/static/js/config.js' ); ?>"></script>
		<?php
	}

	public static function content() {
		?>
        <form id="user_settings_form">
            <p>The following table contains all your WordPress users and their 2FA authentication configuration.</p>
            <div class="response-container"></div>
            <table class="table table-striped table-bordered dataTable no-footer" id="users-table" cellspacing="0"
                   width="100%">
                <thead>
                <tr>
                    <th>Login Name</th>
                    <th>Email</th>
                    <th>Display Name</th>
                    <th>Role</th>
                    <th class="text-center">2FA Status</th>
                    <th class="text-center">Change State</th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ( get_users() as $wp_user ) { ?>
                    <tr>
                        <td><?php print $wp_user->data->user_login; ?></td>
                        <td><?php print $wp_user->data->user_email; ?></td>
                        <td><?php print $wp_user->data->display_name; ?></td>
                        <td><?php print $wp_user->roles[0]; ?></td>
                        <td class="text-center">
                            <span class="glyphicon"></span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn pbm_2fa_button"
                                    data-login="<?php print $wp_user->data->display_name; ?>"
                                    data-email="<?php print $wp_user->data->user_email; ?>"
                                    data-userid="<?php print $wp_user->data->ID; ?>"
                                    data-toggle="tooltip"
                                    data-placement="left"></button>
                            <div class="loading-img"></div>
                        </td>
                    </tr>
				<?php } ?>
                </tbody>
            </table>
            <div class="pbm-account-warning">
                <div class="alert alert-warning" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    <span>According to your <b><span id="pbm-pricing-value"></span></b> subscription you allowed to set 2FA authentication up to
                        <b><span id="pbm-account-number-of-users"></span></b> users.
                    </span>
                </div>
            </div>
            <hr>
            <p><input class="button button-primary button-large" type="button" id="user_settings_refresh"
                      value="Refresh List"></p>
            <p><i> Refreshing list automatically every <span id="auto_refresh_timer"></span> sec.</i></p>
        </form>
        <div id="dialog" class="collapse" title="Enrollment is in progress...">
            <span tabindex="1"></span>
            <p>User has an active enrollment already. If you want to re-send an enrollment email please delete all
                active enrollments of this user in the
                <a target="_blank" href="https://admin.passbyme.com/register">administration interface</a>.
            </p>
        </div>
        <script type="text/javascript"
                src="<?php print plugins_url( 'passbyme-two-factor-authentication/static/js/general.js' ); ?>"></script>
		<?php
	}
}