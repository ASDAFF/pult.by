<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Склады");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.store", 
	".default", 
	array(
		"SEF_MODE" => "Y",
		"PHONE" => "Y",
		"SCHEDULE" => "Y",
		"SET_TITLE" => "Y",
		"TITLE" => "Наши магазины",
		"MAP_TYPE" => "0",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_NOTES" => "",
		"SEF_FOLDER" => "#SITE_DIR#about/store/",
		"COMPONENT_TEMPLATE" => ".default",
		"SEF_URL_TEMPLATES" => array(
			"liststores" => "index.php",
			"element" => "#store_id#",
		)
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>