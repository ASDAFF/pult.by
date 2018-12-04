<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\UserConsent\Agreement;

if (!Loader::includeModule('iblock'))
	return;
$boolCatalog = Loader::includeModule('catalog');

$arSKU = false;
$boolSKU = false;
if ($boolCatalog && (isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID']) > 0)
{
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$arFilterViewModeList = array(
	"vertical" => GetMessage("CPT_BC_FILTER_VIEW_MODE_VERTICAL"),
	"horizontal" => GetMessage("CPT_BC_FILTER_VIEW_MODE_HORIZONTAL")
);

if (isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0)
{
	$arAllPropList = array();
	$arFilePropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arListPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arHighloadPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];
		$arAllPropList[$arProp['CODE']] = $strPropName;
		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFilePropList[$arProp['CODE']] = $strPropName;
		if ('L' == $arProp['PROPERTY_TYPE'])
			$arListPropList[$arProp['CODE']] = $strPropName;
		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			$arHighloadPropList[$arProp['CODE']] = $strPropName;
	}

	$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
	$arFilter = Array("TYPE_ID" => "RECALL_FORM", "ACTIVE" => "Y");
	if($site !== false)
		$arFilter["LID"] = $site;

	$arEvent = Array();
	$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
	while($arType = $dbType->GetNext())
		$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

	$arTemplateParameters['ADD_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFilePropList
	);
	$arTemplateParameters['LABEL_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_LABEL_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arListPropList
	);

	if ($boolSKU)
	{
		$arDisplayModeList = array(
			'N' => GetMessage('CP_BC_TPL_DML_SIMPLE'),
			'Y' => GetMessage('CP_BC_TPL_DML_EXT')
		);
		$arTemplateParameters['PRODUCT_DISPLAY_MODE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_PRODUCT_DISPLAY_MODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
			'VALUES' => $arDisplayModeList
		);
		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$arTreeOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
			$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
			if ('' == $arProp['CODE'])
				$arProp['CODE'] = $arProp['ID'];
			$arAllOfferPropList[$arProp['CODE']] = $strPropName;
			if ('F' == $arProp['PROPERTY_TYPE'])
				$arFileOfferPropList[$arProp['CODE']] = $strPropName;
			if ('N' != $arProp['MULTIPLE'])
				continue;
			if (
				'L' == $arProp['PROPERTY_TYPE']
				|| 'E' == $arProp['PROPERTY_TYPE']
				|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			)
				$arTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
		$arTemplateParameters['OFFER_ADD_PICT_PROP'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFileOfferPropList
		);
		$arTemplateParameters['OFFER_TREE_PROPS'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_OFFER_TREE_PROPS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arTreeOfferPropList
		);
	}
}

$arTemplateParameters['USE_FAVORITES'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('USE_FAVORITES'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters["FILTER_VIEW_MODE"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage('CPT_BC_FILTER_VIEW_MODE'),
	"TYPE" => "LIST",
	"VALUES" => $arFilterViewModeList,
	"DEFAULT" => "VERTICAL",
	"HIDDEN" => (!isset($arCurrentValues['USE_FILTER']) || 'N' == $arCurrentValues['USE_FILTER'])
);

$arTemplateParameters['DETAIL_DISPLAY_NAME'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_NAME'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);

$displayPreviewTextMode = array(
	'H' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_HIDE'),
	'E' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_EMPTY_DETAIL'),
	'S' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_SHOW')
);

$arTemplateParameters['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE'),
	'TYPE' => 'LIST',
	'VALUES' => $displayPreviewTextMode,
	'DEFAULT' => 'E'
);

if ($boolCatalog)
{
	$arTemplateParameters['USE_COMMON_SETTINGS_BASKET_POPUP'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_USE_COMMON_SETTINGS_BASKET_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);
	$useCommonSettingsBasketPopup = (
		isset($arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'])
		&& $arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y'
	);
	$addToBasketActions = array(
		'BUY' => GetMessage('ADD_TO_BASKET_ACTION_BUY'),
		'ADD' => GetMessage('ADD_TO_BASKET_ACTION_ADD')
	);
	$arTemplateParameters['COMMON_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_COMMON_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => ($useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['COMMON_SHOW_CLOSE_POPUP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_COMMON_SHOW_CLOSE_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['TOP_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_TOP_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['SECTION_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_SECTION_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['DETAIL_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'BUY',
		'REFRESH' => 'N',
		'MULTIPLE' => 'Y',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['DETAIL_SHOW_MAX_QUANTITY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_SHOW_MAX_QUANTITY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	if (isset($arCurrentValues['USE_PRODUCT_QUANTITY']) && $arCurrentValues['USE_PRODUCT_QUANTITY'] === 'Y')
	{
		$arTemplateParameters['DETAIL_SHOW_BASIS_PRICE'] = array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_SHOW_BASIS_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
		);
	}
}

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADDED_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_COMPARE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE_DEFAULT')
);
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE_DEFAULT')
);
$arTemplateParameters['USE_SKU_TOOLTIP'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_USE_SKU_TOOLTIP'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
);
$arTemplateParameters['HOVER_IMAGE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_HOVER_IMAGE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);
$arTemplateParameters['MAX_WIDTH'] = array(
    "NAME" => GetMessage("MAX_WIDTH"),
	'PARENT' => 'VISUAL',
	"TYPE" => "STRING",
	'DEFAULT' => '300',
);
$arTemplateParameters['MAX_HEIGHT'] = array(
    "NAME" => GetMessage("MAX_HEIGHT"),
	'PARENT' => 'VISUAL',
	"TYPE" => "STRING",
	'DEFAULT' => '300',
);
$arTemplateParameters['DETAIL_USE_VOTE_RATING'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_VOTE_RATING'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);
if (isset($arCurrentValues['DETAIL_USE_VOTE_RATING']) && 'Y' == $arCurrentValues['DETAIL_USE_VOTE_RATING'])
{
	$arTemplateParameters['DETAIL_VOTE_DISPLAY_AS_RATING'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_VOTE_DISPLAY_AS_RATING'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'rating' => GetMessage('CP_BC_TPL_DVDAR_RATING'),
			'vote_avg' => GetMessage('CP_BC_TPL_DVDAR_AVERAGE'),
		),
		'DEFAULT' => 'rating'
	);
}

$arTemplateParameters['DETAIL_USE_COMMENTS'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_COMMENTS'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);
if (isset($arCurrentValues['DETAIL_USE_COMMENTS']) && 'Y' == $arCurrentValues['DETAIL_USE_COMMENTS'])
{
	if (ModuleManager::isModuleInstalled("blog"))
	{
		$arTemplateParameters['DETAIL_BLOG_USE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);
		if (isset($arCurrentValues['DETAIL_BLOG_USE']) && $arCurrentValues['DETAIL_BLOG_USE'] == 'Y')
		{
			$arTemplateParameters['DETAIL_BLOG_URL'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BCE_DETAIL_TPL_BLOG_URL'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'catalog_comments'
			);
			$arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BCE_TPL_DETAIL_BLOG_EMAIL_NOTIFY'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N'
			);
		}
	}

	$boolRus = false;
	$langBy = "id";
	$langOrder = "asc";
	$rsLangs = CLanguage::GetList($langBy, $langOrder, array('ID' => 'ru',"ACTIVE" => "Y"));
	if ($arLang = $rsLangs->Fetch())
	{
		$boolRus = true;
	}

	if ($boolRus)
	{
		$arTemplateParameters['DETAIL_VK_USE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);

		if (isset($arCurrentValues['DETAIL_VK_USE']) && 'Y' == $arCurrentValues['DETAIL_VK_USE'])
		{
			$arTemplateParameters['DETAIL_VK_API_ID'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_API_ID'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'API_ID'
			);
		}
	}

	$arTemplateParameters['DETAIL_FB_USE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_FB_USE']) && 'Y' == $arCurrentValues['DETAIL_FB_USE'])
	{
		$arTemplateParameters['DETAIL_FB_APP_ID'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_APP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => ''
		);
	}
}

if (ModuleManager::isModuleInstalled("highloadblock"))
{
	$arTemplateParameters['DETAIL_BRAND_USE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_BRAND_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_BRAND_USE']) && 'Y' == $arCurrentValues['DETAIL_BRAND_USE'])
	{
		$arTemplateParameters['DETAIL_BRAND_PROP_CODE'] = array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_PROP_CODE"),
			"TYPE" => "LIST",
			"VALUES" => $arHighloadPropList,
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y"
		);
	}
}

$arTemplateParameters['USE_QUICKVIEW'] = array(
	'NAME' => GetMessage('USE_QUICKVIEW'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

if (isset($arCurrentValues['SHOW_TOP_ELEMENTS']) && 'Y' == $arCurrentValues['SHOW_TOP_ELEMENTS'])
{
	$arTopViewModeList = array(
		'BANNER' => GetMessage('CPT_BC_TPL_VIEW_MODE_BANNER'),
		'SLIDER' => GetMessage('CPT_BC_TPL_VIEW_MODE_SLIDER'),
		'SECTION' => GetMessage('CPT_BC_TPL_VIEW_MODE_SECTION')
	);
	$arTemplateParameters['TOP_VIEW_MODE'] = array(
		'PARENT' => 'TOP_SETTINGS',
		'NAME' => GetMessage('CPT_BC_TPL_TOP_VIEW_MODE'),
		'TYPE' => 'LIST',
		'VALUES' => $arTopViewModeList,
		'MULTIPLE' => 'N',
		'DEFAULT' => 'SECTION',
		'REFRESH' => 'Y'
	);
	if (isset($arCurrentValues['TOP_VIEW_MODE']) && ('SLIDER' == $arCurrentValues['TOP_VIEW_MODE'] || 'BANNER' == $arCurrentValues['TOP_VIEW_MODE']))
	{
		$arTemplateParameters['TOP_ROTATE_TIMER'] = array(
			'PARENT' => 'TOP_SETTINGS',
			'NAME' => GetMessage('CPT_BC_TPL_TOP_ROTATE_TIMER'),
			'TYPE' => 'STRING',
			'DEFAULT' => '30'
		);
	}
}
$arSkuViewList = array(
		'LIST' => GetMessage('MSG_SKU_VIEW_LIST'),
		'LIST2' => GetMessage('MSG_SKU_VIEW_LIST2'),
		'DROPDOWN_LIST' => GetMessage('MSG_SKU_VIEW_DROPDOWN_LIST'),
		'DROPDOWN_LIST2' => GetMessage('MSG_SKU_VIEW_DROPDOWN_LIST2')
	);
	$arTemplateParameters['SKU_VIEW'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('MSG_SKU_VIEW'),
		'TYPE' => 'LIST',
		'VALUES' => $arSkuViewList,
		'MULTIPLE' => 'N',
		'DEFAULT' => 'LIST',
		'REFRESH' => 'N'
	);
$arTemplateParameters['CAROUSEL_DOTS_VERTICAL'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('MSG_CAROUSEL_DOTS_VERTICAL'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'N'
);
/*****************buy one click*****************/
$arTemplateParameters['USE_ONECLICK'] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("MFP_USE_ONECLICK"), 
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y"
);
if ('Y' == $arCurrentValues['USE_ONECLICK'])
{
	$arTemplateParameters['ONECLICK_USE_MASK'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_USE_MASK"), 
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	if ('Y' == $arCurrentValues['ONECLICK_USE_MASK'])
	{
		$arTemplateParameters['ONECLICK_MASK_PHONE'] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("MFP_ONECLICK_MASK_PHONE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_ONECLICK_MASK_PHONE_DEFAULT")
		);
	}
	$arTemplateParameters['ONECLICK_USE_CAPTCHA'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_CAPTCHA"), 
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	);
	$arTemplateParameters['ONECLICK_MESS_TITLE'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_MESS_TITLE"), 
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("MFP_MESS_TITLE_DEFAULT")
	);
	$arTemplateParameters['ONECLICK_OK_TEXT'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_OK_MESSAGE"), 
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("MFP_OK_TEXT")
	);
	$arTemplateParameters['ONECLICK_EMAIL_TO'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_EMAIL_TO"), 
		"TYPE" => "STRING",
		"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from"))
	);
	$arTemplateParameters['ONECLICK_INCLUDE_FIELDS'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BCS_INCLUDE_FIELDS"), 
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => array(
			"NAME" => GetMessage('CP_BCS_INCLUDE_FIELDS_NAME'),
			"EMAIL" => GetMessage('CP_BCS_INCLUDE_FIELDS_EMAIL'),
			"PHONE" => GetMessage('CP_BCS_INCLUDE_FIELDS_PHONE'),
			"MESSAGE" => GetMessage('CP_BCS_INCLUDE_FIELDS_MESSAGE')
		),
		"DEFAULT" => ""
	);
	$arTemplateParameters['ONECLICK_REQUIRED_FIELDS'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_REQUIRED_FIELDS"), 
		"TYPE" => "LIST",
		"VALUES" => Array("NONE" => GetMessage("MFP_ALL_REQ"), "NAME" => GetMessage("MFP_NAME"), "EMAIL" => "E-mail",  "PHONE" => GetMessage('CP_BCS_INCLUDE_FIELDS_PHONE'), "MESSAGE" => GetMessage("MFP_MESSAGE")),
		"DEFAULT" => "", 
		"MULTIPLE"=>"Y",
		"COLS"=>25
	);
	$arTemplateParameters['ONECLICK_EVENT_MESSAGE_ID'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("MFP_EMAIL_TEMPLATES"), 
		"TYPE" => "LIST",
		"VALUES" => $arEvent,
		"DEFAULT" => "",
		"MULTIPLE"=>"Y",
		"COLS"=>25,
	);

	$arTemplateParameters['ONECLICK_USER_CONSENT'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('ONECLICK_USER_CONSENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if ('Y' == $arCurrentValues['ONECLICK_USER_CONSENT'])
	{
		$arTemplateParameters['ONECLICK_USER_CONSENT_ID'] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("COMP_MAIN_USER_CONSENT_PARAM_ID"), 
			"TYPE" => "LIST",
			"VALUES" => Agreement::getActiveList(),
			"MULTIPLE" => "N",
			"DEFAULT" => ""
		);

		$arTemplateParameters['ONECLICK_USER_CONSENT_IS_CHECKED'] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("COMP_MAIN_USER_CONSENT_PARAM_IS_CHECKED"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		);

		$arTemplateParameters['ONECLICK_USER_AUTO_SAVE'] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("COMP_MAIN_USER_CONSENT_PARAM_IS_AUTO_SAVE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		);

		$arTemplateParameters['ONECLICK_USER_IS_LOADED'] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("COMP_MAIN_USER_CONSENT_PARAM_IS_IS_LOADED"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		);
	}

}
/*****************zoom*****************/
$arTemplateParameters['USE_ZOOM'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('USE_ZOOM'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);
if ('Y' == $arCurrentValues['USE_ZOOM'])
{
   $arTemplateParameters['ZOOM'] = array(
    	'PARENT' => 'DETAIL_SETTINGS',
    	'NAME' => GetMessage('ZOOM'),
    	'TYPE' => 'LIST',
    	'MULTIPLE' => 'N',
    	'ADDITIONAL_VALUES' => 'N',
    	'REFRESH' => 'Y',
    	'VALUES' => array(
            'window' => GetMessage('ZOOM_WINDOW'),
            'inner' => GetMessage('ZOOM_INNER'),
            'lens' => GetMessage('ZOOM_LENS'),
        )
    );
    if ('window' == $arCurrentValues['ZOOM'])
    {
        $arTemplateParameters['ZOOM_WINDOW_POSITION'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_WINDOW_POSITION'),
        	'TYPE' => 'LIST',
        	'MULTIPLE' => 'N',
        	'ADDITIONAL_VALUES' => 'Y',
        	'REFRESH' => 'N',
        	'VALUES' => array(
                '1' => GetMessage('ZOOM_WINDOW_POSITION_ONE'),
                '2' => GetMessage('ZOOM_WINDOW_POSITION_TWO'),
                '3' => GetMessage('ZOOM_WINDOW_POSITION_THREE'),
                '4' => GetMessage('ZOOM_WINDOW_POSITION_FOUR'),
                '5' => GetMessage('ZOOM_WINDOW_POSITION_FIVE'),
                '6' => GetMessage('ZOOM_WINDOW_POSITION_SIX'),
                '7' => GetMessage('ZOOM_WINDOW_POSITION_SEVEN'),
                '8' => GetMessage('ZOOM_WINDOW_POSITION_EIGHT'),
                '9' => GetMessage('ZOOM_WINDOW_POSITION_NINE'),
                '10' => GetMessage('ZOOM_WINDOW_POSITION_TEN'),
                '11' => GetMessage('ZOOM_WINDOW_POSITION_ELEVEN'),
                '12' => GetMessage('ZOOM_WINDOW_POSITION_TWELVE'),
            )
        );
        $arTemplateParameters['ZOOM_WINDOW_OFFETX'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_WINDOW_OFFETX'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '0',
        );
        $arTemplateParameters['ZOOM_WINDOW_WIDTH'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_WINDOW_WIDTH'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '400',
        );
        $arTemplateParameters['ZOOM_WINDOW_HEIGHT'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_WINDOW_HEIGHT'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '400',
        );
    }
    $arTemplateParameters['ZOOM_MOUSEWHEELZOOM'] = array(
    	'PARENT' => 'DETAIL_SETTINGS',
    	'NAME' => GetMessage('ZOOM_MOUSEWHEELZOOM'),
    	'TYPE' => 'LIST',
    	'MULTIPLE' => 'N',
    	'ADDITIONAL_VALUES' => 'N',
    	'REFRESH' => 'N',
    	'VALUES' => array(
            '0' => GetMessage('ZOOM_MOUSEWHEELZOOM_OFF'),
            '1' => GetMessage('ZOOM_MOUSEWHEELZOOM_ON'),
        )
    );
    if ('window' == $arCurrentValues['ZOOM'] || 'inner' == $arCurrentValues['ZOOM'])
    {
        $arTemplateParameters['ZOOM_EASING'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_EASING'),
        	'TYPE' => 'LIST',
        	'MULTIPLE' => 'N',
        	'ADDITIONAL_VALUES' => 'N',
        	'REFRESH' => 'N',
        	'VALUES' => array(
                '0' => GetMessage('ZOOM_EASING_OFF'),
                '1' => GetMessage('ZOOM_EASING_ON'),
            )
        );
        $arTemplateParameters['ZOOM_CURSOR'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_CURSOR'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => 'default',
        );
    }
    if ('window' == $arCurrentValues['ZOOM'] || 'lens' == $arCurrentValues['ZOOM'])
    {
        $arTemplateParameters['ZOOM_LENS_BORDER_SIZE'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_BORDER_SIZE'),
        	'TYPE' => 'LIST',
        	'MULTIPLE' => 'N',
        	'ADDITIONAL_VALUES' => 'N',
        	'REFRESH' => 'N',
        	'VALUES' => array(
                '0' => GetMessage('ZOOM_LENS_BORDER_ZERO'),
                '1' => GetMessage('ZOOM_LENS_BORDER_ONE'),
                '2' => GetMessage('ZOOM_LENS_BORDER_TWO'),
                '3' => GetMessage('ZOOM_LENS_BORDER_THREE'),
                '4' => GetMessage('ZOOM_LENS_BORDER_FOUR'),
            )
        );
        $arTemplateParameters['ZOOM_LENS_BORDER_SIZE_COLOR'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_BORDER_SIZE_COLOR'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '#dcdcdc',
        );
        $arTemplateParameters['ZOOM_LENS_LENS_SHAPE'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_LENS_SHAPE'),
        	'TYPE' => 'LIST',
        	'MULTIPLE' => 'N',
        	'ADDITIONAL_VALUES' => 'N',
        	'REFRESH' => 'N',
        	'VALUES' => array(
                'square' => GetMessage('ZOOM_LENS_LENS_SHAPE_SQUARE'),
                'round' => GetMessage('ZOOM_LENS_LENS_SHAPE_ROUND'),
            )
        );
    }
    if ('lens' == $arCurrentValues['ZOOM'])
    {
        $arTemplateParameters['ZOOM_LENS_SIZE'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_SIZE'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '200',
        );
    }
    if ('window' == $arCurrentValues['ZOOM'])
    {
        $arTemplateParameters['ZOOM_LENS_BORDER'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_BORDER'),
        	'TYPE' => 'LIST',
        	'MULTIPLE' => 'N',
        	'ADDITIONAL_VALUES' => 'N',
        	'REFRESH' => 'N',
        	'VALUES' => array(
                '0' => GetMessage('ZOOM_LENS_BORDER_ZERO'),
                '1' => GetMessage('ZOOM_LENS_BORDER_ONE'),
                '2' => GetMessage('ZOOM_LENS_BORDER_TWO'),
                '3' => GetMessage('ZOOM_LENS_BORDER_THREE'),
                '4' => GetMessage('ZOOM_LENS_BORDER_FOUR'),
            )
        );
        $arTemplateParameters['ZOOM_LENS_COLOUR'] = array(
        	'PARENT' => 'DETAIL_SETTINGS',
        	'NAME' => GetMessage('ZOOM_LENS_COLOUR'),
        	'TYPE' => 'STRING',
        	'DEFAULT' => '#c9dae6',
        );
    }
}
/*****************zoom*****************/