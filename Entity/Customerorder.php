<?php
/**
 * Заказы
 * @url https://online.moysklad.ru/api/remap/1.1/doc/index.html#документ-заказ-покупателя
 */

namespace MoySklad\Entity;


use MoySklad\Includes\Curl;


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Customerorder {
	const // protected: only php 7.1
		ORGANIZATION = '13942c3b-586c-11e5-90a2-8ecb0037b09d';

	public
		$mediaType = 'application/json';

	function __construct() {
		// echo 'Customerorder > ' . __NAMESPACE__ . ' > ' . __CLASS__;
	}

	public function customerorder($order) {
		$curl = new Curl();
		$stateId = '13a21e7b-586c-11e5-90a2-8ecb0037b0b7'; // id статуса заказа "Новый"

		$data = [
			'name'         => (string) $order->id, // or $order->get_order_number()
			'moment'       => $order->order_date, // Дата Заказа
			'description'  => $order->customer_note, // Комментарий Заказа покупателя
			'state'        => $this->meta('state', '/customerorder/metadata/states/' . $stateId), // Статус Заказа в формате Метаданных
			'organization' => $this->meta('organization', '/organization/' . self::ORGANIZATION), // Ссылка на ваше юрлицо в формате Метаданных
			'agent'        => $this->meta('counterparty', '/counterparty/' . Counterparty::$customerorderUserId), // Ссылка на контрагента (покупателя) в формате Метаданных
			'attributes'   => $this->attributes(), // Источник заказа только один - "Сайт"
			"positions"    => $this->positions($order) // Ссылка на позиции в Заказе в формате Метаданных
		];

		$result = $curl->init('/entity/' . __FUNCTION__, 'post', $data);
	}


	private function positions($order) {
		$array = [];

		foreach ($order->get_items() as $item_id => $item) :
			$_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);

			array_push($array, [
				'quantity' => (int) $item['qty'],
				'price'    => (int) $_product->get_price()*100, // Цена товара/услуги в копейках
				'reserve'  => (int) $item['qty'],
				'assortment' => $this->meta('product', '/product/' . get_field('moysklad_id', $item['product_id']))
			]);
		endforeach;

		return $array;
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




// echo '<pre>';
// var_dump($data);
// echo '</pre>';