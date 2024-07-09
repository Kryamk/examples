<?
// не управляем активностью раздела при обмене товарами с 1С
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "skipSectionActivity_If1C");
function skipSectionActivity_If1C(&$arParams) {
	if (($_SERVER["SCRIPT_NAME"] == '/bitrix/admin/1c_exchange.php') || ($_SERVER["SCRIPT_NAME"] == '/bitrix/admin/1c_exchange_custom.php')) {
		unset($arParams["ACTIVE"]);
		unset($arParams["NAME"]);
		unset($arParams["DETAIL_TEXT"]);
		unset($arParams["PREVIEW_TEXT"]);

		if (CModule::IncludeModule('iblock')) {
			$arSort = array();
			$arSelect = array('NAME', 'CODE', 'DETAIL_PAGE_URL');
			$arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], 'ID' => $arParams['ID']);
			$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
			while ($ob = $res->GetNextElement()) {
				$fields = $ob->GetFields();
				$property = $ob->GetProperties();
			}

			if ($fields['CODE'] !== '') {
				unset($arParams["CODE"]);
			}

			if (isset($arParams['PROPERTY_VALUES']['1578'])) {
				$prop_id = $property['TSVET_KOMMERCHESKIY']['PROPERTY_VALUE_ID'];
				$prop_value = $property['TSVET_KOMMERCHESKIY']['VALUE'];
				$prop_desc = $property['TSVET_KOMMERCHESKIY']['DESCRIPTION'];

				$old_param = array($prop_id => array('VALUE' => $prop_value, 'DESCRIPTION' => $prop_desc));

				array_splice($arParams['PROPERTY_VALUES']['1578'], 0);

				$arParams['PROPERTY_VALUES']['1578'] = $old_param;

			}
		}
	}
}