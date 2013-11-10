<?php
/**
 * Plugin Name: 360 tour plugin
 * Description: Custom plugin to add support for 360 tour service (provided by http://360.cr/) implemented on hotelarenalkioro.com
 * Version: 0.1
 * Author: Hans Doller
 * Author URI: http://ticonerd.com
 * License: GPLv3
 */

if ( !function_exists( 'add_action' ) ) {
	exit;
}

define("T360_VERSION", '1.0');

defined("T360_BASE_DIR") ||
define("T360_BASE_DIR", dirname(__FILE__));

// @see http://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
function t360_file_join($path) {
	return preg_replace ( '~[/\\\]+~', DIRECTORY_SEPARATOR, implode ( DIRECTORY_SEPARATOR, array_filter ( func_get_args (), function ($p) {
		return $p !== '';
	} ) ) );
}

function t360_file_base($filename) {
	$args = func_get_args();
	array_unshift($args, T360_BASE_DIR);
	return call_user_func_array('t360_file_join', $args);
}

function t360_file_resource($filename) {
	return t360_file_base('res/', $filename);
}

function t360_admin_cb_msg_warn($message) {
	$html = <<<_TPL
	<div class="updated fade">{$message}</div>
_TPL;

	return create_function('', 'echo "'+ addcslashes($html) +'";');
}
function t360_admin_init() {
	global $wp_version;

	if (! function_exists ( 'is_multisite' ) && version_compare ( $wp_version, '3.0', '<' )) {
		add_action ( 'admin_notices', t360_admin_cb_msg_warn(sprintf(__('360 Tour v%s requires WordPress 3.0 or higher.'), T360_VERSION)) );
		return;
	}
}

function t360_admin_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( t360_file_base(__FILE__) ) ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=t360-key-config' ) . '">'.__( 'Settings' ).'</a>';
	}

	return $links;
}

function t360_admin_menu() {

	if ( class_exists( 'Jetpack' ) ) {
		add_submenu_page( 'jetpack', __( '360 Tour Manager' ), __( '360 Tour' ), 'manage_options', '360-tour-config', 't360_admin_view_manager' );
	} else {
		add_submenu_page('plugins.php', __('360 Tour Manager'), __('360 Tour'), 'manage_options', '360-tour-config', 't360_admin_view_manager');
	}
}

function t360_admin_view_manager() {
	echo "Hello World!";
}

function t360_get_enabled() {
	return strcasecmp(get_option('t360_enabled', 'yes'), 'yes') === 0;
}

function t360_get_baseurl() {
	return get_option('t360_base_url');
}

function t360_get_siteid() {
	return get_option('t360_site_id');
}

add_action( 'init', 't360_init' );
function t360_init() {
}

function t360_controller_admin_boot() {
	add_action( 'admin_init', 't360_admin_init' );
	add_action( 'admin_menu', 't360_admin_menu' );
	add_filter( 'plugin_action_links', 't360_admin_plugin_action_links', 10, 2 );
}

function t360_controller_site_boot() {
}

// bootstrap the correct front-end controller:
is_admin() ? t360_controller_admin_boot() : t360_controller_site_boot();