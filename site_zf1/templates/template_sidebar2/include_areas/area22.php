<?$APPLICATION->IncludeComponent(
	"bitrix:sender.subscribe",
	"sidebar",
	Array(
		"AJAX_MODE" => "Y",
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
	)
);?>