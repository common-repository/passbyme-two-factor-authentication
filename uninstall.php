<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

require_once plugin_dir_path( __FILE__ ) . 'src/Microsec/PassByME/Plugin/Loader.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Microsec/PassByME/Plugin/Admin/Functions.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Microsec/PassByME/TwoFactor/Config.php';

$loader = new PassByME\Plugin\Loader();
$loader->deleteAllOptions();

$users = get_users( array() );
foreach ( $users as $user ) {
    delete_user_meta($user->ID, 'pbm_2fa_authentication');
    delete_user_meta($user->ID, 'pbm_2fa_user_status');
    delete_user_meta($user->ID, 'pbm_2fa_user_oid');
}
