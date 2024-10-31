<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin\Pages;

class About {
	public static function content( $version ) {
		?>
        <table class="simple">
            <tbody>
            <tr>
                <td>Vendor:</td>
                <td><a href="https://www.microsec.hu/en/">Microsec Ltd.</a></td>
            </tr>
            <tr>
                <td>Version:</td>
                <td><?php print $version ?></td>
            </tr>
            <tr>
                <td>Contact information:</td>
                <td><a href="mailto:development@passbyme.com?Subject=PBM WordPress Plugin" target="_top">development@passbyme.com</a>
                </td>
            </tr>
            <tr>
                <td>Website:</td>
                <td><a href="https://www.passbyme.com">www.passbyme.com</a></td>
            </tr>
            </tbody>
        </table>
        <div class="clear"></div>
        <h4>Licence</h4>
        <p><a href="https://www.gnu.org/licenses/gpl-2.0.txt" target="_blank">GPLv2</a></p>
        <p>Copyright (c) 2017 Microsec Ltd.</p>
		<?php
	}
}