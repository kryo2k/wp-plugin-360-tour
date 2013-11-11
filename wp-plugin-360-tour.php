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
define ( "T360_KEY_SETTINGS", 't360-settings' );

define ( "T360_SETTING_ENABLED",   't360_enabled' );
define ( "T360_SETTING_SITEID",    't360_siteid' );
define ( "T360_SETTING_BASEURL",   't360_baseurl' );
define ( "T360_SETTING_TARGETSEL", 't360_targetselector' );
define ( "T360_SETTING_POSITION",  't360_position' );
define ( "T360_SETTING_IMAGE",     't360_image' );
define ( "T360_SETTING_IMAGETITLE",'t360_imagetitle' );
define ( "T360_SETTING_WINDOWTITLE",'t360_windowtitle' );

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
function t360_admin_setting_section_image() {
?><p><?php esc_html_e( 'Image settings for 360 tour plugin.', T360_I18N ); ?></p><?php
}
function t360_admin_setting_enabled() {
	echo sprintf('<input name="%s" type="checkbox" value="1" class="code"%s>',T360_SETTING_ENABLED, checked( 1, t360_get_enabled(), false ));
}
function t360_admin_setting_siteid() {
	echo sprintf('<input name="%s" size="40" type="text" value="%s">',T360_SETTING_SITEID, t360_get_siteid());
}
function t360_admin_setting_baseurl() {
	echo sprintf('<input name="%s" size="50" type="text" value="%s">',T360_SETTING_BASEURL, t360_get_baseurl());
}
function t360_admin_setting_targetselector() {
	echo sprintf('<input name="%s" size="50" type="text" value="%s">',T360_SETTING_TARGETSEL, t360_get_targetselector());
}
function t360_admin_setting_position() {
	echo sprintf('<input name="%s" size="40" type="text" value="%s">',T360_SETTING_POSITION, t360_get_position());
}
function t360_admin_setting_image() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',T360_SETTING_IMAGE, t360_get_image());
}
function t360_admin_setting_imagetitle() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',T360_SETTING_IMAGETITLE, t360_get_imagetitle());
}
function t360_admin_setting_windowtitle() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',T360_SETTING_WINDOWTITLE, t360_get_windowtitle());
}
function t360_admin_get_settings_sections() {
	return (array) apply_filters('t360_admin_get_settings_sections', array(
		't360_general' => array(
			'title'    => __( 'General settings', T360_I18N ),
			'callback' => 't360_admin_setting_section_general'
		),
		't360_image' => array(
			'title'    => __( 'Image settings', T360_I18N ),
			'callback' => 't360_admin_setting_section_image'
		)
	));
}
function t360_admin_get_settings_fields() {
	return (array) apply_filters('t360_admin_get_settings_fields', array(
		't360_general' => array(
			T360_SETTING_ENABLED => array(
				'title'             => __( 'Enable 360 tour plugin', T360_I18N ),
				'callback'          => 't360_admin_setting_enabled',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),
			T360_SETTING_SITEID => array(
				'title'             => __( 'Site id', T360_I18N ),
				'callback'          => 't360_admin_setting_siteid',
				'sanitize_callback' => 'trim',
				'args'              => array()
			),
			T360_SETTING_BASEURL => array(
				'title'             => __( 'Site url (%s is replaced by site id)', T360_I18N ),
				'callback'          => 't360_admin_setting_baseurl',
				'sanitize_callback' => 'trim',
				'args'              => array()
			),
			T360_SETTING_TARGETSEL => array(
				'title'             => __( 'jQuery target for 360 tour', T360_I18N ),
				'callback'          => 't360_admin_setting_targetselector',
				'args'              => array()
			),
			T360_SETTING_WINDOWTITLE => array(
				'title'             => __( 'Title for pop-up window', T360_I18N ),
				'callback'          => 't360_admin_setting_windowtitle',
				'args'              => array()
			)
		),
		't360_image' => array(
			T360_SETTING_POSITION => array(
				'title'             => __( 'Position of link', T360_I18N ),
				'callback'          => 't360_admin_setting_position',
				'args'              => array()
			),
			T360_SETTING_IMAGE => array(
				'title'             => __( 'Image for link', T360_I18N ),
				'callback'          => 't360_admin_setting_image',
				'args'              => array()
			),
			T360_SETTING_IMAGETITLE => array(
				'title'             => __( 'Image title for link', T360_I18N ),
				'callback'          => 't360_admin_setting_imagetitle',
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
	return intval ( get_option ( T360_SETTING_ENABLED, 1 ) ) === 1;
}
function t360_get_targetselector() {
	return get_option ( T360_SETTING_TARGETSEL, 'header' );
}
function t360_get_position() {
	return get_option ( T360_SETTING_POSITION, 'bottom-right' );
}
function t360_get_image() {
	return get_option ( T360_SETTING_IMAGE, path_join(plugin_dir_url(__FILE__),
		"images/default.png") );
}
function t360_get_imagetitle() {
	return get_option ( T360_SETTING_IMAGETITLE, "360 Tour" );
}
function t360_get_windowtitle() {
	return get_option ( T360_SETTING_WINDOWTITLE, "360 Tour" );
}
function t360_get_baseurl() {
	return get_option ( T360_SETTING_BASEURL );
}
function t360_get_siteid() {
	return get_option ( T360_SETTING_SITEID );
}
function t360_site_header_style() {
	wp_enqueue_style('t360', path_join(plugin_dir_url(__FILE__),
		"css/style.css"), false);
}
function t360_site_header_script() {
	wp_enqueue_script('t360', path_join(plugin_dir_url(__FILE__),
		"js/core.js"), false);
}
function t360_site_header_script_config() {
	echo sprintf('<script type="text/javascript">window.t360_config = %s;</script>',
			json_encode(array(
			'selector' => t360_get_targetselector(),
			'positionCls' => t360_get_position(),
			'image' => t360_get_image(),
			'imageTitle' => t360_get_imagetitle(),
			'windowTitle' => t360_get_windowtitle(),
			'url' => sprintf( t360_get_baseurl(), t360_get_siteid() ),
			'enabled' => t360_get_enabled()
		))
	);
}
function t360_site_init() {
}
function t360_controller_admin_boot() {
	add_action ( 'admin_init', 't360_admin_init' );
	add_action ( 'admin_menu', 't360_admin_menu' );
	add_filter ( 'plugin_action_links', 't360_admin_plugin_action_links', 10, 2 );
}
function t360_controller_site_boot() {
	if(!t360_get_enabled()) return;

	add_action( 'init', 't360_site_init' );
	add_action( 'wp_enqueue_scripts', 't360_site_header_style' );
	add_action( 'wp_enqueue_scripts', 't360_site_header_script' );
	add_action( 'wp_head', 't360_site_header_script_config' );
}

// bootstrap the correct front-end controller:
is_admin () ? t360_controller_admin_boot () : t360_controller_site_boot ();