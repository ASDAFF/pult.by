<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if (!Loader::includeModule('iblock'))
	return;
$boolCatalog = Loader::includeModule('catalog');

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCT_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCT_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCT_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCT_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADDED_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_ADDED_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE_DEFAULT')
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
if ($boolCatalog)
{
	$arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCT_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCT_TPL_SHOW_OLD_PRICE'),
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
}
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
$arTemplateParameters['DISPLAY_COMPARE'] = array(
	'NAME' => GetMessage('CP_BCS_DISPLAY_COMPARE'),
	'TYPE' => 'CHECKBOX',
	"REFRESH" => "Y",
	'DEFAULT' => 'N',
);
if (isset($arCurrentValues['DISPLAY_COMPARE']) && $arCurrentValues['DISPLAY_COMPARE'] == 'Y')
{
	$arTemplateParameters['COMPARE_PATH'] = array(
		'NAME' => GetMessage('CP_BCS_COMPARE_PATH'),
		'TYPE' => 'STRING',
		'DEFAULT' => '={SITE_DIR."catalog/compare/"}'
	);
}