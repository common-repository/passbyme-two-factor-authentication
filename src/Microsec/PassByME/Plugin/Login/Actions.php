<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Login;

class Actions {
	private static $version;

	public function __construct( $version ) {
		self::$version   = $version;
		$this->functions = new Functions();
		session_start();
	}

	public function enqueueStyles() {
		wp_enqueue_style(
			'pbm-login-css',
			plugins_url( 'passbyme-two-factor-authentication/static/css/login.css' ),
			array(),
			self::$version
		);
	}

	public function authenticateUser( $username, $password ) {
		if ( isset( $username ) and isset( $password ) ) {
			$user = $this->functions->isWpUser( $username, $password );
			if ( $user ) {
				$this->functions->doLogin( $user );
			} else {
				//If basic authentication failed then default wp error handling
				return;
			}
		} else {
			//If missing input then default wp error handling
			return;
		}
	}

	public function enqueueLoginScripts() {
		wp_enqueue_script(
			'pbm-poll-js',
			plugins_url( 'passbyme-two-factor-authentication/static/js/login.js' ),
			array( 'jquery' ),
			self::$version
		);
		wp_enqueue_script(
			'pbm-2fa-js',
			plugins_url( 'passbyme-two-factor-authentication/static/js/pbm2FA.js' ),
			array( 'jquery' ),
			self::$version
		);
		wp_localize_script(
			'pbm-poll-js',
			'ajax_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
		);
	}

	public function polling2FaAuthenticationCallback() {
		$userId  = filter_var( $_SESSION['pbm_user'], FILTER_VALIDATE_EMAIL );
		$msg     = filter_var( $_SESSION['pbm_device_msg'], FILTER_SANITIZE_STRING );
		$timeout = filter_var( $_SESSION['pbm_timeout'], FILTER_VALIDATE_INT );

		$messageId = filter_input( INPUT_GET, 'messageId', FILTER_SANITIZE_ENCODED );
		$polling   = filter_input( INPUT_GET, 'polling', FILTER_VALIDATE_BOOLEAN );

		try {
			if ( $polling ) {
				$json = $this->functions->trackPBM2FaMessage( $userId, $messageId );
			} else {
				$json = $this->functions->sendPBM2FaMessage( $userId, $msg, $timeout );
			}
		} catch ( \Exception $exc ) {
			$json = array( 'errormsg' => $exc->getMessage() );
		}

		//JSON return
		echo json_encode( $json );
		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function is2FaApproved() {
		if ( $_SESSION['wp_user_obj'] instanceof \WP_User and $_SESSION['pbm_login_status'] == 'APPROVED' ) {
			$this->functions->login( $_SESSION['wp_user_obj'] );
		}
	}

	public function twoFaScreen() {
		?>
        <div id="pbm-auth-request">
            <form id="pbm-form">
                <a href="https://passbyme.com/" target="_blank" title="PassBy[ME]" tabindex="-1">
                    <div class='pbm-cubic-pulse'>
                        <div class="cube">
                            <div class="align right bottom">P&nbsp;A</div>
                        </div>
                        <div class="cube">
                            <div class="align left bottom">S&nbsp;S</div>
                        </div>
                        <div class="cube">
                            <div class="align top right">B&nbsp;Y</div>
                        </div>
                        <div class="cube">
                            <div class="align top left">
                                <span class="bracket top">[</span><strong>ME</strong><span class="bracket">]</span>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="pbm-form-text">
                    <h3>Waiting for your approval...</h3>
                    <p class="tr-id">Transaction ID: <span id="pbm-identifier"></span></p>
                </div>
            </form>
            <p id="backtologin"><a href="../../../../../../../../wp-login.php">← Back to login page</a></p>
        </div>
		<?php
	}
}