<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * @file
 * Access level: user.
 */

function user_access($params = []) {

	/**
	 * Error code - 1
	 * Required authorization parameters: id, time, hash.
	 */
	$required = ['id', 'time', 'hash'];
	$required_keys = array_flip($required);
	foreach ($params as $key => $value) {
		if (isset($required_keys[$key])) {
			unset($required[$required_keys[$key]]);
		}
	}
	if (count($required) == 1) {
		error('Missing authorization parameter: ' . current($required), 1);
	}
	else if (count($required) > 1) {
		error('Missing authorization parameters: ' . implode($required, ', '), 1);
	}

	/**
	 * Error code - 4
	 * Authorization parameters are not correct.
	 */
	$error = TRUE;
	$user = new User($params['id']);
	if ($user) {
		$hash = authhash($params['id'], $user->get('email'), $user->get('password_md5'), $params['time']);
		if ($hash == $params['hash']) {
			$error = FALSE;
		}
	}
	if ($error) {
		error('Authorization parameters are not correct. Force user to log in again.', 4);
	}

	/**
	 * Error code - 5
	 * The session time is expired.
	 */
	$now = time();
	if ($now - $params['time'] > 60 * 60 * 24) {
		error('The session time is expired. Force user to log in again.', 5);
	}

	// Set user to global variables.
	$GLOBALS['user'] = $user;

}
