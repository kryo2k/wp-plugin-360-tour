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

defined("T360_BASE_DIR") ||
define("T360_BASE_DIR", dirname(__FILE__));

// @see http://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
function t360_joinpath($path) {
	return preg_replace ( '~[/\\\]+~', DIRECTORY_SEPARATOR, implode ( DIRECTORY_SEPARATOR, array_filter ( func_get_args (), function ($p) {
		return $p !== '';
	} ) ) );
}

function t360_file_base($filename) {
	$args = func_get_args();
	array_unshift($args, T360_BASE_DIR);
	return call_user_func_array('t360_joinpath', $args);
}

function t360_path_resource($filename) {
	return t360_file_base('res/', $filename);
}

function t360_admin_menu() {

	if ( class_exists( 'Jetpack' ) ) {
		add_submenu_page( 'jetpack', __( '360 Tour Manager' ), __( '360 Tour' ), 'manage_options', '360-tour-config', 't360_admin_view_manager' );
	} else {
		add_submenu_page('plugins.php', __('360 Tour Manager'), __('360 Tour'), 'manage_options', '360-tour-config', 't360_admin_view_manager');
	}

/*	add_object_page( __( '360 Tour Manager', 't360' ), __( '360 Tour', 't360' ),
	't360_read_contact_forms', 't360', 't360_admin_view_manager',
	t360_path_resource( 'image/admin/menu-icon.png' ) );

	$contact_form_admin = add_submenu_page( 't360',
		 __( 'Edit Contact Forms', 't360' ), __( 'Edit', 't360' ),
		't360_read_contact_forms', 't360', 't360_admin_management_page' );

	add_action( 'load-' . $contact_form_admin, 't360_load_contact_form_admin' ); */
}

function t360_admin_view_manager() {
	echo "Hello World!";
}

function t360_controller_admin_boot() {
	add_action( 'admin_menu', 't360_admin_menu' );
}

function t360_controller_site_boot() {
}

// initialize the main action:
add_action( 'init', 't360_init' );
function t360_init() {
}

// bootstrap the correct front-end controller:
is_admin() ? t360_controller_admin_boot() : t360_controller_site_boot();