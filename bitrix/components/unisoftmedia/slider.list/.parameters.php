<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
{
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arSorts = Array(
	"ASC" => GetMessage("T_IBLOCK_DESC_ASC"),
	"DESC" => GetMessage("T_IBLOCK_DESC_DESC"),
);

$arSortFields = Array(
		"ID" => GetMessage("T_IBLOCK_DESC_FID"),
		"NAME" => GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM" => GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT" => GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X" => GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCKS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"MULTIPLE" => "Y",
		),
		"NEWS_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"SORT_BY1"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_BY2"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER2"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SLIDER_HEIGHT"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MSG_SLIDER_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "500"
		),
		"SLIDER_HEIGHT_MOBILE"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MSG_SLIDER_HEIGHT_MOBILE"),
			"TYPE" => "STRING",
			"DEFAULT" => "40"
		),
		"SLIDER_FULL_SCREEN"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MSG_SLIDER_FULL_SCREEN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SLIDER_AUTOPLAY"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_AUTOPLAY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SLIDER_SPEED"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "1000"
		),
		"SLIDER_SHOW_SPEED"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_SHOW_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "8000"
		),
		"SLIDER_AUTOPLAY_HOVER_PAUSE"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_AUTOPLAY_HOVER_PAUSE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"MOUSE_DRAG"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MOUSE_DRAG"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"SLIDER_NAV"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_NAV"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SLIDER_DOTS"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_DOTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SLIDER_LOOP"  =>  Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SLIDER_LOOP"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		)
	),
);
?>
