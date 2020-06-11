<?php

/**
 * @file
 * Error status callbacks.
 */

// General error callback.
function error($message, $error_code = 0) {
	$response = [
		'status' => 'error',
		'error_code' => $error_code,
		'message' => $message
	];

	api_response($response);
}

// Error 0 - unknown error.
function error_unknown() {
	error('Unknown error');
}

// Error 403 - access denied.
function error_access_denied($params = []) {
	error('Access denied', 403);
}

// Error 404 - not found.
function error_not_found($params = []) {
	error('Endpoint not found: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $GLOBALS['endpoint'], 404);
}

// Error 2 - missing required parameters.
function error_required($required) {
	$count = count($required);
	if ($count == 0) {
		error('Missing required parameter.', 2);
	}
	else if ($count == 1) {
		error('Missing required parameter: ' . current($required), 2);
	}
	else {
		error('Missing required parameters: ' . implode($required, ', '), 2);
	}
}

// Check Error - 2.
function check_error_required($required, $params = []) {
	$required_keys = array_flip($required);
	foreach ($params as $key => $value) {
		if (isset($required_keys[$key]) && trim($value) !== '') {
			unset($required[$required_keys[$key]]);
		}
	}
	if (count($required)) {
		error_required($required);
	}
}

// Error 3 - $field_name is not valid.
function error_not_valid($field_name) {
	error("$field_name is not valid.", 3);
}

// Check Error 3 - Email validation.
function check_email_valid($email) {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		error_not_valid('email');
	}
}

// Check Error 3 - Date validation.
function check_date_valid($date, $field = 'date') {
	if (!is_numeric($date)) {
		error_not_valid($field);
	}
}

// Error 101 - Password is too simple.
function error_password($password) {
	error('Password is too simple', 101);
}

// Check Error 101 - Password validation.
function check_password_valid($password) {
	if (strlen($password) <= 3) {
		error_password($password);
	}
}

// Check Error 0 - Email is existed.
function error_email_existed($email) {
	error('Email is already registered', 0);
}

// Check Error 0 - Email is existed.
function check_email_existed($email) {
	global $db;
	$result = $db->select('id', 'users', [ ['email', strtolower($email), '='] ]);
	if (count($result)) error_email_existed($email);
}
