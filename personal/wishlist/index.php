<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->IncludeComponent(
	"unisoftmedia:wishlist.list", 
	"wishlist", 
	array(
		"COMPONENT_TEMPLATE" => "wishlist",
		"PATH_TO_WISHLIST" => SITE_DIR."personal/wishlist/",
		"MAX_WIDTH_WISHLIST" => "70",
		"MAX_HEIGHT_WISHLIST" => "70",
		"SHOW_PRODUCTS" => "N"
	),
	false,
	Array('HIDE_ICONS' => 'Y')
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");