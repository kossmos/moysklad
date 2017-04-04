<?php

namespace MoySklad\Includes;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Config {


	function __construct() {}

	/**
	 * Дополнительные поля контрагентов
	 * @return [array] [массив дополнительных полей контрагентов]
	 */
	public function attributes() {
		return [
			'postcode' => [
				'id' => 'f2bf7baa-e9ed-11e5-7a69-8f5500249a0d',
				'name' => 'Почтовый индекс',
				'type' => 'string'
			],
			'country' => [
				'id' => 'aa412a68-32e4-11e6-7a69-8f5500007b7f',
				'name' => 'Страна',
				'type' => 'string'
			],
			'first_name' => [
				'id' => 'a8b368b9-36d3-11e6-7a69-8f55002f157b',
				'name' => 'Имя',
				'type' => 'string'
			],
			'last_name' => [
				'id' => 'a8b36a3e-36d3-11e6-7a69-8f55002f157c',
				'name' => 'Фамилия',
				'type' => 'string'
			],
			'state' => [
				'id' => 'ba441c88-36d3-11e6-7a69-8f55002f1b45',
				'name' => 'Регион',
				'type' => 'string'
			],
			'city' => [
				'id' => 'ba441e20-36d3-11e6-7a69-8f55002f1b46',
				'name' => 'Город',
				'type' => 'string'
			],
			'street' => [
				'id' => 'ba441f5b-36d3-11e6-7a69-8f55002f1b47',
				'name' => 'Улица',
				'type' => 'string'
			],
		];
	}


}