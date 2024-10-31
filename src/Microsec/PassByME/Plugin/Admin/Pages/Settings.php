<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin\Pages;

class Settings {

	public static function content() {
		?>
        <form id="general_settings_form">
            <div class="settings-block">
                <div class="textarea-wrap">
                    <h4>Authentication Message</h4>
                    <p>In this section you can edit authentication message content that PassBy[ME] send to
                        users mobile device on login attempts.</p>
                    <table class="pbm-table" width="100%">
                        <tr>
                            <td>
                                <label for="pbm_message_title" class="input-label">Title</label>
                                <input id="pbm_message_title" class="pbm-long-input" type="text"
                                       name="pbm_message_title"
                                       value="<?php print get_option( 'pbm_2fa_message_title' ); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="label_pbm_message" class="input-label">Body</label>
                                <textarea id="label_pbm_message" rows="3" cols="15"
                                          maxlength="4094"><?php print get_option( 'pbm_2fa_message' ); ?></textarea>
                                <span class="pbm-text-italic" id="chars"></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <p>
                    You can use the following placeholders to include WordPress user data to PassBy[ME] message body:
                </p>
                <table class="table-striped table-bordered pbm-table">
                    <tr>
                        <th>Placeholder</th>
                        <th>Explanation</th>
                    </tr>
                    <tr>
                        <td>{USER_NAME}</td>
                        <td>The user login name.</td>
                    </tr>
                    <tr>
                        <td>{EMAIL}</td>
                        <td>The user email address.</td>
                    </tr>
                    <tr>
                        <td>{DISPLAY_NAME}</td>
                        <td>The user full name.</td>
                    </tr>
                </table>
            </div>
            <div class="settings-block">
                <h4>Timeout</h4>
                <p>The authentication request timeout in seconds between 10 and 120.</p>
                <div>
                    <input title="Timeout" type="number" min="10" max="120" name="pbm_timeout" id="timeout"
                           value="<?php print get_option( 'pbm_2fa_timeout' ); ?>"/>
                </div>
                <input type="hidden" id="pbm_message" name="pbm_message"/>
            </div>
            <div class="settings-block">
                <a id="expand-advanced-settings" class="pointer">Show Advanced Settings...</a>
                <div id="advanced-settings" class="collapse">
                    <h4>PassBy[ME] Connection</h4>
                    <div>
                        <table class="pbm-table">
                            <tr>
                                <td><label for="pbm_2fa_mng_api_service_url">Management API URL</label></td>
                                <td><input placeholder="https://api.passbyme.com/register" class="pbm-long-input"
                                           id="pbm_2fa_mng_api_service_url" name="pbm_mng_url"
                                           type="text" value="<?php print get_option( 'pbm_2fa_mng_url' ); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="pbm_2fa_auth_api_service_url">Authentication API URL</label></td>
                                <td><input placeholder="https://auth-sp.passbyme.com/frontend" class="pbm-long-input"
                                           id="pbm_2fa_auth_api_service_url" name="pbm_auth_url"
                                           type="text" value="<?php print get_option( 'pbm_2fa_auth_url' ); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="pbm_2fa_proxy">Proxy URL</label></td>
                                <td><input placeholder="proxy.example.com" class="pbm-long-input" id="pbm_2fa_proxy"
                                           name="pbm_proxy_url"
                                           type="text"
                                           value="<?php print get_option( 'pbm_2fa_proxy_url' ); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="pbm_2fa_proxy_port">Proxy Port</label></td>
                                <td><input placeholder="8080" id="pbm_2fa_proxy_port" name="pbm_proxy_port" type="text"
                                           value="<?php print get_option( 'pbm_2fa_proxy_port' ); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="pbm_2fa_proxy_pwd">Proxy password</label></td>
                                <td><input placeholder="********" id="pbm_2fa_proxy_pwd" name="pbm_proxy_pwd"
                                           type="password"
                                           value="<?php print get_option( 'pbm_2fa_proxy_pwd' ); ?>"></td>
                            </tr>
                        </table>
                    </div>
                    <a class="pointer" id="close-advanced-settings">Close Advanced Settings...</a>
                </div>
            </div>
            <hr>
            <div class="response-container">
                <div class="response-message"></div>
            </div>
            <input class="button button-primary button-large" type="button" id="general_settings_submit"
                   value="Save Changes">
        </form>
        <script type="text/javascript"
                src="<?php print plugins_url( 'passbyme-two-factor-authentication/static/js/settings.js' ); ?>"></script>
		<?php
	}
}