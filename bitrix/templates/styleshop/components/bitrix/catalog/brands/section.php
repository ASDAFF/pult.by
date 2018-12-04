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

global $arrBrandFilter;
$arrBrandFilter = array("PROPERTY_".current($arParams['DETAIL_BRAND_PROP_CODE'])."" => $arResult["VARIABLES"]["SECTION_CODE"]);

$arFilter = array(
	'IBLOCK_ID' => $arParams['IBLOCK_ID'],
	'CODE' => current($arParams['DETAIL_BRAND_PROP_CODE']),
	'PROPERTY_TYPE' => 'S:directory',
	'SECTION_CODE' => $arResult["VARIABLES"]["SECTION_CODE"],
	'ACTIVE' => 'Y'
);

$obCache = new CPHPCache();
if ($obCache->InitCache(360000, serialize($arFilter), "/iblock/catalog"))
{
	$arBrand = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
	$arBrand = array();
	if (Loader::includeModule('highloadblock'))
	{
		$propList = array();

		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			$arFilter
		);
		while ($prop = $rsProps->Fetch())
		{
			$prop['ID'] = (int)$prop['ID'];
			$prop['USER_TYPE'] = (string)$prop['USER_TYPE'];
			if (!isset($prop['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($prop['USER_TYPE_SETTINGS']['TABLE_NAME']))
				continue;
			$prop['VALUE'] = false;
			$propList = $prop;
		}


		$reqParams = array();

		if(!empty($propList))
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
						array('filter' => array('=TABLE_NAME' => $propList['USER_TYPE_SETTINGS']['TABLE_NAME']))
					)->fetch();

			if (isset($hlblock['ID']))
			{
				if (!isset($reqParams[$hlblock['ID']]))
				{
					$reqParams[$hlblock['ID']] = array(
						'HLB' => $hlblock
					);
				}
				if ($prop['VALUE'] === false)
				{
					$reqParams[$hlblock['ID']]['VALUES'] = false;
				}
				else
				{
					$reqParams[$hlblock['ID']]['VALUES'] = (
						isset($reqParams[$hlblock['ID']]['VALUES'])
						? array_merge($reqParams[$hlblock['ID']]['VALUES'], $prop['VALUE'])
						: $prop['VALUE']
					);
				}
			}

			foreach ($reqParams as &$params)
			{
				$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($params['HLB']);
				$entityDataClass = $entity->getDataClass();
				$fieldsList = $entityDataClass::getMap();
				if (count($fieldsList) === 1 && isset($fieldsList['ID']))
					$fieldsList = $entityDataClass::getEntity()->getFields();

				$arFilter = array(
						'limit' => 1
					);

					$arFilter['filter'] = array(
						'=UF_LINK' => $arResult["VARIABLES"]["SECTION_CODE"]
					);

					$rsPropEnums = $entityDataClass::getList($arFilter);
					if ($arEnum = $rsPropEnums->fetch())
					{
						$boolPict = true;
						if (isset($arEnum['UF_FILE']) && (int)$arEnum['UF_FILE'] > 0)
						{
							if (!isset($arEnum['UF_FILE']) || (int)$arEnum['UF_FILE'] <= 0)
								$boolPict = false;

							$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['UF_FILE']);
							if($boolPict)
							{
								$arEnum['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
									$arEnum['PREVIEW_PICTURE'],
									array("width" => 220, "height" => 220),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);

								$arEnum['PREVIEW_PICTURE']['SRC'] = $arEnum['PREVIEW_PICTURE']['src'];
								$arEnum['PREVIEW_PICTURE']['WIDTH'] = $arEnum['PREVIEW_PICTURE']['width'];
								$arEnum['PREVIEW_PICTURE']['HEIGHT'] = $arEnum['PREVIEW_PICTURE']['height'];
							}
						}

						$arBrand = array(
							'NAME' => (isset($arEnum['UF_NAME']) ? $arEnum['UF_NAME'] : false),
							'DESCRIPTION' => (isset($arEnum['UF_DESCRIPTION']) && (string)$arEnum['UF_DESCRIPTION'] !== '' ? $arEnum['UF_DESCRIPTION'] : false),
							'FULL_DESCRIPTION' => (isset($arEnum['UF_FULL_DESCRIPTION']) && (string)$arEnum['UF_FULL_DESCRIPTION'] !== '' ? $arEnum['UF_FULL_DESCRIPTION'] : false),
							'PICT' => ($boolPict ? $arEnum['PREVIEW_PICTURE'] : false),
							'PARENT_NAME' => $propList['NAME'],
							'UF_XML_ID' => $arEnum['UF_XML_ID']
						);
					}
				
			}
		}
	}
	$obCache->EndDataCache($arBrand);
}
if (!isset($arBrand))
	$arBrand = array();

if(!empty($arBrand))
{
	$arrBrandFilter = array("PROPERTY_".current($arParams['DETAIL_BRAND_PROP_CODE'])."" => $arBrand['UF_XML_ID']);
	
	$APPLICATION->SetPageProperty("title", str_replace(array('%title%','%site_name%'), array($arBrand['NAME'],COption::GetOptionString("main", "site_name")), $arParams['PREFIX_TITLE']));

	if($arBrand['DESCRIPTION'])
		$APPLICATION->SetPageProperty("description", $arBrand['DESCRIPTION']);

	$APPLICATION->SetPageProperty("keywords", str_replace(array('%title%','%site_name%'), array($arBrand['NAME'],COption::GetOptionString("main", "site_name")), $arParams['PREFIX_KEYWORDS']));

  $APPLICATION->SetTitle(str_replace('%s%', $arBrand['NAME'], $arParams['PREFIX_NAME']));

  $APPLICATION->AddChainItem($arBrand['NAME'], str_replace('#SECTION_CODE#', $arResult['VARIABLES']['SECTION_CODE'], $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"]));
}


ob_start();
$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"sidebar",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
		"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
	),
	$component,
	array("HIDE_ICONS" => "Y")
);
$sectionList = ob_get_contents();
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
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => "arrBrandFilter",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
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

		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
		'HOVER_IMAGE' => $arParams["HOVER_IMAGE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
		'MESS_BTN_ADDED_TO_BASKET' => $arParams['MESS_BTN_ADDED_TO_BASKET'],

		"USE_SKU_TOOLTIP" => $arParams['USE_SKU_TOOLTIP'],

		'SHOW_CLOSE_POPUP' => $arParams['COMMON_SHOW_CLOSE_POPUP'],

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
    "SHOW_ALL_WO_SECTION" => "Y",
	),
	$component
);
?></div><?
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
			<!--row--><div class="row">
			<?
			if($column != 'one_column')
			{?>
				<div class="col-md-9 <?if($column == 'two_columns_right'):?>section-left<?elseif($column == 'two_columns_left'):?>section-right<?endif?>">
					<h1><?=$APPLICATION->ShowTitle(false)?></h1>
					<?if(!empty($arBrand) && $arBrand['DESCRIPTION'])
					{?>
						<div class="category-description-block hidden-xs row">
							<div class="col-sm-9 col-md-9 col-lg-10"><?php echo $arBrand['DESCRIPTION'] ?></div>
							<div class="col-sm-3 col-md-3 col-lg-2"><img src="<?php echo $arBrand['PICT']['SRC'] ?>" alt="<?php echo $arBrand['NAME'] ?>" /></div>
						</div>
					<?}?>
				</div>
			<?}
			if($column == 'one_column') {
				?><!--col-md-12--><div class="col-md-12"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-12--><?
			} else if($column == 'two_columns_right') {
				?><!--col-md-3--><div class="sidebar col-md-3 float-md-right"><?

				/************************ Include Areas Section Left column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Section Left column ****************************/

			?></div><!--/col-md-3--><?

			?><!--col-md-9--><div class="col-md-9"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ sectionList ****************************/
				echo $sectionList;
				/************************ sectionList ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-9--><?
		} else if($column == 'two_columns_left') {
			?><!--col-md-3--><div class="sidebar col-md-3"><?

			/************************ sectionList ****************************/
			echo $sectionList;
			/************************ sectionList ****************************/

				/************************ Include Areas Section Left column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_left.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_LEFT")));
				/************************ /Include Areas Section Left column ****************************/

			?></div><!--/col-md-3--><?

			?><!--col-md-9--><div class="col-md-9"><?

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center_top.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER_TOP")));
				/************************ /Include Areas Section Center column ****************************/

				/************************ Section ****************************/
				echo $catalogSection;
				/************************ /Section ****************************/

				/************************ Include Areas Section Center column ****************************/
				$APPLICATION->IncludeFile(SITE_DIR."catalog/include_areas/section_center.php",Array(),Array("MODE"=>"html","NAME"=> Loc::getMessage("SECT_SIDEBAR_CENTER")));
				/************************ /Include Areas Section Center column ****************************/

				?></div><!--/col-md-9--><?
		}
		?></div><!--/row--><?
?></section><?