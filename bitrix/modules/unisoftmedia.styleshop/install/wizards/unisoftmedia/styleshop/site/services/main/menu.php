<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

	CModule::IncludeModule('fileman');
	//$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);

    $arMenuTypes['left'] = GetMessage("WIZ_MENU_left");
	$arMenuTypes['top'] = GetMessage("WIZ_MENU_top");
    $arMenuTypes['personal'] = GetMessage("WIZ_MENU_personal");

    $arMenuTypes['footer_information'] = GetMessage("WIZ_MENU_footer_information");
	$arMenuTypes['footer_menu'] = GetMessage("WIZ_MENU_footer_menu");

	SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
	//COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);