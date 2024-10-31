<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin\Pages;

class Account {
	public static function content( $pfx ) {
		$pfxData = openssl_x509_parse( file_get_contents( $pfx ) );
		?>
        <div class="pbm-panel">
            <h4>Organization details:</h4>
            <table class="simple">
                <tr>
                    <td>Serial Number:</td>
                    <td data-name="organizationId"></td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td data-name="name"></td>
                </tr>
                <tr>
                    <td>Subscription type:</td>
                    <td>
                        <span data-name="pricing"></span>
                        <span class="upgrade-button-container">
                            <a class="upgrade-subscription-button" target="_blank"
                               href="https://admin.passbyme.com/register/protected/index?widget=orgdetails">
                                <button class="button button-primary">Upgrade</button>
                            </a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Contact email:</td>
                    <td>
                        <a href="#" data-name="email"></a>
                    </td>
                </tr>
            </table>
            <h4>Application details:</h4>
            <table class="simple">
                <tr>
                    <td>Serial number:</td>
                    <td><?php print $pfxData['subject']['serialNumber']; ?></td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td><?php print $pfxData['subject']['CN']; ?></td>
                </tr>
            </table>
            <div class="response-container"></div>
            <hr>
            <input class="button button-primary button-large" id="change_application" type="button"
                   value="Change account">
        </div>
        <div id="dialog" class="collapse" title="Attention!">
            <p>You are about to remove your account configuration.</p>
            <p>Are you sure you want to continue?</p>
        </div>
        <script type="text/javascript"
                src="<?php print plugins_url( 'passbyme-two-factor-authentication/static/js/account.js' ); ?>"></script>
		<?php
	}
}