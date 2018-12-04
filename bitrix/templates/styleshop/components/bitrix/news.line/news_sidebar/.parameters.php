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