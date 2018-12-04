<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Огромный выбор акустических систем Hi-Fi/Hi-End компонентов, представленных на современном рынке");
$APPLICATION->SetPageProperty("keywords", "Огромный выбор акустических систем Hi-Fi/Hi-End компонентов, представленных на современном рынке");
$APPLICATION->SetPageProperty("description", "ПУЛЬТ.BY - Техника для вашего дома");

$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");

$theme->Template()->requireTemplate();
global $USER;
//$USER->Authorize(1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");