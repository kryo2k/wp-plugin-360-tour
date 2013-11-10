<?php
/**
 * Plugin Name: 360 tour plugin
 * Description: Custom plugin to add support for 360 tour service (provided by http://360.cr/) implemented on hotelarenalkioro.com
 * Version: 0.1
 * Author: Hans Doller
 * Author URI: http://ticonerd.com
 * License: GPLv3
 */

if (! function_exists ( 'add_action' )) {
	exit ();
}

define ( "T360_I18N", 't360' );

defined ( "T360_BASE_DIR" ) || define ( "T360_BASE_DIR", dirname ( __FILE__ ) );

defined ( "T360_KEY_SETTINGS" ) || define ( "T360_KEY_SETTINGS", 't360-settings' );

function t360_admin_init() {
	global $wp_version;
	if (! function_exists ( 'is_multisite' ) && version_compare ( $wp_version, '3.0', '<' )) {
		wp_die( __( '360 tour plugin requires wordpress >= 3.0' ) );
	}

	t360_admin_register_settings();
}
function t360_admin_plugin_action_links($links, $file) {
	if ($file == plugin_basename ( __FILE__ )) {
		$links [] = '<a href="' . add_query_arg ( array (
				'page' => T360_KEY_SETTINGS 
		), admin_url ( 'options-general.php' ) ) . '">' . __ ( 'Settings', T360_I18N ) . '</a>';
	}
	
	return $links;
}
function t360_admin_menu() {
	add_options_page( __ ( '360 Tour Manager', T360_I18N ), __ ( '360 Tours', T360_I18N ), 'manage_options', T360_KEY_SETTINGS, 't360_admin_options' );
}
function t360_admin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

?><form method="POST" action="options.php">
<?php settings_fields(T360_KEY_SETTINGS); // pass slug name of page, also referred to in Settings API as option group name
do_settings_sections(T360_KEY_SETTINGS);  // pass slug name of page
submit_button();
?>
</form><?php
}
function t360_admin_setting_section_general() {
?><p><?php esc_html_e( 'General settings for 360 tour plugin.', T360_I18N ); ?></p><?php
}
function t360_admin_setting_enabled() {
	echo sprintf('<input name="%s" type="checkbox" value="1" class="code"%s>','t360_enabled', checked( 1, t360_get_enabled(), false ));
}
function t360_admin_setting_siteid() {
	echo sprintf('<input name="%s" type="text" value="%s">','t360_siteid', t360_get_siteid());
}
function t360_admin_setting_baseurl() {
	echo sprintf('<input name="%s" type="text" value="%s">','t360_baseurl', t360_get_baseurl());
}
function t360_admin_get_settings_sections() {
	return (array) apply_filters('t360_admin_get_settings_sections', array(
		't360_general' => array(
			'title'    => __( '360 tour settings', T360_I18N ),
			'callback' => 't360_admin_setting_section_general'
		)
	));
}
function t360_admin_get_settings_fields() {
	return (array) apply_filters('t360_admin_get_settings_fields', array(
		't360_general' => array(
			't360_enabled' => array(
				'title'             => __( 'Enable 360 tour plugin', T360_I18N ),
				'callback'          => 't360_admin_setting_enabled',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),
			't360_siteid' => array(
				'title'             => __( 'Site id', T360_I18N ),
				'callback'          => 't360_admin_setting_siteid',
				'sanitize_callback' => 'trim',
				'args'              => array()
			),
			't360_baseurl' => array(
				'title'             => __( 'Site url (%s is replaced by site id)', T360_I18N ),
				'callback'          => 't360_admin_setting_baseurl',
				'sanitize_callback' => 'trim',
				'args'              => array()
			)
		)
	));
}
function t360_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty
	if ( empty( $section_id ) )
		return false;

	$fields = t360_admin_get_settings_fields();
	$retval = isset( $fields[$section_id] ) ? $fields[$section_id] : false;

	return (array) apply_filters( 't360_admin_get_settings_fields_for_section', $retval, $section_id );
}
function t360_admin_register_settings() {
	$sections = t360_admin_get_settings_sections();

	if ( empty( $sections ) )
		return false;

	foreach ( (array) $sections as $section_id => $section ) {

		$page = empty($section['page']) ? T360_KEY_SETTINGS : $section['page'];
		$fields = t360_admin_get_settings_fields_for_section( $section_id );

		if ( empty( $fields ) )
			continue;

		add_settings_section(
			$section_id,
			$section['title'],
			$section['callback'],
			$page
		);

		foreach ( (array) $fields as $field_id => $field ) {

			if ( ! empty( $field['callback'] ) && !empty( $field['title'] ) ) {
				add_settings_field( $field_id, $field['title'], $field['callback'], $page, $section_id, $field['args'] );
			}

			register_setting( $page, $field_id, $field['sanitize_callback'] );
		}
	}
}
function t360_get_enabled() {
	return intval ( get_option ( 't360_enabled', 1 ) ) === 1;
}
function t360_get_baseurl() {
	return get_option ( 't360_baseurl' );
}
function t360_get_siteid() {
	return get_option ( 't360_siteid' );
}
function t360_site_init() {
}
function t360_controller_admin_boot() {
	add_action ( 'admin_init', 't360_admin_init' );
	add_action ( 'admin_menu', 't360_admin_menu' );
	add_filter ( 'plugin_action_links', 't360_admin_plugin_action_links', 10, 2 );
}
function t360_controller_site_boot() {
	add_action ( 'init', 't360_site_init' );
}

// bootstrap the correct front-end controller:
is_admin () ? t360_controller_admin_boot () : t360_controller_site_boot ();