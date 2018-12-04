<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if (!Loader::includeModule('iblock'))
	return;
$boolCatalog = Loader::includeModule('catalog');

$arSKU = false;
$boolSKU = false;
if ($boolCatalog && (isset($arCurrentValues['IBLOCK_ID']) && 0 < intval($arCurrentValues['IBLOCK_ID'])))
{
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

if ($boolSKU)
{
	$arDisplayModeList = array(
		'N' => GetMessage('CP_BCS_TPL_DML_SIMPLE'),
		'Y' => GetMessage('CP_BCS_TPL_DML_EXT')
	);
	$arTemplateParameters['PRODUCT_DISPLAY_MODE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_PRODUCT_DISPLAY_MODE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'Y',
		'DEFAULT' => 'N',
		'VALUES' => $arDisplayModeList
	);
}

if (isset($arCurrentValues['IBLOCK_ID']) && 0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$arAllPropList = array();
	$arFilePropList = array(
		'-' => GetMessage('CP_BCS_TPL_PROP_EMPTY')
	);
	$arListPropList = array(
		'-' => GetMessage('CP_BCS_TPL_PROP_EMPTY')
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
	}

	$arTemplateParameters['ADD_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFilePropList
	);

	$arTemplateParameters['LABEL_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_LABEL_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arListPropList
	);

	if ($boolSKU && isset($arCurrentValues['PRODUCT_DISPLAY_MODE']) && 'Y' == $arCurrentValues['PRODUCT_DISPLAY_MODE'])
	{
		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			'-' => GetMessage('CP_BCS_TPL_PROP_EMPTY')
		);
		$arTreeOfferPropList = array(
			'-' => GetMessage('CP_BCS_TPL_PROP_EMPTY')
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
			'NAME' => GetMessage('CP_BCS_TPL_OFFER_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFileOfferPropList
		);
		$arTemplateParameters['OFFER_TREE_PROPS'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BCS_TPL_OFFER_TREE_PROPS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arTreeOfferPropList
		);
	}
}

if ($boolCatalog)
{
	$arTemplateParameters['PRODUCT_SUBSCRIPTION'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_PRODUCT_SUBSCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	);
	$arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	);
	$addToBasketActions = array(
		'ADD' => GetMessage('ADD_TO_BASKET_ACTION_ADD'),
		'BUY' => GetMessage('ADD_TO_BASKET_ACTION_BUY')
	);
	$arTemplateParameters['ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BCS_TPL_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N'
	);
	$arTemplateParameters['SHOW_CLOSE_POPUP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_SHOW_CLOSE_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCE_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADDED_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_SUBSCRIBE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCE_TPL_MESS_BTN_SUBSCRIBE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_SUBSCRIBE_DEFAULT')
);
if (isset($arCurrentValues['DISPLAY_COMPARE']) && isset($arCurrentValues['DISPLAY_COMPARE']) == 'Y')
{
	$arTemplateParameters['MESS_BTN_COMPARE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCS_TPL_MESS_BTN_COMPARE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_BTN_COMPARE_DEFAULT')
	);
}
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE_DEFAULT')
);
$arTemplateParameters['USE_SKU_TOOLTIP'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_USE_SKU_TOOLTIP'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
);
$arTemplateParameters['USE_CAROUSEL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('USE_CAROUSEL'),
	'TYPE' => 'CHECKBOX',
	"REFRESH" => "Y",
	'DEFAULT' => 'N',
);
$arListResponsiveCarousel = array(
	'1' => '1',
	'2' => '2',
	'3' => '3',
	'4' => '4',
	'5' => '5'
);
$arListResponsive = array(
	'12' => '1',
	'6' => '2',
	'4' => '3',
	'3' => '4'
);
if (isset($arCurrentValues['USE_CAROUSEL']) && $arCurrentValues['USE_CAROUSEL'] == 'Y')
{
	$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_LG'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_LG'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsiveCarousel,
		'DEFAULT' => '5',
	);
	$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_MD'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_MD'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsiveCarousel,
		'DEFAULT' => '4',
	);
	$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_SM'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_SM'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsiveCarousel,
		'DEFAULT' => '3',
	);
	$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_XS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_XS'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsiveCarousel,
		'DEFAULT' => '2',
	);
	$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_MOBILE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_MOBILE'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsiveCarousel,
		'DEFAULT' => '2',
	);
	$arTemplateParameters['SLIDER_AUTOPLAY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_AUTOPLAY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SLIDER_SPEED'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_SPEED'),
		'TYPE' => 'STRING',
		'DEFAULT' => '1000',
	);
	$arTemplateParameters['SLIDER_SHOW_SPEED'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_SHOW_SPEED'),
		'TYPE' => 'STRING',
		'DEFAULT' => '8000',
	);
	$arTemplateParameters['SLIDER_AUTOPLAY_HOVER_PAUSE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_AUTOPLAY_HOVER_PAUSE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['MOUSE_DRAG'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('MOUSE_DRAG'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);		
	$arTemplateParameters['SLIDER_NAV'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_NAV'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SLIDER_LOOP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SLIDER_LOOP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}
else
{
	$arTemplateParameters['RESPONSIVE_ITEMS_LG'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_ITEMS_LG'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsive,
		'DEFAULT' => '3',
	);
	$arTemplateParameters['RESPONSIVE_ITEMS_MD'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_ITEMS_MD'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsive,
		'DEFAULT' => '3',
	);
	$arTemplateParameters['RESPONSIVE_ITEMS_SM'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_ITEMS_SM'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsive,
		'DEFAULT' => '4',
	);
	$arTemplateParameters['RESPONSIVE_ITEMS_XS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('RESPONSIVE_ITEMS_XS'),
		'TYPE' => 'LIST',
		'VALUES' => $arListResponsive,
		'DEFAULT' => '6',
	);
}
$arTemplateParameters['MESS_TITLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_TITLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_MESS_TITLE_DEFAULT')
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
$arTemplateParameters['USE_QUICKVIEW'] = array(
	'NAME' => GetMessage('USE_QUICKVIEW'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);
$arTemplateParameters['USE_FAVORITES'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('USE_FAVORITES'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);