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