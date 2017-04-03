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


use MoySklad\Entity\Customerorder; // указывать с файлом!!!
use MoySklad\Dev\Dev;
use MoySklad\Includes\Curl;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('autoload.php'); // require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );


class MoySklad {
	const // protected: only php 7.1
		ORGANIZATION = '13942c3b-586c-11e5-90a2-8ecb0037b09d';

	public
		$mediaType = 'application/json';

	function __construct() { // последовательность выполнения в конструкторе важна
		add_action('woocommerce_order_items_table', [$this, 'customerorder']); // создаём заказ

	}

	public function customerorder($order) {
		$stateId = '13a21e7b-586c-11e5-90a2-8ecb0037b0b7'; // id статуса заказа "Новый"

		$data = [
			'name'         => (string) $order->id, // or $order->get_order_number()
			'moment'       => $order->order_date, // Дата Заказа
			'description'  => $order->customer_note, // Комментарий Заказа покупателя
			'state'        => $this->meta('state', '/customerorder/metadata/states/' . $stateId), // Статус Заказа в формате Метаданных
			'organization' => $this->meta('organization', '/organization/' . self::ORGANIZATION), // Ссылка на ваше юрлицо в формате Метаданных
			'agent'        => $this->meta('counterparty', '/counterparty/4ef2f677-e8e1-11e5-7a69-97110007f2b2'), // Ссылка на контрагента (покупателя) в формате Метаданных
			'attributes'   => $this->attributes(), // Источник заказа только один - "Сайт"
			"positions" => [] // Ссылка на позиции в Заказе в формате Метаданных
		];

		foreach ($order->get_items() as $item_id => $item) :
			$_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);

			array_push($data['positions'], [
				'quantity' => (int) $item['qty'],
				'price'    => (int) $_product->get_price()*100, // Цена товара/услуги в копейках
				'reserve'  => (int) $item['qty'],
				'assortment' => $this->meta('product', '/product/' . get_field('moysklad_id', $item['product_id']))
			]);
		endforeach;

		// echo '<pre>';
		// var_dump(get_field('moysklad_id', $item['product_id'])); //
		// echo '</pre>';

		new Curl('/entity/' . __FUNCTION__, 'post', $data);
	}

	private function meta($type, $url) {
		return [
			'meta' => [
				'href' => Curl::END_POINT . '/entity' . $url,
				'type' => $type,
				'mediaType' => $this->mediaType
			]
		];
	}

	/**
	 * Отмечаем дополнительное поле в заказе "Источник заказа" -> "Сайт"
	 * GET https://online.moysklad.ru/api/remap/1.1/entity/customentity/e2f7dbb8-e9ce-11e5-7a69-8f55001c001b для получения пользовательского справочника (всех значений этого дополнительного поля "Источник заказа")
	 * GET https://online.moysklad.ru/api/remap/1.1/entity/customentity/e2f7dbb8-e9ce-11e5-7a69-8f55001c001b/064e93cc-e9cf-11e5-7a69-9711001c49b7 для получения сущности пользовательского справочника "Сайт"
	 * customentity это "Пользовательский справочник"
	 * @url https://online.moysklad.ru/api/remap/1.1/doc/index.html#пользовательский-справочник-пользовательские-справочники
	 * @return [array]
	 */
	private function attributes() { // Источник заказа только один - "Сайт"
		$id             = '064e93cc-e9cf-11e5-7a69-9711001c49b7'; // это id сущности пользовательского справочника "Сайт"
		$customentityId = 'e2f7dbb8-e9ce-11e5-7a69-8f55001c001b'; // это пользовательского справочника "Источник заказа" (customEntityMeta id дополнительного поля (attributes) "Источник заказа")
		$metaId         = 'f24606de-e9ce-11e5-7a69-9711001c4539'; // это meta id дополнительного поля (attributes) "Источник заказа"
		$meta           = $this->meta('customentity', $customentityId . '/' . $id);

		$meta['meta']['metadataHref'] = Curl::END_POINT . '/entity/companysettings/metadata/customEntities/' . $customentityId;

		return [[
			'id' => $metaId,
			'value' => $meta // это метаднные варианта "Сайт" дополнительного поля (attributes) "Источник заказа"
		]];
	}
}


new MoySklad();