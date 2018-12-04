<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arSorter = array(
	"sort" => GetMessage("SORTERED_SORT"),
	"name" => GetMessage("SORTERED_NAME"),
	"popular" => GetMessage("SORTERED_POPULAR"),
	"catalog_price_1" => GetMessage("SORTERED_PRICE"),
);

$arList = array();
for($i = 5; 100 >= $i; $i = $i+5)
{
	$arList[$i] = $i;
}

$listView = array(
	"grid" => GetMessage("GRID"),
	"list" => GetMessage("LIST"),
	"table" => GetMessage("TABLE"),
);

$arComponentParameters = array(
"GROUPS" => array(
		"GROUPS_OUTPUT_LIST_NUM" => array(
			"NAME" => GetMessage("GROUPS_OUTPUT_LIST_NUM"),
		),
    "GROUPS_SETTINGS_SORTERED" => array(
			"NAME" => GetMessage("GROUPS_SETTINGS_SORTERED"),
		),
    "GROUPS_VIEW" => array(
			"NAME" => GetMessage("GROUPS_VIEW"),
		),
	),
	"PARAMETERS" => array(
		"SORTERED_SHOW" => array(
			"NAME" => GetMessage("SORTERED_SHOW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'GROUPS_SETTINGS_SORTERED',
			"REFRESH" => "Y",
		),
		"OUTPUT_LIST_NUM_SHOW" => array(
			"NAME" => GetMessage("OUTPUT_LIST_NUM_SHOW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'GROUPS_OUTPUT_LIST_NUM',
			"REFRESH" => "Y",
		),
	)
);
if('Y' == $arCurrentValues["SORTERED_SHOW"])
{
    $arComponentParameters["PARAMETERS"]["SORTERED_ACCESS_OPTIONS"] = array(
        "PARENT" => "GROUPS_SETTINGS_SORTERED",
        "NAME" => GetMessage("SORTERED_SORTER"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arSorter,
        "ADDITIONAL_VALUES" => "Y",
    );
}


/* VIEW */
$arComponentParameters["PARAMETERS"]["USE_VIEW"] = array(
			"NAME" => GetMessage("USE_VIEW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => "GROUPS_VIEW",
			"REFRESH" => "Y",
);
if('Y' === $arCurrentValues["USE_VIEW"])
{
    $arComponentParameters["PARAMETERS"]["LIST_VIEW"] = array(
        "PARENT" => "GROUPS_VIEW",
        "NAME" => GetMessage("VIEW_LIST"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $listView,
    );
    $arComponentParameters["PARAMETERS"]["VIEW_DEFAULT"] = array(
        "PARENT" => "GROUPS_VIEW",
        "NAME" => GetMessage("VIEW_DEFAULT"),
        "TYPE" => "LIST",
        "MULTIPLE" => "N",
        "VALUES" => $listView,
    );
}
/* VIEW */

if('Y' == $arCurrentValues["OUTPUT_LIST_NUM_SHOW"])
{
	$arComponentParameters["PARAMETERS"]["OUTPUT_LIST_NUM"] = array(
		"PARENT" => "GROUPS_OUTPUT_LIST_NUM",
		"NAME" => GetMessage("OUTPUT_LIST_NUM_VAL"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arList,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["OUTPUT_LIST_NUM_DEFAULT"] = array(
		"PARENT" => "GROUPS_OUTPUT_LIST_NUM",
		"NAME" => GetMessage("OUTPUT_LIST_NUM_VAL_DEFAULT"),
		"TYPE" => "LIST",
    "MULTIPLE" => "N",
    "VALUES" => $arList,
	);
    $arComponentParameters["PARAMETERS"]["OUTPUT_LIST_NUM_SHOW_ALL"] = array(
		"PARENT" => "GROUPS_OUTPUT_LIST_NUM",
		"NAME" => GetMessage("OUTPUT_LIST_NUM_SHOW_ALL"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
	);
}

$arComponentParameters["PARAMETERS"]["IS_AJAX"] = array(
	"NAME" => GetMessage("IS_AJAX"),
	"TYPE" => "CHECKBOX",
	"VALUE" => "Y",
	"DEFAULT" => "Y"
);