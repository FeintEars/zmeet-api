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

// We use user/login, user/register and user/invite endpoints instead of that.

/**
 * User object - Read.
 */
function object_user_read($params) {
	object_user_delete($params); // Same access.
}

/**
 * User object - Update.
 */
function object_user_update($params) {
	object_user_delete($params); // Same access.
}

/**
 * User object - Delete.
 */
function object_user_delete($params) {
	user_access($params);

	global $user;

	// if ($user is admin) return TRUE;

	error_access_denied();
}
