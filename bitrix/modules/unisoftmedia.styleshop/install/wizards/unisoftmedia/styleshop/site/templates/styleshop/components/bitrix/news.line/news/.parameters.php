<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTemplateParameters['MESS_TITLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_TITLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_MESS_TITLE_DEFAULT')
);
$arTemplateParameters['MESS_TITLE_LINK'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_TITLE_LINK'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
);
$arTemplateParameters['ALL_LINK'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_ALL_LINK'),
	'TYPE' => 'CHECKBOX',
	"REFRESH" => "Y",
	'DEFAULT' => 'N'
);
if (isset($arCurrentValues['ALL_LINK']) && $arCurrentValues['ALL_LINK'] == 'Y')
{
	$arTemplateParameters['MESS_ALL_LINK'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_ALL_LINK'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_MESS_ALL_LINK_DEFAULT')
);
}
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
$arTemplateParameters['HIDDEN_BUTTON_MORE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('HIDDEN_BUTTON_MORE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
);
$arTemplateParameters['MODE_VIEW'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MODE_VIEW'),
	'TYPE' => 'LIST',
	'VALUES' => array(
		'LEFT' => GetMessage('MODE_VIEW_LEFT'),
		'TOP' => GetMessage('MODE_VIEW_TOP')
	),
	'DEFAULT' => 'LEFT'
);
$arTemplateParameters["MAX_WIDTH"] = array(
	'PARENT' => 'VISUAL',
	"NAME" => GetMessage("MAX_WIDTH"),
	"TYPE" => "STRING",
	"DEFAULT" => "280",
);
$arTemplateParameters["MAX_HEIGHT"] = array(
	'PARENT' => 'VISUAL',
	"NAME" => GetMessage("MAX_HEIGHT"),
	"TYPE" => "STRING",
	"DEFAULT" => "200",
);
$arTemplateParameters["PREVIEW_TRUNCATE_LEN"] = array(
	'PARENT' => 'VISUAL',
	"NAME" => GetMessage("T_IBLOCK_DESC_PREVIEW_TRUNCATE_LEN"),
	"TYPE" => "STRING",
	"DEFAULT" => "",
);