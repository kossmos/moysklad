<?php

namespace MoySklad\Includes;


use MoySklad\Dev\Dev;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Curl {


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

		if ($type == 'post') {
			curl_setopt($ch, CURLOPT_POST, 1);
		} elseif ($type == 'put') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		}

		if ($type == 'post' || $type == 'put') {
			array_push($header, 'Content-Type:application/json');

			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// Dev::logDisplay($ch);

		$return = curl_exec($ch);

		Dev::logFile($return);

		curl_close($ch);

		return json_decode($return, true);
	}


}