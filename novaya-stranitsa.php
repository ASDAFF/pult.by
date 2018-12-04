<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новая страница");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"sidebar2",
	Array(
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_TYPE" => "A",
		"COUNT_ELEMENTS" => "N",
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"])?$arParams["SECTIONS_HIDE_SECTION_NAME"]:"N"),
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_TYPE" => "catalog",
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_FIELDS" => array("",""),
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"SECTION_USER_FIELDS" => array("",""),
		"SHOW_PARENT_NAME" => "Y",
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
		"VIEW_MODE" => "LINE"
	),
$component,
Array(
	'HIDE_ICONS' => 'Y'
)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>