<?php

require_once __DIR__ . '/endpoints/status/error.php';

/**
 * @file.
 * The bridge to API endpoints.
 */

$router = [

	'/' => [
		'GET' => [
			'include_files' => ['access/role_guest.php', 'endpoints/status/success.php'],
			'callbacks_queue' => ['role_guest_access', 'success_ok'],
			// Possible general errors:
			// 0 - Undefined error with message
			// 1 - For authorized requests: missing authorization parameters (id, time, hash)
			// 2 - Missing required parameters
			// 3 - Parameter is not valid
			// 4 - For authorized requests: Authorization parameters are not correct. Force user to log in again.
			// 5 - For authorized requests: The session time is expired. Force user to log in again.
			// 403 - Access denied
			// 404 - Not found
		],
	],


	'user/login' => [
		'POST' => [
			'include_files' => ['access/role_guest.php', 'endpoints/user/login.php'],
			'callbacks_queue' => ['role_guest_access', 'endpoints_user_login'],
			// Required: login, password.
			// Possible errors:
			// 100 - Either email or password are wrong.
		]
	],
	'user/register' => [
		'POST' => [
			'include_files' => ['access/role_guest.php', 'endpoints/user/register.php'],
			'callbacks_queue' => ['role_guest_access', 'endpoints_user_register'],
			// Required: email, first_name, last_name, password, password2.
			// Possible errors:
			// 101 - Password parameter is too simple
		]
	],


	'user/read' => [
		'GET' => [
			'include_files' => ['access/object_user.php', 'endpoints/object/User.php'],
			'callbacks_queue' => ['object_user_read', 'endpoints_user_read'],
			// Required: (id, time, hash), user_id.
		]
	],
	'user/update' => [
		'POST' => [
			'include_files' => ['access/object_user.php', 'endpoints/object/User.php'],
			'callbacks_queue' => ['object_user_update', 'endpoints_user_update'],
			// Required: (id, time, hash), user_id.
			// Optional: first_name, last_name, password, password2.
		]
	],
	'user/delete' => [
		'POST' => [
			'include_files' => ['access/object_user.php', 'endpoints/object/User.php'],
			'callbacks_queue' => ['object_user_delete', 'endpoints_user_delete'],
			// Required: (id, time, hash), user_id.
		]
	],


];




/**
 * Start calling API methods.
 */

// Get request parameters for API.
$params = [];
switch ($method = $_SERVER['REQUEST_METHOD']) {

	case 'GET':
		$params = $_GET;
		break;

	case 'POST':
		$params = $_POST;
		if (count($params) == 0) {
			$json =  file_get_contents("php://input");
			$json = preg_replace('/\s(\w+)\s/i', '"$1"', $json);
			$json = json_decode($json, true);
			if (count($json)) $params = $json;
		}
		break;

	case 'PUT':
	case 'DELETE':
		parse_str(file_get_contents("php://input"), $GLOBALS['params']);
		break;
}

// Check the endpoint.
$endpoint = '/';
if ($_SERVER['SCRIPT_NAME'] == '/index.php' && isset($_SERVER['REDIRECT_URL'])) {
	$endpoint = trim($_SERVER['REDIRECT_URL'], '/');
}
if (($_SERVER['SCRIPT_NAME'] == '/api/api.php' || !isset($_SERVER['REDIRECT_URL'])) && isset($params['endpoint'])) {
	$endpoint = ($params['endpoint'] == '/') ? '/' : rtrim($params['endpoint'], '/');
	unset($params['endpoint']);
}
if ($_SERVER['SCRIPT_NAME'] == '/api/api.php') { header("HTTP/1.0 404 Not Found"); exit; }
if ($_ENV['APP_MODE'] == 'both') {
	$endpoint = ltrim($endpoint, 'api');
	$endpoint = ltrim($endpoint, '/');
}
if ($endpoint == '') $endpoint = '/';
$GLOBALS['endpoint'] = $endpoint;

// Helper API function: Execute the callbacks queue.
function api_executor($include_files, $callbacks_queue, $params = []) {
	foreach ($include_files as $file) {
		require_once $file;
	}
	foreach ($callbacks_queue as $callback) {
		$callback($params);
	}
}

// Helper API function: Print response.
function api_response($response = []) {
	// Close database connection right before response.
	global $db;
	if (isset($db)) $db->close();

	if (!isset($response['status'])) $response = ['status' => 'ok'] + $response;
	print json_encode($response);
	// print '<pre>'; print_r($response); print '</pre>';

	exit;
}

// Executing methods.
if (isset($router[$endpoint])) {
	if (isset($router[$endpoint][$method])) {

		require_once 'api/database/MySQL.php';
		$GLOBALS['db'] = new MySQL($_ENV['DB_HOST'], $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

		api_executor(
			$router[$endpoint][$method]['include_files'],
			$router[$endpoint][$method]['callbacks_queue'],
			$params
		);
	}
}

// Not found error.
error_not_found();
