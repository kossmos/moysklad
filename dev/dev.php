<?php

namespace MoySklad\Dev;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Dev {


	static function apiError($return, $url, $email) {
		foreach ($return as $value) {
			$data = date('r') . ';' . $email . ';' . $value['parameter'] . ';' . $value['error'] . ';' . $url . "\r\n";

			self::file('api', $data);
		}
	}

	static function curlError($error, $url, $email) {
		$data = date('r') . ';' . $email . ';' . $error . ';' . $url . "\r\n";

		self::file('curl', $data);
	}

	static function logFile($return, $url) {
		$data = date('r') . ': ' . "\n" . 'url:' . $url . "\n" . var_export($return, true) . "\n\n";

		self::file('all', $data);
	}

	static function JSONError($error, $url, $email) {
		$data = date('r') . ';' . $email . ';' . $error . ';' . $url . "\r\n";

		self::file('json', $data);
	}

	static function file($name, $data) {
		file_put_contents(WP_CONTENT_DIR . '/logs/debug-moysclad-' . $name . '.log', $data, FILE_APPEND | LOCK_EX);
	}


}