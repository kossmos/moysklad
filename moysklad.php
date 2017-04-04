<?php
/*
Plugin Name: MoySklad
Plugin URI: https://github.com/kossmos/moysklad/
Description: Wordpress плагин для интеграции сервиса МойСклад (moysklad) с магазином на WooCommerce
Version: 0.1.0
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


use MoySklad\Entity\Counterparty; 	// Контрагенты
use MoySklad\Entity\Customerorder; 	// Заказы


if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('autoload.php');


class MoySklad {


	function __construct() { // последовательность выполнения в конструкторе важна
		add_action('woocommerce_order_items_table', [new Counterparty(), 'counterparty'], 5); // проверяем/обновляем/создаём пользователя
		add_action('woocommerce_order_items_table', [new Customerorder(), 'customerorder'], 10); // создаём заказ
	}


}


new MoySklad();