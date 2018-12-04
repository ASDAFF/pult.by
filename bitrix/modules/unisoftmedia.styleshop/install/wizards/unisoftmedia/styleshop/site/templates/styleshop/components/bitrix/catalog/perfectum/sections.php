<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

?>
<h1 class="pagetitle"><?$APPLICATION->ShowTitle(false)?></h1>
<?$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/sections_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("TOP")));?>
<?$APPLICATION->IncludeComponent(
	'bitrix:catalog.section.list',
	'sections',
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => "2",
        "SET_TITLE" => "N",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
	),
	$component
);?>
<?$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/sections_bottom.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("BOTTOM")));?>