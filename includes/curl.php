<?php

namespace MoySklad\Includes;


use MoySklad\Dev\Dev;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Curl {


	public static
		$userEmail;

	const // protected: only php 7.1
    	END_POINT = 'https://online.moysklad.ru/api/remap/1.1';

	function __construct() {}

	public function init($url, $type = 'get', $data = '') {
		return $this->curl($url, $type, $data);
	}

	protected function curl($url, $type, $data) {
		$url = self::END_POINT . $url;
		$ch = curl_init($url);

		$header = ['Authorization: Basic ' . base64_encode(MOYSKLAD_LOGIN . ':' . MOYSKLAD_PASSWORD)];

		if ($type == 'post') :
			curl_setopt($ch, CURLOPT_POST, 1);
		elseif ($type == 'put') :
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		endif;

		if ($type == 'post' || $type == 'put') :
			array_push($header, 'Content-Type:application/json');

			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		endif;

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10);

		$return = curl_exec($ch);
		$info = curl_getinfo($ch);

		if (json_last_error_msg() !== 'No error') :
			Dev::JSONError(json_last_error_msg(), $info['url'], self::$userEmail);
		endif;

		if (!$return) :
			Dev::curlError(curl_error($ch), $info['url'], self::$userEmail);
		endif;

		curl_close($ch);
		$return = json_decode($return, true);

		Dev::logFile($return, $info['url'], self::$userEmail);

		/**
		 * Если MoySklad вернул сообщение с ошибкой
		 */
		if (!empty($return['errors'])) :
			Dev::apiError($return['errors'], $info['url'], self::$userEmail);
		endif;

		return $return;
	}


}