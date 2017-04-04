<?php

namespace MoySklad\Dev;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Dev {


	static function test() {
		echo __NAMESPACE__ . ' > ' . __CLASS__;
	}

	static function logDisplay($ch) {
		$info = curl_getinfo($ch);

		echo '<pre>';
			var_dump($info['url']);
			// var_dump($return);
			// var_dump(json_decode($return, true));
		echo '</pre>';
	}

	static function logFile($return) {
		$return = json_decode($return);

		file_put_contents(WP_CONTENT_DIR . '/debug-moysclad.log', date('r') . ': ' . var_export($return, true) . "\n\n", FILE_APPEND | LOCK_EX);
		// debug_log_filter(, WP_CONTENT_DIR . '/debug-moysclad.log', true);

		// self::JSONError();
	}

	static function JSONError() {
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				echo ' - Ошибок нет';
			break;
			case JSON_ERROR_DEPTH:
				echo ' - Достигнута максимальная глубина стека';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				echo ' - Некорректные разряды или не совпадение режимов';
			break;
			case JSON_ERROR_CTRL_CHAR:
				echo ' - Некорректный управляющий символ';
			break;
			case JSON_ERROR_SYNTAX:
				echo ' - Синтаксическая ошибка, не корректный JSON';
			break;
			case JSON_ERROR_UTF8:
				echo ' - Некорректные символы UTF-8, возможно неверная кодировка';
			break;
			default:
				echo ' - Неизвестная ошибка';
			break;
		}

		echo PHP_EOL;
	}


}