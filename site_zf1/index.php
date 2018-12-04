<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");

$theme->Template()->requireTemplate();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");