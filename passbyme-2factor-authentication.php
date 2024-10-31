<?php

/*
Plugin Name: PassBy[ME] two-factor authentication
Plugin URI:  http://www.passbyme.com
Description: PassBy[ME] help you protect your WordPress account using two-factor authentication.
Version:     1.0.1
Author:      Microsec Ltd.
Author URI:  http://www.microsec.hu/en/
License:     GPLv2
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'src/Microsec/PassByME/Plugin/Core.php';

$core = new PassByME\Plugin\Core('1.0.1');
$core->run();