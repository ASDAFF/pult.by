<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"left",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "left",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "N"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sender.subscribe",
	"sidebar",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CONFIRMATION" => "Y",
		"MESS_DESC" => "Подпишитесь и получайте дополнительные скидки",
		"MESS_TITLE" => "Будь в курсе первым!",
		"PAGE" => SITE_DIR."personal/subscribe/",
		"SET_TITLE" => "N",
		"SHOW_HIDDEN" => "N",
		"USE_PERSONALIZATION" => "Y"
	),
false,
Array(
	'ACTIVE_COMPONENT' => 'N'
)
);?>