<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"PATH_TO_WISHLIST" => array(
			"NAME" => GetMessage("MSG_PATH_TO_WISHLIST"),
			"TYPE" => "STRING",
			"DEFAULT" => '={SITE_DIR."personal/wishlist/"}',
			"PARENT" => "BASE",
		),
		"MAX_WIDTH_WISHLIST" => array(
			"NAME" => GetMessage("MSG_MAX_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => '70',
			"PARENT" => "BASE",
		),
		"MAX_HEIGHT_WISHLIST" => array(
			"NAME" => GetMessage("MSG_MAX_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => '70',
			"PARENT" => "BASE",
		),
		// LIST
		"SHOW_PRODUCTS" => array(
			"NAME" => GetMessage("SBBL_SHOW_PRODUCTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "LIST",
		),
	)
);