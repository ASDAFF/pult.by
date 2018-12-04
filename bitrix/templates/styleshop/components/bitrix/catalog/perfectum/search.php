<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $theme;
$column = $theme->Option()->get('section_column', '', SITE_ID);

$filterTemplate = 'vertical';

if($column == 'one_column')
{
	$filterTemplate = 'horizontal';
}
else if($arParams['FILTER_VIEW_MODE'] == 'horizontal')
{
	$filterTemplate = $arParams['FILTER_VIEW_MODE'];
}

ob_start();
$arElements = $APPLICATION->IncludeComponent(
	"bitrix:search.page",
	".default",
	Array(
		"RESTART" => $arParams["RESTART"],
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
		"USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
		"arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"SHOW_WHERE" => "N",
		"USE_LANGUAGE_GUESS" => "Y",
		"arrWHERE" => array(),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => 50,
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "N",
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);
$searchPage = ob_get_contents();
ob_end_clean();

ob_start();
$APPLICATION->IncludeComponent(
	"unisoftmedia:catalog.sorter", 
	"catalog", 
	array(
		"COMPONENT_TEMPLATE" => "catalog",
		"OUTPUT_LIST_NUM_SHOW" => "Y",
		"OUTPUT_LIST_NUM" => array(
			0 => "5",
			1 => "20",
			2 => "40",
			3 => "80",
			4 => "100",
			5 => "",
		),
		"OUTPUT_LIST_NUM_DEFAULT" => "5",
		"OUTPUT_LIST_NUM_SHOW_ALL" => "N",
		"SORTERED_SHOW" => "Y",
		"SORTERED_ACCESS_OPTIONS" => array(
			0 => "name",
			1 => "popular",
			2 => "PROPERTY_MINIMUM_PRICE",
			3 => "",
		),
		"USE_VIEW" => "Y",
		"LIST_VIEW" => array(
			0 => "grid",
			1 => "list",
			2 => "table",
		),
		"VIEW_DEFAULT" => "grid",
		"IS_AJAX" => "Y"
	),
	null
);
$catalogSorter = ob_get_contents();
ob_end_clean();

global $intSectionID;

ob_start();
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && empty($_SERVER['HTTP_X_REQUESTED_WITH']))
{
	?><div id="ajax-section"><?
}
echo $catalogSorter;

if (!empty($arElements) && is_array($arElements))
{
	global $searchFilter;
	$searchFilter = array(
		"=ID" => $arElements,
	);

	?><div id="ajax-section-loader"><?

	$intSectionID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		View::getView(),
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => Sorter::getSort(),
			"ELEMENT_SORT_ORDER" => Sorter::getType(),
			"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
			"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"META_KEYWORDS" => "",
			"META_DESCRIPTION" => "",
			"BROWSER_TITLE" => "",
			"FILTER_NAME" => "searchFilter",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "N",
			"SET_TITLE" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"PAGE_ELEMENT_COUNT" => ListNum::getListNum(),
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			'HOVER_IMAGE' => $arParams["HOVER_IMAGE"],

			'LABEL_PROP' => $arParams['LABEL_PROP'],
			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
			'MESS_BTN_ADDED_TO_BASKET' => $arParams['MESS_BTN_ADDED_TO_BASKET'],

			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
			'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
			'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
			'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
			'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
			'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
			'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
			"MAX_WIDTH" => $arParams['MAX_WIDTH'],
			"MAX_HEIGHT" => $arParams['MAX_HEIGHT'],
			"USE_QUICKVIEW" => $arParams['USE_QUICKVIEW'],
			"ON_PREVIEW_TEXT" => $arParams['ON_PREVIEW_TEXT'],
			"USE_FAVORITES" => $arParams['USE_FAVORITES'],
			"FORUM_ID" => $arParams['FORUM_ID'],
			"USE_COMMENTS" => $arParams['DETAIL_USE_COMMENTS'],
			"DISPLAY_PROPERTIES_CHARACTERISTICS" => $arParams['LIST_PROPERTY_CODE_CHARACTERISTICS'],
			"IS_AJAX" => (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 'Y' : '',
			"COLUMN" => $column,
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			'STRICT_SECTION_CHECK' => (isset($arParams['DETAIL_STRICT_SECTION_CHECK']) ? $arParams['DETAIL_STRICT_SECTION_CHECK'] : ''),
			'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
			'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION']
		),
		$component
	);
	?></div><?
}
else
{
	echo '<p class="msg bg-danger">'.Loc::getMessage('CT_BCSE_NOT_FOUND').'</p>';
	echo '<style>.sorter{display:none;}</style>';
}
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && empty($_SERVER['HTTP_X_REQUESTED_WITH']))
{
	?></div><?
}
$catalogSection = ob_get_contents();
ob_end_clean();
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
isset($_REQUEST['is_unajax']) && $_REQUEST['is_unajax'] == 'Y')
{
	$APPLICATION->RestartBuffer();
	echo $catalogSection;
	die;
}

?>
<section id="catalog-section">
			<?
			if($column != 'one_column')
			{?>
				<div class="col-md-9 <?if($column == 'two_columns_right'):?>section-left<?elseif($column == 'two_columns_left'):?>section-right<?endif?>">
					<?$APPLICATION->ShowViewContent("picture");?>
					<h1><?=$APPLICATION->ShowTitle(false)?></h1>
					<?$APPLICATION->ShowViewContent("description");?>
				</div>
			<?}
			if($column == 'one_column') {
				?><!--col-md-12--><div class="col-md-12"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ Search Page ****************************/
			 	echo $searchPage;
				/************************ /Search Page ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-12--><?
			} else if($column == 'two_columns_right') {
				?><!--col-md-3--><div class="sidebar col-md-3 float-md-right"><?

				/************************ Include Areas Section Left column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Section Left column ****************************/

			?></div><!--/col-md-3--><?

			?><!--col-md-9--><div class="col-md-9"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ Search Page ****************************/
			 	echo $searchPage;
				/************************ /Search Page ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-9--><?
		} else if($column == 'two_columns_left') {
			?><!--col-md-3--><div class="sidebar col-md-3"><?

				/************************ Include Areas Section Left column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Section Left column ****************************/

			?></div><!--/col-md-3--><?

			?><!--col-md-9--><div class="col-md-9"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ Search Page ****************************/
			 	echo $searchPage;
				/************************ /Search Page ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-9--><?
		}
?></section><?