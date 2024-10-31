<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin;

use PassByME\Plugin\Loader;
use PassByME\TwoFactor\PBMErrorException;

class Actions {
	/**
	 * Plugin Version number.
	 * @var
	 */
	private $version;
	/**
	 * Main page identifier.
	 * @var string
	 */
	private $mainPageId;
	/**
	 * Only the ones with the proper capability can access to plugin menu.
	 * @var string
	 */
	private $capability;

	/**
	 * @var Pager
	 */
	private $pages;

	public function __construct( $version ) {
		$this->version    = $version;
		$this->mainPageId = 'pbm-config-page';
		// Allows access to Administration Panel options
		$this->capability = 'manage_options';
		$this->pages      = new Pager( $this->version );
		$this->functions  = new Functions();
	}

	public function enqueueStyles() {
		wp_enqueue_style(
			'pbm-external-bs3-css',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/bootstrap/dist/css/bootstrap.min.css' ),
			array(),
			$this->version
		);
		wp_enqueue_style(
			'pbm-external-jquery-ui-css',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/jquery-ui/themes/base/jquery-ui.min.css' ),
			array(),
			$this->version
		);
		wp_enqueue_style(
			'pbm-external-datatables-css',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/datatables/media/css/dataTables.jqueryui.min.css' ),
			array(),
			$this->version
		);
		wp_enqueue_style(
			'pbm-admin-css',
			plugins_url( 'passbyme-two-factor-authentication/static/css/admin.css' ),
			array(),
			$this->version
		);
	}

	private function registerScripts() {
		wp_register_script(
			'pbm-admin-bs3-js',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/bootstrap/dist/js/bootstrap.min.js' ),
			array(),
			$this->version
		);
		wp_register_script(
			'pbm-admin-table-js',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/datatables/media/js/jquery.dataTables.min.js' ),
			array(),
			$this->version
		);
		wp_register_script(
			'pbm-admin-jquery-form',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/jquery-form/jquery.form.js' ),
			array(),
			$this->version
		);
		wp_register_script(
			'pbm-admin-jquery-ui',
			plugins_url( 'passbyme-two-factor-authentication/static/bower_components/jquery-ui/jquery-ui.min.js' ),
			array(),
			$this->version
		);
	}

	public function enqueueScripts() {
		$this->registerScripts();
		wp_enqueue_script(
			'pbm-core-js',
			plugins_url( 'passbyme-two-factor-authentication/static/js/core.js' ),
			array(
				'pbm-admin-bs3-js',
				'jquery',
				'pbm-admin-jquery-form',
				'pbm-admin-jquery-ui',
				'pbm-admin-table-js',
			)
		);
	}

	public function renderMenu() {
		$isConfigured = false;
		if ( get_option( 'pbm_2fa_management_certificate' ) !== false and get_option( 'pbm_2fa_application_certificate' ) !== false ) {
			$isConfigured = true;
		}
		$this->functions->setMainMenu( 'Settings - PBM', 'PassBy[ME]', $this->mainPageId, $this->capability, function () {
			$this->pages->GeneralPage();
		} )->setSubMenu( $this->mainPageId, $this->capability,
			array(
				array(
					'title'      => 'General Settings - PBM',
					'name'       => 'General',
					'pageId'     => $this->mainPageId,
					'visibility' => true
				),
				array(
					'title'      => 'Settings - PBM',
					'name'       => 'Settings',
					'pageId'     => 'pbm-settings',
					'callback'   => function () {
						$this->pages->SettingsPage();
					},
					'visibility' => $isConfigured
				),
				array(
					'title'      => 'Account information - PBM',
					'name'       => 'Account',
					'pageId'     => 'pbm-account-info',
					'callback'   => function () {
						$this->pages->AccountPage();
					},
					'visibility' => $isConfigured
				),
				array(
					'title'      => 'About - PBM',
					'name'       => 'About',
					'pageId'     => 'pbm-about',
					'callback'   => function () {
						$this->pages->AboutPage();
					},
					'visibility' => $isConfigured
				)
			)
		);
	}

	public function setCertMimeType( $existing_mimes = array() ) {
		$existing_mimes['extension'] = 'mime/type';
		$existing_mimes['pem']       = 'application/x-pem-file';

		// and return the new full result
		return $existing_mimes;
	}

	public function uploadApplicationCert() {
		try {
			$this->functions->uploadCertificate( 'pbm_app_pfx', 'pbm_app_pfx_pwd', 'app' );
			$response['success'] = 'Application certificate is valid, and was successfully uploaded.';
		} catch ( \Exception $ex ) {
			$response['error'] = $ex->getMessage();
		}
		print json_encode( $response );
		die();
	}

	public function uploadManagementCert() {
		try {
			$this->functions->uploadCertificate( 'pbm_mng_pfx', 'pbm_mng_pwd', 'mng' );
			$response['success'] = 'Management certificate is valid, and was successfully uploaded.';
		} catch ( \Exception $ex ) {
			$response['error'] = $ex->getMessage();
		}
		print json_encode( $response );
		die();
	}

	public function removeConfig() {
		$response    = array();
		$application = get_option( 'pbm_2fa_application_certificate' );
		$management  = get_option( 'pbm_2fa_management_certificate' );
		if ( $application !== false and $management !== false ) {
			unlink( $application );
			unlink( $management );
			$users = get_users( array() );
			foreach ( $users as $user ) {
				delete_user_meta( $user->ID, 'pbm_2fa_authentication' );
				delete_user_meta( $user->ID, 'pbm_2fa_user_status' );
			}
			$loader = new Loader();
			$loader->deleteAllOptions();
			$response['data'] = 'OK';
		} else {
			$response['error'] = 'The settings you want to change are not available anymore.';
		}
		print json_encode( $response );
		die(); //request handlers should die() when they complete their task
	}

	public function enable2FaToNewAdmins( $user_id ) {
		$user = get_users( array( 'search' => $user_id, 'role' => 'administrator' ) );
		if ( isset( $user[0] ) ) {
			$this->functions->addUserToPBM( $user[0] );
		}
	}

	public function users2FaStatus() {
		try {
			$response = array();
			$list     = filter_input( INPUT_POST, 'user_to_2fa' );
			$reload   = filter_input( INPUT_POST, 'reload', FILTER_VALIDATE_BOOLEAN );
			if ( $list ) {
				$response = $this->functions->setUsersStatus( $list, $reload );
			} else {
				//Stay silent
			}
		} catch ( \Exception $ex ) {
			$response['error'] = $ex->getMessage();
		}
		print json_encode( $response );
		die(); //wp specific: request handlers should die() when they complete their task
	}

	public function editSettings() {
		try {
			$response = array();
			if ( ! current_user_can( 'manage_options' ) ) {
				throw new \Exception( 'You do not have sufficient permissions to access this page' );
			} else {
				$rules       = $this->functions->inputRules();
				$validation  = $this->functions->getValidationRules( $rules );
				$inputs      = filter_input_array( INPUT_POST, $validation );
				$validInputs = $this->functions->validateInputs( $inputs, $rules );
				$this->functions->testConnection( $inputs );
				foreach ( $validInputs as $key => $value ) {
					update_option( $key, $value );
				}
				$response['data'] = 'Settings saved!';
			}
		} catch ( \Exception $ex ) {
			if ( $ex->getCode() == 0 ) {
				$error = 'Failed to save settings! Connection to PassBy[ME] service is not possible with the given configuration!';
			} else {
				$error = $ex->getMessage();
			}
			$response['error'] = $error;
		}
		print json_encode( $response );
		die(); //request handlers should die() when they complete their task
	}

	public function checkAccount() {
		try {
			$json['data'] = array(
				'account'      => $this->functions->getAccountData(),
				'organisation' => $this->functions->getOrganisationData()
			);
		} catch ( PBMErrorException $ex ) {
			if ( $ex->getCode() == '0' ) {
				$error = '<strong>Host not found!</strong> Check <i>advanced settings</i> section in <a href="../../../../../../../../wp-admin/admin.php?page=pbm-settings">Settings</a> menu to setup connection correctly!';
			} else {
				$error = 'PassBy[ME] error occurred: ' . $ex->getMessage();
			}
			$json['error'] = $error;
		} catch ( \Exception $ex ) {
			$json['error'] = $ex->getMessage();
		}
		print json_encode( $json );
	}
}