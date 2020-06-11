<?php

/**
 * @file
 * Load .ENV variables.
 */

// Temporary solution.
function _getenv_load() {
	$env = file_get_contents('.ENV');
	$env_strs = explode("\n", $env);
	foreach ($env_strs as $env_str) {
		$env_var = explode('=', $env_str);
		if (count($env_var) == 2) {
			$_ENV[$env_var[0]] = $env_var[1];
		}
	}
}

_getenv_load();
