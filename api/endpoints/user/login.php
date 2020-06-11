<?php

require_once 'api/includes/authorization.php';
require_once 'api/entities/User.php';

/**
 * Endpoint: POST user/login.
 */
function endpoints_user_login($params) {
	check_error_required(['login', 'password'], $params);

	$user = User::login($params);
	if ($user == FALSE) {
		error('Either email or password are wrong.', 100);
	}

	$response = [
		'message' => 'Successfully logged in.',
		'id' => $user->get('id'),
	] + authuser($user);

	api_response($response);
}
