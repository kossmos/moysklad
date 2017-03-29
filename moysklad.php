<?php
/*
Plugin Name: MoySklad
Plugin URI: https://github.com/kossmos/moysklad/
Description: Wordpress плагин для интеграции moysklad api
Version: 1.0
Author: Юрий «kossmos» Кравчук
Author URI: https://kossmos.space
License: GPL2
*/

/*  Copyright 2016 Юрий «kossmos» Кравчук  (email : kossmos.mobile@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


namespace MoySklad;

use MoySklad\Entity\Customerorder;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('autoload.php'); // require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );


class MoySklad {
	const // protected: only php 7.1
		ORGANIZATION = "13942c3b-586c-11e5-90a2-8ecb0037b09d",
		AUTH_LOGIN = "admin@shopclay",
		AUTH_PASSWORD = "649d40e23b";

	private 
    	$endpoint = "https://online.moysklad.ru/api/remap/1.1";

	function __construct() { // последовательность выполнения в конструкторе важна
	}

	protected function curl($url, $type, $data) {
		$url = $this->endpoint . $url;
		$ch = curl_init($url);

		$header = ["Authorization: Basic " . base64_encode(self::AUTH_LOGIN . ":" . self::AUTH_PASSWORD)];

		if ($type == "post") {
			array_push($header, 'Content-Type:application/json');

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$this->curlLog($ch);

		$return = curl_exec($ch);

		curl_close($ch);

		return $return;
	}

	public function customerorder($array) {
		$data = [
			"name" => (string) $array["id"],
			"moment" => $array["date"], // Дата Заказа
			"description" => $array["description"], // Комментарий Заказа покупателя
			"state" => $this->state("13a21e7b-586c-11e5-90a2-8ecb0037b0b7"), // Статус Заказа в формате Метаданных
			// "attributes" => $this->sourceOrder(), // Источник заказа только один "Сайт"
			// "attributes" => new Attributes($this->endpoint), // Источник заказа только один "Сайт"
			"organization" => $this->meta("organization", self::ORGANIZATION), // Ссылка на ваше юрлицо в формате Метаданных
			"agent" => $this->meta("counterparty", "4ef2f677-e8e1-11e5-7a69-97110007f2b2"), // Ссылка на контрагента (покупателя) в формате Метаданных
			// "positions" => [], // Ссылка на позиции в Заказе в формате Метаданных
		];

		echo "<pre>";
		echo __NAMESPACE__ . "<br>";
		echo __FUNCTION__ . "<br>";
		echo __METHOD__ . "<br>";
		echo __CLASS__ . "<br>";
		var_dump(json_encode($data));
		var_dump($data);
		echo "</pre>";

		$this->curl('/entity/' . __FUNCTION__, "post", $data);
	}

	/*
	private function sourceOrder() {
		return [[
			"id" => "f24606de-e9ce-11e5-7a69-9711001c4539",
			"value" => [
				"meta" => [
					"href" => "{$this->endpoint}/entity/customentity/e2f7dbb8-e9ce-11e5-7a69-8f55001c001b/064e93cc-e9cf-11e5-7a69-9711001c49b7",
					"metadataHref" => "{$this->endpoint}/entity/companysettings/metadata/customEntities/e2f7dbb8-e9ce-11e5-7a69-8f55001c001b",
					"type" => "customentity",
					"mediaType" => "application/json"
				]
			]
		]];
	}
	*/

	private function meta($type, $id) {
		return [
			"meta" => [
				"href" => "{$this->endpoint}/entity/{$type}/$id",
				"type" => $type,
				"mediaType" => "application/json"
			]
		];
	}

	private function state($id) {
		return [
			"meta" => [
				"href" => "{$this->endpoint}/entity/customerorder/metadata/states/$id",
				"type" => "state",
				"mediaType" => "application/json"
			]
		];
	}
















	public function delete() {}

	public function put() {}
	
	private function curlLog($ch) {
		$info = curl_getinfo($ch);
		$return = curl_exec($ch);

		echo '<pre>';
			var_dump($info['url']);
			// var_dump($return);
			// var_dump(json_decode($return, true));
		echo '</pre>';

		debug_log_filter(json_decode($return), "../../../debug-moysclad.log", true);
		
		$this->JSONError();
	}

	private function JSONError() {
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


new MoySklad();

/*
echo '<pre>';
	// var_dump($array['brand'][$_REQUEST['brand']]);
	// var_dump($array[SC_FILTER::taxonomy][$this->parce_url[1]]);
	// var_dump($string);
	var_dump($_SERVER);
	var_dump($category, $brand);
echo '</pre>';


// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
// curl_setopt($ch, CURLOPT_HEADER, 1);
// curl_setopt($ch, CURLOPT_TIMEOUT, 30);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadName);
// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

// curl_setopt($ch, CURLOPT_USERPWD, self::AUTH_LOGIN . ":" . self::AUTH_PASSWORD); // or

*/