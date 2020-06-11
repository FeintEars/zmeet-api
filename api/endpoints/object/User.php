<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * Endpoint: POST user/create.
 */
function endpoints_user_create($params) {
	check_error_required(['a', 'email', 'first_name', 'last_name', 'company', 'position'], $params);

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
	check_error_required(['a'], $params);

	$user = new User($params['a']);

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
	check_error_required(['a', 'email', 'first_name', 'last_name', 'company', 'position'], $params);

	$user = new User($params['a']);

	$fbId = $user->get('fbId');
	if ($fbId) $params['fbId'] = $fbId;

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
	check_error_required(['a'], $params);

	$user = new User($params['a']);
	$user->delete();

	$response = [
		'message' => 'User successfully deleted',
	];
	api_response($response);
}

/**
 * Endpoint: POST user/avatar.
 */
function endpoints_user_append_avatar($params) {
	check_error_required(['a', 'b'], $params);

	$user = new User($params['a']);
	$user->attachFacebookId($params['b']);
	$user->update();

	$response = []; // ok.
	api_response($response);
}
