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
if (isset($arCurrentValues['USE_CAROUSEL']) && $arCurrentValues['USE_CAROUSEL'] == 'Y')
{
	$arTemplateParameters['CAROUSEL_ITEMS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CAROUSEL_ITEMS'),
		'TYPE' => 'STRING',
		'DEFAULT' => '3',
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
//if ($boolCatalog)
//{
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
//}