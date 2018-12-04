<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['MESS_TITLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_TITLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_MESS_TITLE_DEFAULT')
);
$arTemplateParameters['MESS_DESC'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_MESS_DESC'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_MESS_DESC_DEFAULT')
);