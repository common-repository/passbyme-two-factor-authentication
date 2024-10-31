<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin;

use PassByME\Plugin\Admin\Actions as AdminActions;
use PassByME\Plugin\Login\Actions as LoginActions;

class Core {
	public $loader;
	protected $version;

	public function __construct($version) {
		$this->version = $version;
		$this->requirements();

		$this->loader = new Loader();
		$this->adminHooks();
		$this->loginHooks();
	}

	private function requirements() {
		require_once __DIR__ . '/../Autoloader.php';
	}

	private function adminHooks() {
		$admin = new AdminActions( $this->version );
		$this->loader->addAction( 'admin_enqueue_scripts', $admin, 'enqueueStyles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $admin, 'enqueueScripts' );
		$this->loader->addAction( 'admin_post_add_app_pfx', $admin, 'uploadApplicationCert' );
		$this->loader->addAction( 'admin_post_change_app_pfx', $admin, 'removeConfig' );
		$this->loader->addAction( 'admin_post_users_2fa', $admin, 'users2FaStatus' );
		$this->loader->addAction( 'admin_post_pbm_general_settings', $admin, 'editSettings' );
		$this->loader->addAction( 'admin_post_set_mng_pfx', $admin, 'uploadManagementCert' );
		$this->loader->addAction( 'admin_post_check_account', $admin, 'checkAccount' );
		$this->loader->addAction( 'admin_menu', $admin, 'renderMenu' );
		$this->loader->addAction( 'upload_mimes', $admin, 'setCertMimeType' );
		$this->loader->addAction( 'user_register', $admin, 'enable2FaToNewAdmins' );
	}

	private function loginHooks() {
		$login = new LoginActions( $this->version );
		$this->loader->addAction( 'login_enqueue_scripts', $login, 'enqueueStyles' );
		$this->loader->addAction( 'wp_authenticate', $login, 'authenticateUser', 99999, 2 );
		$this->loader->addAction( 'init', $login, 'is2FaApproved' );
		$this->loader->addAction( 'login_footer', $login, 'twoFaScreen' );
		$this->loader->addAction( 'wp_logout', $login, 'logout' );
		$this->loader->addAction( 'wp_ajax_nopriv_pbm_polling', $login, 'polling2FaAuthenticationCallback' );
	}

	public function run() {
		$this->loader->load();
	}
}