<?php
/**
 * Plugin Name: 360 tour plugin
 * Description: Custom plugin to add support for 360 tour service (provided by http://360.cr/) implemented on hotelarenalkioro.com
 * Version: 0.1
 * Author: Hans Doller
 * Author URI: http://ticonerd.com
 * License: GPLv3
 */

defined("T360_BASE_DIR") ||
	define("T360_BASE_DIR", dirname(__FILE__));

// this should be the only relative path
require_once T360_BASE_DIR . '/lib/functions.php';

// all other requires should use the `t360_path_lib` helper
require_once t360_path_lib('wp/settings.php');
require_once t360_path_lib('wp/hooks.php');

// bootstrap the correct front-end controller:
if ( is_admin() )
	require_once t360_path_lib('wp/controller/admin.php');
else
	require_once t360_path_lib('wp/controller/site.php');

// initialize the main action:
add_action( 'init', 't360_init' );