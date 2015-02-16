<?php

define('TYPE_KEG', 1);
define('TYPE_BOTTLE', 2);
define('PINTS_PER_KEG', 29);

define('STATUS_ERROR', 0);
define('STATUS_SUCCESS', 1);

function get_base_url()
{
	$base_url = dirname($_SERVER['SCRIPT_NAME']);
	return $base_url;
}

function get_request_path()
{
	$stripped = explode(get_base_url(), $_SERVER['REQUEST_URI'], 2);
	$prefix = substr($stripped[1], 1);
	return $prefix;
}

function generate_response($status, $message, $data = array()) {
	return array(
		'status' => $status,
		'message' => $message,
		'data' => $data
	);
}