<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>
<?
global $USER;
if(!$USER->isAuthorized())
{
    LocalRedirect(SITE_DIR.'auth/?backurl='.SITE_DIR.'personal/');
}
else
{
?>
	<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.section", 
	"personal", 
	array(
		"COMPONENT_TEMPLATE" => "personal",
		"SHOW_ACCOUNT_PAGE" => "Y",
		"SHOW_ORDER_PAGE" => "Y",
		"SHOW_PRIVATE_PAGE" => "Y",
		"SHOW_PROFILE_PAGE" => "Y",
		"SHOW_SUBSCRIBE_PAGE" => "Y",
		"SHOW_CONTACT_PAGE" => "Y",
		"SHOW_BASKET_PAGE" => "Y",
		"CUSTOM_PAGES" => "",
		"PATH_TO_PAYMENT" => SITE_DIR."personal/order/payment/",
		"PATH_TO_CONTACT" => SITE_DIR."about/contacts/",
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_CATALOG" => SITE_DIR."catalog/",
		"SEF_MODE" => "Y",
		"SHOW_ACCOUNT_COMPONENT" => "Y",
		"SHOW_ACCOUNT_PAY_COMPONENT" => "Y",
		"ACCOUNT_PAYMENT_SELL_CURRENCY" => "RUB",
		"ACCOUNT_PAYMENT_PERSON_TYPE" => "1",
		"ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS" => array(
			0 => "0",
		),
		"ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES" => "Y",
		"ACCOUNT_PAYMENT_SELL_TOTAL" => array(
			0 => "100",
			1 => "200",
			2 => "500",
			3 => "1000",
			4 => "5000",
			5 => "",
		),
		"ACCOUNT_PAYMENT_SELL_USER_INPUT" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"PROP_1" => array(
		),
		"PROP_2" => array(
		),
		"ORDER_HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"USE_AJAX_LOCATIONS_PROFILE" => "N",
		"COMPATIBLE_LOCATION_MODE_PROFILE" => "N",
		"SEND_INFO_PRIVATE" => "N",
		"CHECK_RIGHTS_PRIVATE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"PER_PAGE" => "20",
		"NAV_TEMPLATE" => "",
		"SET_TITLE" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SEF_FOLDER" => "#SITE_DIR#personal/",
		"SHOW_WISHLIST_PAGE" => "Y",
		"PATH_TO_WISHLIST" => SITE_DIR."personal/wishlist/",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"orders" => "orders/",
			"account" => "account/",
			"subscribe" => "subscribe/",
			"profile" => "profiles/",
			"profile_detail" => "profiles/#ID#",
			"private" => "private/",
			"order_detail" => "orders/#ID#",
			"order_cancel" => "cancel/#ID#",
		)
	),
	false
);?>

<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>