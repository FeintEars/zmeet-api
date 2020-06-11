<?php

require_once 'user.php';
require_once 'api/entities/User.php';

/**
 * @file
 * Access level: user object.
 */

/**
 * User object - Create.
 */
function object_user_create($params) {
	if (isset($_ENV['APP_ADMIN'])) {
		if ($_ENV['APP_ADMIN']) {
			return TRUE; // Admin mode is enabled.
		}
	}

	error_access_denied(); // Admin mode is not enabled.
}

/**
 * User object - Read.
 */
function object_user_read($params) {
	return TRUE; // Always TRUE.
}

/**
 * User object - Update.
 */
function object_user_update($params) {
	object_user_create($params); // Same access.
}

/**
 * User object - Delete.
 */
function object_user_delete($params) {
	object_user_create($params); // Same access.
}
