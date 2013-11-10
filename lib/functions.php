<?php

// @see http://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
function t360_joinpath($path) {
	return preg_replace ( '~[/\\\]+~', DIRECTORY_SEPARATOR, implode ( DIRECTORY_SEPARATOR, array_filter ( func_get_args (), function ($p) {
		return $p !== '';
	} ) ) );
}

function t360_file_base($filename) {
	return call_user_func_array('t360_joinpath', array_unshift(func_get_args(), T360_BASE_DIR));
}

function t360_path_resource($filename) {
	return t360_file_base('res/', $filename);
}

function t360_path_lib($filename) {
	return t360_file_base('lib/', $filename);
}