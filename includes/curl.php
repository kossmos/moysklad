<?php 

namespace MoySklad\Includes;


use MoySklad\Dev\Dev;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Curl {
	const // protected: only php 7.1
    	END_POINT = "https://online.moysklad.ru/api/remap/1.1";

	function __construct($url, $type, $data) {
		$this->curl($url, $type, $data);

		// echo __NAMESPACE__ . ' > ' . __CLASS__;
	}

	protected function curl($url, $type, $data) {
		$url = self::END_POINT . $url;
		$ch = curl_init($url);

		$header = ["Authorization: Basic " . base64_encode(MOYSKLAD_LOGIN . ":" . MOYSKLAD_PASSWORD)];

		if ($type == "post") {
			array_push($header, 'Content-Type:application/json');

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		Dev::logDisplay($ch);
		Dev::logFile($ch);

		$return = curl_exec($ch);

		curl_close($ch);

		return $return;
	}
}