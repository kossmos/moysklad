<?php
/**
 * Контрагенты
 * @url https://online.moysklad.ru/api/remap/1.1/doc/index.html#контрагент
 */

namespace MoySklad\Entity;


use MoySklad\Includes\Curl;
use MoySklad\Includes\Config;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Counterparty {


	private
		$tags,
		$attributes;

	public static
		$customerorderUserId;

	function __construct() {
		$config = new Config();

		$this->attributes = $config->attributes();
		$this->tags = $config->tags();
	}

	public function counterparty($order) {
		$curl = new Curl();
		$data = $this->dataNewUser($order);

		$result = $curl->init('/entity/' . __FUNCTION__ . '?search=' . $order->billing_email);

		if ($result['meta']['size'] == 0) : // создаём нового пользователя
			$curl->init('/entity/' . __FUNCTION__, 'post', $data);
		else : // обновляем пользователя
			self::$customerorderUserId = $result['rows'][0]['id'];

			$curl->init('/entity/' . __FUNCTION__ . '/' . self::$customerorderUserId, 'put', $data); // обновляем пользователя
		endif;
	}

	/**
	 * Данные пользователя из заказа
	 * TODO мне вообще не нравится идея обновлять поля в складе напрямую из нового заказа, надо будет что-то придумать
	 * @param  [object] $order 	Объект заказа
	 * @return [array]			Данные пользователя
	 */
	private function dataNewUser($order) {
		$country = WC()->countries->countries[$order->billing_country];

		$array = [
			'name' => trim($order->billing_last_name . ' ' . $order->billing_first_name),
			'email' => $order->billing_email,
			'phone' => $order->billing_phone,
			'actualAddress' => trim($order->billing_postcode . ', ' . $order->billing_state . ', ' . $country . ', ' . $order->billing_city . ', ' . $order->billing_address_1 . ', ' . $order->billing_address_2, ', \t\n\r\0\x0B'),
			'tags' => $this->tags,
			'attributes' => []
		];

		if (!empty($order->billing_postcode))
			array_push($array['attributes'], $this->dataAttributes('postcode', $order->billing_postcode));

		if (!empty($order->billing_country))
			array_push($array['attributes'], $this->dataAttributes('country', $country));

		if (!empty($order->billing_first_name))
			array_push($array['attributes'], $this->dataAttributes('first_name', $order->billing_first_name));

		if (!empty($order->billing_last_name))
			array_push($array['attributes'], $this->dataAttributes('last_name', $order->billing_last_name));

		if (!empty($order->billing_state))
			array_push($array['attributes'], $this->dataAttributes('state', $order->billing_state));

		if (!empty($order->billing_city))
			array_push($array['attributes'], $this->dataAttributes('city', $order->billing_city));

		if (!empty($order->billing_address_1) || !empty($order->billing_address_2))
			array_push($array['attributes'], $this->dataAttributes('street', trim($order->billing_address_1 . ', ' . $order->billing_address_2, ', \t\n\r\0\x0B')));

		return $array;
	}

	/**
	 * @param  [string] $value 	Значение доп. поля
	 * @return [array] 			Массив дополнительных полей контрагента
	 */
	private function dataAttributes($type, $value) {
		$array = $this->attributes[$type];

		$array['value'] = $value;

		return $array;
	}


}