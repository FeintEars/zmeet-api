<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * Endpoint: POST user/register.
 */
function endpoints_user_register($params) {
	check_error_required(['email', 'first_name', 'last_name', 'password', 'password2'], $params);
	check_email_valid($params['email']);
	check_password_valid($params['password']);
	if ($params['password'] != $params['password2']) error('Passwords are not compared');
	check_email_existed($params['email']);

	$user = User::register($params);
	if ($user) {
		$response = [
			'message' => 'Successfully registered.',
			'id' => $user->get('id'),
		] + authuser($user);
		api_response($response);
	}

	error_unknown();
}
