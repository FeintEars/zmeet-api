<?php

require_once 'user.php';

/**
 * @file
 * Access level: admin.
 */

function role_admin_access($params = []) {
	user_access($params);

	global $user;

	// if ($user is admin) return TRUE;

	error_access_denied();
}
