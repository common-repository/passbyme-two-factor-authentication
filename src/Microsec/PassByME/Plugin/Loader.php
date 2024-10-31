<?php
/**
 * @package passbyme-two-factor-authentication
 * @author  Microsec Ltd. <development@passbyme.com>
 * @copyright (c) 2017, Microsec Ltd.
 */

namespace PassByME\Plugin;

use PassByME\TwoFactor\Config;
use PassByME\Plugin\Admin\Functions;

class Loader {
	public $options;
	public $hooks;

	public function __construct() {
		$this->hooks   = array();
		$this->options = array();
		$this->registerOptions();
		$this->loadPBMConfig();
	}

	private function loadPBMConfig() {
		$functions = new Functions();
		Config::set( 'auth_cert', get_option( 'pbm_2fa_application_certificate' ) );
		Config::set( 'auth_pwd', $functions->aesDecrypt( get_option( 'pbm_2fa_application_pwd' ) ) );
		Config::set( 'auth_url', get_option( 'pbm_2fa_auth_url' ) );
		Config::set( 'mng_cert', get_option( 'pbm_2fa_management_certificate' ) );
		Config::set( 'mng_pwd', $functions->aesDecrypt( get_option( 'pbm_2fa_management_pwd' ) ) );
		Config::set( 'mng_url', get_option( 'pbm_2fa_mng_url' ) );
		Config::set( 'curl_proxy', get_option( 'pbm_2fa_proxy_url' ) );
		Config::set( 'curl_proxyport', get_option( 'pbm_2fa_proxy_port' ) );
		Config::set( 'curl_proxyuserpwd', get_option( 'pbm_2fa_proxy_pwd' ) );
	}

	private function registerOptions() {
		$this->options = array(
			'pbm_2fa_message_title',
			'pbm_2fa_message',
			'pbm_2fa_timeout',
			'pbm_2fa_auth_url',
			'pbm_2fa_mng_url',
			'pbm_2fa_proxy_url',
			'pbm_2fa_proxy_port',
			'pbm_2fa_proxy_pwd',
			'pbm_2fa_application_certificate',
			'pbm_2fa_application_pwd',
			'pbm_2fa_management_certificate',
			'pbm_2fa_management_pwd'
		);

		return $this->options;
	}

	public function addOptions( $options = array() ) {
		if ( ! empty( $options ) ) {
			foreach ( $options as $key => $value ) {
				if ( in_array( $key, $this->registerOptions() ) ) {
					if ( get_option( $key ) != false ) {
						update_option( $key, $value );
					} else {
						add_option( $key, $value );
					}
				} else {
					throw new \Exception( 'Unknown option name found: ' . $key );
				}
			}
		} else {
			throw new \Exception( 'Empty parameter given!' );
		}

		return true;
	}

	public function deleteAllOptions() {
		foreach ( $this->options as $option ) {
			delete_option( $option );
		}
	}

	private function registerHooks( $hook, $component, $callback, $priority, $args ) {
		$this->hooks[] = array(
			'hook'      => $hook,
			'component' => $component,
			'callback'  => $callback,
			'priority'  => $priority,
			'args'      => $args
		);

		return $this->hooks;
	}

	public function addAction( $hook, $component, $callback, $priority = 10, $args = 1 ) {
		$this->registerHooks( $hook, $component, $callback, $priority, $args );
	}

	public function load() {
		if ( is_array( $this->hooks ) ) {
			foreach ( $this->hooks as $hook ) {
				add_action(
					$hook['hook'],
					array( $hook['component'], $hook['callback'] ),
					$hook['priority'],
					$hook['args']
				);
			}
		}
	}
}