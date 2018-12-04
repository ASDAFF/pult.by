<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_COMPARE_LIST_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_COMPARE_LIST_TEMPLATE_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"SORT" => 50,
	"PATH" => array(
		"ID" => "unisoftmedia",
		"CHILD" => array(
			"ID" => "catalog",
			"NAME" => GetMessage("T_IBLOCK_DESC_CATALOG"),
			"SORT" => 30,
		),
	),
);

?>