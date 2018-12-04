<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['SHOW_WISHLIST_PAGE'] = array(
	"NAME" => GetMessage("SPS_SHOW_WISHLIST_PAGE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"PARENT" => "BASE",
	"REFRESH" => "Y"
);
$arTemplateParameters['PATH_TO_WISHLIST'] = array(
	"NAME" => GetMessage("SPS_PATH_TO_WISHLIST"),
	"TYPE" => "STRING",
	"MULTIPLE" => "N",
	"DEFAULT" => "/personal/wishlist/",
	"PARENT" => "URL_TEMPLATES",
	"COLS" => 25
);