<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * Endpoint: POST user/create.
 */

// We use user/login, user/register and user/invite endpoints instead of that.

/**
 * Endpoint: GET user/read.
 */
function endpoints_user_read($params) {
	check_error_required(['user_id'], $params);

	global $user, $obj;

	// Response.
	$response = authuser($user) + [
		'user' => $obj->getArray(),
	];
	api_response($response);
}

/**
 * Endpoint: PUT user/update.
 */
function endpoints_user_update($params) {
	check_error_required(['user_id'], $params);
	if (isset($params['password'])) {
		check_password_valid($params['password']);
		if ($params['password'] != $params['password2']) error('Passwords are not compared');
		$params['password_md5'] = md5($params['password']);
	}

	global $user, $obj;

	// Default parameters.
	$params += [
		'first_name' => $obj->get('first_name'),
		'last_name' => $obj->get('last_name'),
		'password_md5' => $obj->get('password_md5'),
	];

	foreach (['first_name', 'last_name', 'password_md5'] as $prop) {
		$obj->set($prop, $params[$prop]);
	}
	$obj->update();

	// Response.
	$response = [
		'message' => 'User is updated',
	] + authuser($user) + [
		'user' => $obj->getArray(),
	];
	api_response($response);
}

/**
 * Endpoint: DELETE user/delete.
 */
function endpoints_user_delete($params) {
	check_error_required(['user_id'], $params);

	global $user, $obj;
	$user_id = $obj->get('id');
	$obj->delete();

	// Response.
	$response = [
		'message' => "User $user_id is deleted",
	] + authuser($user);
	api_response($response);
}
