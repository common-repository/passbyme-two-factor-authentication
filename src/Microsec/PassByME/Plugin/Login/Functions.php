<?php
/**
 * @package passbyme-two-factor-authentication
 * @author Teszár Balázs tbalazs@microsec.hu
 * @since 2016.09.29.
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Login;

use PassByME\Methods\Messaging;

class Functions {
	public function isWpUser( $username, $password ) {
		$userObj = wp_authenticate( $username, $password );
		if ( $userObj instanceof \WP_User ) {
			return $userObj;
		} else {
			return null;
		}
	}

	public function start2Fa( $userObj ) {
		$_SESSION['pbm_user'] = $userObj->user_email;
		// Set placeholders
		$msg                        = str_replace(
			array( '{USER_NAME}', '{EMAIL}', '{DISPLAY_NAME}' ),
			array( $userObj->user_login, $userObj->user_email, $userObj->display_name ),
			get_option( 'pbm_2fa_message' )
		);
		$_SESSION['pbm_device_msg'] = $msg;
		$_SESSION['pbm_timeout']    = get_option( 'pbm_2fa_timeout' );
		add_filter( 'wp_authenticate_user', array( Actions::class, 'enqueueLoginScripts' ), 99999 );
	}

	public function doLogin( $userObj ) {
		$_SESSION['wp_user_obj'] = $userObj;
		if ( get_user_meta( $userObj->ID, 'pbm_2fa_authentication' )[0] !== '1' or ! get_option( 'pbm_2fa_application_certificate' ) ) {
			$this->login( $userObj );
		} else {
			$this->start2Fa( $userObj );
		}
	}

	private function logout() {
		session_start();
		$_SESSION = array();
		session_destroy();
	}

	public function login( $wpUserObj ) {
		wp_set_current_user( $wpUserObj->ID, $wpUserObj->user_login );
		wp_set_auth_cookie( $wpUserObj->ID, true );
		do_action( 'wp_login', $wpUserObj->user_login, $wpUserObj );

		$current_role = array_shift( $wpUserObj->roles );
		if ( $current_role == 'administrator' ) {
			wp_redirect( admin_url() );
		} else {
			wp_redirect( home_url() );
		}
		$this->logout();
		//From wp documentation: wp_redirect() does not exit automatically and should almost always be followed by exit.
		exit;
	}

	public function sendPBM2FaMessage( $userId, $msg, $timeout ) {
		$pbm = new Messaging();
		if ( $userId ) {
			$json = $pbm->authorizationMessage( array( $userId ), 'WP login request', $msg, $timeout );
		} else {
			throw new \Exception( "Missing PassBy[ME] user identifier!" );
		}

		return $json;
	}

	public function trackPBM2FaMessage( $userId, $messageId ) {
		$pbm = new Messaging();
		if ( $messageId ) {
			$json = $pbm->trackMessage( $messageId );
			if ( is_array( $json->recipients ) ) {
				foreach ( $json->recipients as $recipient ) {
					if ( $recipient->userId == $userId ) {
						$_SESSION['pbm_login_status'] = $recipient->status;
						$json                         = $recipient->status;
						break;
					}
				}
			} else {
				throw new \Exception( "Unexpected PassBy[ME] response!" );
			}
		} else {
			throw new \Exception( "Missing PassBy[ME] message identifier!" );
		}

		return $json;
	}
}