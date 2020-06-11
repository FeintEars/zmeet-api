<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * Endpoint: POST user/create.
 */
function endpoints_user_create($params) {
	check_error_required(['email', 'first_name', 'last_name', 'company'], $params);

	$user = new User($params);

	// Response.
	$user_arr = $user->getArray();
	unset($user_arr['id']);
	unset($user_arr['status']);
	$response = [
		'message' => 'User successfully created',
		// 'user' => $user_arr,
	];
	api_response($response);
}

/**
 * Endpoint: GET user/read.
 */
function endpoints_user_read($params) {
	check_error_required(['email'], $params);

	$user = new User($params['email']);

	// Response.
	$user_arr = $user->getArray();
	unset($user_arr['id']);
	unset($user_arr['status']);
	$response = [
		'user' => $user_arr,
	];
	api_response($response);
}

/**
 * Endpoint: PUT user/update.
 */
function endpoints_user_update($params) {
	check_error_required(['email', 'first_name', 'last_name', 'company'], $params);

	$user = new User($params['email']);
	$user->delete();
	$user = new User($params);

	// Response.
	$user_arr = $user->getArray();
	unset($user_arr['id']);
	unset($user_arr['status']);
	$response = [
		'message' => 'User successfully updated',
		// 'user' => $user_arr,
	];
	api_response($response);
}

/**
 * Endpoint: DELETE user/delete.
 */
function endpoints_user_delete($params) {
	check_error_required(['email'], $params);

	$user = new User($params['email']);
	$user->delete();

	$response = [
		'message' => 'User successfully deleted',
	];
	api_response($response);
}
