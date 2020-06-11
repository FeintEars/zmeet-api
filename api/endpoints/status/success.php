<?php

/**
 * @file
 * Success status callbacks.
 */

// General success callback.
function success_ok($params = []) {
	$response = [
		'status' => 'ok',
	];

	api_response($response);
}
