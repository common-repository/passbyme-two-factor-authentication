<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin\Admin;

use PassByME\Plugin\Admin\Pages\About;
use PassByME\Plugin\Admin\Pages\Account;
use PassByME\Plugin\Admin\Pages\General;
use PassByME\Plugin\Admin\Pages\Settings;

class Pager {
	private $version;

	/**
	 * Pager constructor.
	 *
	 * @param $version
	 */
	public function __construct( $version ) {
		$this->version   = $version;
		$this->functions = new Functions();
	}

	public function renderPage( $pageIcon, $pageTitle, $pageContent ) {
		$appCert = $this->functions->isCertSet( 'pbm_2fa_application_certificate' );
		$mngCert = $this->functions->isCertSet( 'pbm_2fa_management_certificate' );
		if ( $appCert ) {
			if ( $mngCert ) {
				//Is configured
				$icon    = $pageIcon;
				$title   = $pageTitle;
				$content = $pageContent;
			} else {
				$icon    = 'glyphicon-wrench';
				$title   = 'Connect WordPress with your PassBy[ME] account';
				$content = function () {
					General::configMng();
				};
			}
		} else {
			$icon    = 'glyphicon-wrench';
			$title   = 'Connect WordPress with your PassBy[ME] account';
			$content = function () {
				General::configAuth();
			};
		}
		$this->functions->createHTML( $icon, $title, $content );
	}

	public function GeneralPage() {
		$this->renderPage( 'glyphicon-user', 'General', function () {
			General::content();
		} );
	}

	public function SettingsPage() {
		$this->renderPage( 'glyphicon-cog', 'Settings', function () {
			Settings::content();
		} );
	}

	public function AccountPage() {
		$this->renderPage( 'glyphicon-th', 'Account', function () {
			Account::content( $this->functions->isCertSet( 'pbm_2fa_application_certificate' ) );
		} );
	}

	public function AboutPage() {
		$this->renderPage( 'glyphicon-info-sign', 'About', function () {
			About::content( $this->version );
		} );
	}
}