<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $theme,$ElementID;
$column = $theme->Option()->get('detail_column', '', SITE_ID);

$ajax = false;
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']))
{
	$ajax = true;
}

if($ajax && isset($_REQUEST['ajax_add2basket']) && !empty($_REQUEST['ajax_add2basket']) && isset($_REQUEST['elementId']) && 0 < intval($_REQUEST['elementId']))
{
	$APPLICATION->RestartBuffer();
	$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "popup", array(
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"COLUMNS_LIST" => array(
			0 => "NAME",
			2 => "PRICE",
			3 => "QUANTITY",
			4 => "SUM",
			5 => "PROPS"
		),
		"AJAX_MODE" => "N",
		"ELEMENT_ID" => intval($_REQUEST['elementId']),
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"BASKET_URL" => $arParams["BASKET_URL"],
		"HIDE_COUPON" => "N",
		"QUANTITY_FLOAT" => "N",
		"PRICE_VAT_SHOW_VALUE" => "Y",
		"TEMPLATE_THEME" => "site",
		"SET_TITLE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"OFFERS_PROPS" => $arParams['OFFER_TREE_PROPS'],
		),
		$component
	);
	die;
}

if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y')
{
	$basketAction = (isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? array($arParams['COMMON_ADD_TO_BASKET_ACTION']) : array());
}
else
{
	$basketAction = (isset($arParams['DETAIL_ADD_TO_BASKET_ACTION']) ? $arParams['DETAIL_ADD_TO_BASKET_ACTION'] : array());
}



$ajax_element = false;
/************ AJAX QUICK VIEW **************/
if($ajax && 'Y' == $_REQUEST['ajax_quickview'])
{
    $APPLICATION->RestartBuffer();
    $ajax_quickview = true;
}

ob_start();
    $ElementID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.element",
		"catalog",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
			"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
			"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
			"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
			"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],
			"SHOW_DEACTIVATED" => $arParams["SHOW_DEACTIVATED"],

			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

			"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
			"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],

			'COLUMN' => $column,

			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'LABEL_PROP' => $arParams['LABEL_PROP'],
			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
			'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
			'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
			'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
			'MESS_BTN_ADDED_TO_BASKET' => $arParams['MESS_BTN_ADDED_TO_BASKET'],
			'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
			'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
			'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
			'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
			"USE_COMMENTS" => $arParams['DETAIL_USE_COMMENTS'],
			"USE_REVIEW" => $arParams['USE_REVIEW'],
			"FORUM_ID" => $arParams['FORUM_ID'],
			'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
			'BRAND_USE' => $arParams['DETAIL_BRAND_USE'],
			'BRAND_PROP_CODE' => $arParams['DETAIL_BRAND_PROP_CODE'],
			'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
			'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
			"PROP_CODE_ARTNUMBER" => $arParams['DETAIL_PROP_CODE_ARTNUMBER'],
			"FILTER_NAME" => $arParams['FILTER_NAME'],
			"PROP_CODE_DOCUMENTATIONS" =>  $arParams['PROP_CODE_DOCUMENTATIONS'],
			"USE_ZOOM" => $arParams['USE_ZOOM'],
			"CAROUSEL_DOTS_VERTICAL" => $arParams['CAROUSEL_DOTS_VERTICAL'],
			"SKU_VIEW" => $arParams['SKU_VIEW'],
			"PARAMS" => $arParams,
			"AJAX" => $ajax,
			"AJAX_QUICKVIEW" => $ajax_quickview,
			"USE_CATALOG_CREDIT" => $arParams['USE_CATALOG_CREDIT'],
			"LINK_CATALOG_CREDIT" => $arParams['LINK_CATALOG_CREDIT'],
			"DISPLAY_PROPERTIES_CHARACTERISTICS" => $arParams['DETAIL_LIST_PROPERTY_CODE_CHARACTERISTICS'],
			'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
			'ADD_TO_BASKET_ACTION' => $basketAction,
			"DISPLAY_COMPARE" => $arParams['USE_COMPARE'],
			'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
			'BACKGROUND_IMAGE' => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
			'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
			'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),

			"USE_FAVORITES" => $arParams['USE_FAVORITES'],

			"USE_GIFTS_DETAIL" => $arParams['USE_GIFTS_DETAIL']?: 'Y',
			"USE_GIFTS_MAIN_PR_SECTION_LIST" => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST']?: 'Y',
			"GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
			"GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
			"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
			"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
			"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
			"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
			"GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
			"GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
			"GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

			"ONECLICK_USE_MASK" => $arParams['ONECLICK_USE_MASK'],
			"ONECLICK_MASK_PHONE" => $arParams['ONECLICK_MASK_PHONE'],
			// bu one click
			"USE_ONECLICK" => $arParams['USE_ONECLICK'],
			"ONECLICK_USE_CAPTCHA" => $arParams['ONECLICK_USE_CAPTCHA'],
			"ONECLICK_MESS_TITLE" => $arParams['ONECLICK_MESS_TITLE'],
			"ONECLICK_OK_TEXT" => $arParams['ONECLICK_OK_TEXT'],
			"ONECLICK_INCLUDE_FIELDS" => $arParams['ONECLICK_INCLUDE_FIELDS'],
			"ONECLICK_EMAIL_TO" => $arParams['ONECLICK_EMAIL_TO'],
			"ONECLICK_REQUIRED_FIELDS" => $arParams['ONECLICK_REQUIRED_FIELDS'],
			"ONECLICK_EVENT_MESSAGE_ID" => $arParams['ONECLICK_EVENT_MESSAGE_ID'],
			"ONECLICK_USER_CONSENT" => $arParams['ONECLICK_USER_CONSENT'],
			"ONECLICK_USER_CONSENT_ID" => $arParams['ONECLICK_USER_CONSENT_ID'],
			"ONECLICK_USER_CONSENT_IS_CHECKED" => $arParams['ONECLICK_USER_CONSENT_IS_CHECKED'],
			"ONECLICK_USER_AUTO_SAVE" => $arParams['ONECLICK_USER_AUTO_SAVE'],
			"ONECLICK_USER_IS_LOADED" => $arParams['ONECLICK_USER_IS_LOADED'],

			// store
			"USE_STORE" => $arParams['USE_STORE'],
			"STORE_PATH" => $arParams['STORE_PATH'],
			"MAIN_TITLE" => $arParams['MAIN_TITLE'],
			"USE_MIN_AMOUNT" =>  $arParams['USE_MIN_AMOUNT'],
			"MIN_AMOUNT" => $arParams['MIN_AMOUNT'],
			"STORES" => $arParams['STORES'],
			"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
			"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
			"USER_FIELDS" => $arParams['USER_FIELDS'],
			"FIELDS" => $arParams['FIELDS'],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			'STRICT_SECTION_CHECK' => (isset($arParams['DETAIL_STRICT_SECTION_CHECK']) ? $arParams['DETAIL_STRICT_SECTION_CHECK'] : ''),
			'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
			'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION']
		),
		$component
	);
$catalogElement = ob_get_contents();
ob_end_clean();


if($ajax_quickview) {
	echo $catalogElement;
	die;
}

?>
<section id="catalog-element">
			<?if($column == 'one_column') {
				?><!--col-md-12--><div class="col-md-12"><?

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Element Center column ****************************/

				/************************ Element ****************************/
				echo $catalogElement;
				/************************ /Element ****************************/

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Element Center column ****************************/

				?></div><!--/col-md-12--><?
			} else if($column == 'two_columns_right') {
				?><!--col-md-9--><div class="col-md-9"><?

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Element Center column ****************************/

				/************************ Element ****************************/
				echo $catalogElement;
				/************************ /Element ****************************/

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Element Center column ****************************/

				?></div><!--/col-md-9--><?

				?><!--col-md-3--><div class="sidebar col-md-3"><?

				/************************ Include Areas Element Left column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Element Left column ****************************/

			?></div><!--/col-md-3--><?
		} else if($column == 'two_columns_left') {
			?><!--col-md-9--><div class="col-md-9 float-md-right"><?

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Element Center column ****************************/

				/************************ Element ****************************/
				echo $catalogElement;
				/************************ /Element ****************************/

				/************************ Include Areas Element Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Element Center column ****************************/

				?></div><!--/col-md-9--><?

				?><!--col-md-3--><div class="sidebar col-md-3"><?

				/************************ Include Areas Element Left column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/detail_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Element Left column ****************************/

			?></div><!--/col-md-3--><?
		}
?></section><?