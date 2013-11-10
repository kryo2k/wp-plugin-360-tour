<?php

function t360_init() {
}

add_action( 'admin_menu', 't360_admin_menu', 9 );

function t360_admin_menu() {
	add_object_page( __( '360 Tour Manager', 't360' ), __( '360 Tour', 't360' ),
	't360_read_contact_forms', 't360', 't360_admin_management_page',
	t360_path_resource( 'image/admin/menu-icon.png' ) );

/*	$contact_form_admin = add_submenu_page( 't360',
			__( 'Edit Contact Forms', 't360' ), __( 'Edit', 't360' ),
			't360_read_contact_forms', 't360', 't360_admin_management_page' ); */

//	add_action( 'load-' . $contact_form_admin, 't360_load_contact_form_admin' );
}

function t360_admin_management_page() {
	echo "Hello World!";
}