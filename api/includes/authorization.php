<?php

/**
 * @file
 * Authorization functions.
 */

// Get the authorize hash.
function authhash($id, $email, $password_md5, $time) {
	return md5(strtolower($_ENV['APP_KEY'] . $id . $email . $password_md5 . $time));
}

// Prepare the response to authorize $user.
function authuser($user) {
	$response = [
		// 'id' => $user->get('id'),
		'time' => time(),
		'hash' => authhash($user->get('id'), $user->get('email'), $user->get('password_md5'), time()),
	];
	return $response;
}
