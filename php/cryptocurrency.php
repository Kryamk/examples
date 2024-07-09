<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arData
 * @var CAllMain $APPLICATION
 */

$sTemplateId = $arData['id'];
$arVisual = $arResult['VISUAL'];
?>

<?php
if (CModule::IncludeModule("iblock")) {
    $arSort = Array();
    $arSelect = array("ID", "IBLOCK_ID");
    $arFilter = array("IBLOCK_ID" => 54, 'CODE' => 'elementy');
    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $fields = $ob->GetFields();
        $property = $ob->GetProperties();
        $cript_key = $property['CRIPT_KEY']['VALUE'] ?? '';
    }
}

$response = null;
$bitcoin_price = '';
$bitcoin_percent = '';
$bitcoin_up = false;

function get_bitcoin_data($cript_key) {
    $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
    $parameters = ['symbol' => 'BTC'];
    $headers = [
        'Accepts: application/json',
        "X-CMC_PRO_API_KEY: {$cript_key}"
    ];

    $qs = http_build_query($parameters);
    $request = "{$url}?{$qs}";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $request,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => 1
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response);
}

if (!empty($cript_key)) {
    $response = get_bitcoin_data($cript_key);

    if ($response && isset($response->data->BTC->quote->USD)) {
        $bitcoin = $response->data->BTC->quote->USD;
        $bitcoin_price = number_format($bitcoin->price, 2);
        $bitcoin_percent = number_format($bitcoin->percent_change_24h, 2);
        $bitcoin_up = $bitcoin_percent > 0;
    }
}

?>

<div class="widget-view-desktop-10">
    <div class="cryptoro-header">
        <div class="cryptoro-container">
            <div class="header-wrapper">
                <div class="header-top">

                    <? if (!empty($response->data)) { ?>
                        <div class="header-rate">
                            <svg class="header-rate-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <path d="M24.292 17.2419C23.6979 19.6186 19.9987 18.4111 18.8487 18.0852L19.9029 13.9069C21.0912 14.2519 24.9054 14.7502 24.292 17.2419ZM18.3312 20.2319L17.1812 24.8511C18.5995 25.2152 22.9887 26.6144 23.6404 24.0077C24.3304 21.2861 19.7495 20.5769 18.3312 20.2319ZM38.5904 24.6402C36.022 34.9136 25.6337 41.1619 15.3604 38.5936C5.08703 36.0252 -1.15555 25.6369 1.40703 15.3636C2.01585 12.9219 3.0997 10.6241 4.59669 8.60138C6.09368 6.57866 7.97448 4.87066 10.1317 3.57492C12.2888 2.27919 14.6802 1.42111 17.169 1.04969C19.6579 0.678266 22.1955 0.800784 24.637 1.41024C34.8912 3.97858 41.1395 14.3669 38.5904 24.6402ZM24.2345 12.4311L25.097 8.98108L22.9887 8.50191L22.1454 11.8177C21.5895 11.6836 21.0337 11.5494 20.4587 11.4344L21.302 8.04191L19.2129 7.54358L18.3504 10.9744C17.8904 10.8594 17.4304 10.7636 17.0087 10.6486L14.1145 9.92024L13.5395 12.1627C13.5395 12.1627 15.1112 12.5269 15.0729 12.5461C15.9354 12.7569 16.0887 13.2936 16.0504 13.7727L13.6929 23.2219C13.597 23.4902 13.2904 23.8352 12.7345 23.7394C12.7537 23.7586 11.2012 23.3561 11.2012 23.3561L10.1662 25.7519L12.8879 26.4419C13.4054 26.5761 13.9037 26.7102 14.402 26.8252L13.5204 30.3136L15.6287 30.8502L16.4912 27.3811C17.0662 27.5344 17.622 27.6686 18.1587 27.8219L17.2962 31.2528L19.4045 31.7894L20.2862 28.3011C23.832 28.9719 26.5537 28.7036 27.6654 25.4644C28.6237 22.8769 27.6654 21.3436 25.7487 20.3661C27.1287 20.0019 28.1637 19.1394 28.4512 17.2611C28.8345 14.7119 26.8795 13.3511 24.2345 12.4311Z" fill="#F4821C" />
                            </svg>
                            <div class="header-rate-values">
                                <div class="header-rate-values__num">$<?= $bitcoin_price ?></div>
                                <div class="header-rate-values__percent <?= $bitcoin_up ? 'up' : 'down' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7">
                                        <path d="M4.65921 6.15245C4.69744 6.20797 4.74859 6.25336 4.80825 6.28472C4.86792 6.31608 4.93431 6.33246 5.00171 6.33246C5.06912 6.33246 5.13551 6.31608 5.19518 6.28472C5.25484 6.25336 5.30599 6.20797 5.34421 6.15245L9.09421 0.735782C9.13762 0.673305 9.16307 0.600128 9.16781 0.5242C9.17255 0.448273 9.15639 0.372499 9.12108 0.305112C9.08578 0.237725 9.03268 0.181301 8.96756 0.141972C8.90244 0.102643 8.82779 0.0819119 8.75171 0.0820318H1.25171C1.17581 0.0823453 1.10144 0.103343 1.03658 0.142766C0.971723 0.18219 0.918839 0.238548 0.883617 0.305779C0.848394 0.37301 0.832165 0.448571 0.836675 0.524336C0.841185 0.600101 0.866264 0.673204 0.909213 0.735782L4.65921 6.15245Z" />
                                    </svg>
                                    <?= $bitcoin_percent ?>% (1d)
                                </div>
                            </div>
                        </div>
                    <? } ?>

                </div>
            </div>
        </div>
    </div>
</div>