<?
// Отправка в Bitrix24 заказа при доставке с оплатой при получении
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSaved');
function OnSaleOrderSaved(\Bitrix\Main\Event $event) {
	// Данные для доступа к API Bitrix24
	$bitrix24URL = 'https://cnscoins.bitrix24.ru/rest/8/xxxxxxxxxxxxxxxx/';
	$method = 'crm.deal.add';

	$order = $event->getParameter("ENTITY");

	$isNew = $event->getParameter("IS_NEW");
	$delivery_id = $order->getField('DELIVERY_ID');
	$check = $isNew;

	if (!$check) return;

	$properties = $order->getPropertyCollection()->toArray();

	$paymentSystemId = $order->getPaymentCollection()->current()->getPaymentSystemId();
	$paymentSystem = \Bitrix\Sale\PaySystem\Manager::getObjectById($paymentSystemId);

	$shipmentCollection = $order->getShipmentCollection();
	foreach ($shipmentCollection as $shipment) {
		// Проверяем, является ли текущая отгрузка доставкой
		if ($shipment->isSystem()) {
			continue; // Пропускаем системные отгрузки, если они есть
		}
		$deliveryService = $shipment->getDelivery(); // Получаем службу доставки
		$deliveryName = $deliveryService->getName(); // Получаем название текущей доставки
		$deliveryPrice = $shipment->getPrice(); // Получаем цену текущей доставки
	}

	$basket = $order->getBasket(); // Получаем списко товаров
	$productsInfo = '';
	foreach ($basket as $basketItem) {
		$productName = $basketItem->getField('NAME');
		$productQuantity = $basketItem->getQuantity();

		$productInfo = "$productName - $productQuantity x" . $basketItem->getPrice() . " = " . $basketItem->getFinalPrice() . "\n"; // Формируем информацию о товаре для поля UF_CRM_PRODUCT

		$productsInfo .= $productInfo; // Добавляем информацию о товаре к общей строке
	}

	$discFull = $order->getDiscount()->getApplyResult();
	$coupon_name = '';
	foreach ($discFull['ORDER'] as $ord) {
		$coupon_name .= $ord['COUPON_ID'] . ' ';
	}

	$discout_value = $discFull['PRICES']['BASKET'][array_key_first($discFull['PRICES']['BASKET'])]['DISCOUNT'];

	$orderData = array(
		'FIELDS' => array(
			'TITLE' => 'Заказ ' . $order->getField('ACCOUNT_NUMBER') . ' оформлен на сайте', // Заголовок заказа
			'OPPORTUNITY' => $order->getPrice(), // Сумма заказа

			// Кастомные поля для Bitrix24
			'UF_CRM_6303F5B88C089' => $paymentSystem->getField('NAME') . ': ' . $paymentSystemId, // ID оплаты   UF_CRM_PAYMENTID
			// 'UF_CRM_630378E0B3D98' => $paymentSystem->getField('NAME'), // Система оплаты                       UF_CRM_PAYMENTSYSTEM
			'UF_CRM_630378E0CF1F6' => $order->getField('ACCOUNT_NUMBER'), // Номер заказа                       UF_CRM_ORDERID
			'UF_CRM_630378E0E0345' => $productsInfo, // Информация о продукте                                   UF_CRM_PRODUCT
			'UF_CRM_630378E1256E9' => $deliveryName, // Тип доставки                                            UF_CRM_DELIVERY
			'UF_CRM_630378E138871' => $deliveryPrice, // Стоимость доставки                                     UF_CRM_DELIVERYPRICE
			'UF_CRM_630385BF8103E' => $coupon_name . ': ' . $discout_value, // Скидка                                     UF_CRM_DISCOUNT
			'UF_CRM_630385BF99172' => $order->getPrice() - $deliveryPrice + $order->getDiscountPrice(), // цена без скидки и без доставки UF_CRM_SUBTOTAL
			'UF_CRM_1706255186177' => $_SERVER['HTTP_ORIGIN'] . "/bitrix/admin/sale_order_detail.php?ID=" . $order->getId(), // ссылка на заказ ORDER_LINK
		)
	);

	foreach ($properties as $property) {
		switch ($property['CODE']) {
			case 'FIO':
				$orderData['FIELDS']['UF_CRM_1708516335204'] = $property['VALUE']; // ФИО покупателя
				break;
			case 'EMAIL':
				$orderData['FIELDS']['UF_CRM_1708516389641'] = $property['VALUE']; // Email покупателя
				break;
			case 'PHONE':
				$orderData['FIELDS']['UF_CRM_1708516377188'] = $property['VALUE']; // Телефон покупателя
				break;
			case 'ADDRESS':
				$orderData['FIELDS']['UF_CRM_630378E147D0F'] = $property['VALUE']; // Адрес доставки UF_CRM_DELIVERYADDRE
				break;
		}
	}

	// комментарий
	$orderData['FIELDS']['COMMENTS'] = 'Комментарий: ' . $order->getField('USER_DESCRIPTION');

	// Формирование запроса к API Bitrix24
	$queryUrl = $bitrix24URL . $method;
	$queryData = http_build_query($orderData);

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryUrl,
		CURLOPT_POSTFIELDS => $queryData,
	));

	$result = curl_exec($curl);
	curl_close($curl);

	// Обработка результата
	if ($result === false) {
		$orderData['message'] = 'Ошибка выполнения запроса к API Bitrix24';
	} else {
		$resultArray = json_decode($result, true);
		if ($resultArray && array_key_exists('result', $resultArray)) {
			$orderData['message'] = 'Заказ успешно создан в Bitrix24';
		} else {
			$orderData['message'] = 'Ошибка при создании заказа в Bitrix24';
		}
	}

	// $file = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/lead_order.json';
	// $current = file_get_contents($file);
	// $current .= 'создан ' . $delivery_id . PHP_EOL;
	// $current .= json_encode($orderData, JSON_UNESCAPED_UNICODE) . PHP_EOL . $queryUrl .'/?' . $queryData . PHP_EOL . json_encode($resultArray, JSON_UNESCAPED_UNICODE) . PHP_EOL;
	// file_put_contents($file, $current);
}
