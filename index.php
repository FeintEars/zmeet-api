<?php

require_once 'api/includes/environment.php';

// Pass directly if API mode.
if ($_ENV['APP_MODE'] == 'api') {
	require 'api/api.php';
}

// Both website and API are enabled.
if ($_ENV['APP_MODE'] == 'both') {
	if (strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
		require 'api/api.php';
	}
	else {
		require 'includes/View.php';
		$view = new View;
		$view->data = [
			'main_title' => 'API Server',
			'url' => $_SERVER['REQUEST_URI'],
			'header' => $view->render('templates/blocks/header.tpl.html'),
			'footer' => $view->render('templates/blocks/footer.tpl.html'),
		];

		// Direct routes.

		$route = [
			'/' => ['title' => 'Homepage', 'template' => 'templates/pages/index.tpl.html'],
			'/403' => ['title' => '403 Access Denied', 'template' => 'templates/pages/403.tpl.html'],
			'/404' => ['title' => '404 Not Found', 'template' => 'templates/pages/404.tpl.html'],
		];

		$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
		if ($request_uri == '') $request_uri = '/';

		if (isset($route[$request_uri])) {
			$view->data += $route[$request_uri];
			print $view->render($route[$request_uri]['template']);
			exit;
		}

		// 404 - Not found.

		header("HTTP/1.0 404 Not Found");
		$view->data += $route['/404'];
		print $view->render($route['/404']['template']);
	}
}
