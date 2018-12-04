<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['EFFECT_OPEN_MENU'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_EFFECT_OPEN_MENU'),
	'TYPE' => 'STRING',
	'DEFAULT' => 'fadeIn',
);
$arTemplateParameters['EFFECT_OPEN_SUBMENU'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_EFFECT_OPEN_SUBMENU'),
	'TYPE' => 'STRING',
	'DEFAULT' => 'fadeIn',
);
$arTemplateParameters['HOME_OPEN'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_HOME_OPEN'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);
$arTemplateParameters['MIN_HEIGHT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MSG_MIN_HEIGHT'),
	'TYPE' => 'STRING',
	'DEFAULT' => '',
);