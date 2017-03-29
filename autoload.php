<?php


spl_autoload_register(function($class) {


	$class = str_replace('\\', DIRECTORY_SEPARATOR, str_replace( '_', '-', strtolower($class)));

	$path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $class . '.php';

	if (file_exists($path))
		require_once $path;


});