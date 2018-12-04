<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_2"));
CJSCore::Init("jquery");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
global $MCART_IS_SKU;
$MCART_IS_SKU = false;
global $DB;
$db_type=strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/classes/general/profile2.php");
if((CModule::IncludeModule('catalog'))&&(CModule::IncludeModule('sale')))
	$MCART_IS_SKU = true;

	$firstColumn = $_REQUEST['firstColumn'];
	$firstRow = $_REQUEST['firstRow'];
	$titleRow = $_REQUEST['titleRow'];
	$highestColumn = $_REQUEST['highestColumn'];

	if (!$CML2_LINK_CODE = $_REQUEST['cml2_link_code'])
		$CML2_LINK_CODE = "";

	if ($_REQUEST["make_translit_code"]=="Y")
		$MAKE_TRANSLIT_CODE = 1;
	else
		$MAKE_TRANSLIT_CODE =0;

	if ($_REQUEST['save_profile']=="Y")
		{
		$IS_SAVE_PROFILE = true;
		$PROFILE_NAME = addslashes($_REQUEST['profile_name']);
		}
	else
		$IS_SAVE_PROFILE = false;

	/*
	if (!(isset($_REQUEST['fld_identify'])))
	{
		ShowError(GetMessage("WRONG_IDENTIFY_CODE"));
		die();
	}
	*/
$i = 0;
foreach(range(ord($firstColumn), ord($highestColumn)) as $v)
	{
		$alfavit[chr($v)] = $i;
		$i++;
	}

/*
$tmpColumns = $_REQUEST['columns'];
if ((count($tmpColumns[$_REQUEST['fld_identify']])==0)&&($_REQUEST['fld_identify']!==$_REQUEST['fld_name']))
	{
ShowError(GetMessage("WRONG_IDENTIFY_CODE"));
die();
	}
*/

$XLS_GLOBALS = $_REQUEST["XLS_GLOBALS"];

foreach ($XLS_GLOBALS['xls']  as $key_i=>$one_el)
{
	$key = $one_el;
	$code = $XLS_GLOBALS['bx'][$key_i];
	if ($code=="0")
		{
			continue;
		}
	$type = $XLS_GLOBALS['modify_type'][$key_i];
	$subaction = $XLS_GLOBALS['modify_subaction'][$key_i];
	$params = $XLS_GLOBALS['modify_subaction_params'][$key_i];
	$XLS_GLOBALS_EX[$key][$code] = array("action"=>$type, "subaction"=>$subaction, "params"=>$params);
}

$_SESSION["MCART_XLS_ARRAY"] =
array
(
	'COLUMNS'=>$XLS_GLOBALS_EX,
	//'ACTION_MODIFY'=>array('TO_INT'=>$arrToInt, 'TO_LINK'=>$arrToLink, 'TAKE_TWO'=>$arrTakeTwo, "TAKE_THREE"=>$arrTakeThree, "SUBACTION_PARAMS"=>$arrActionParams, "DEL_SUBSTR"=>$arrDelSubstr),
	'NAME_ID'=>$_REQUEST['fld_name'],
	'IDENTIFY'=>$_REQUEST['XLS_IDENTIFY'],
	'INPUT_FILENAME'=>$_REQUEST['xls_input_filename'],
	"SELECTED_FIELDS" => $XLS_GLOBALS['bx'],
	'IBLOCK_ID'=>$_REQUEST['xls_iblock_id'],
	'SECTION'=>$_REQUEST['xls_iblock_section_id'],
	'SECTION_FOR_NEW'=>$_REQUEST['xls_iblock_section_id_new'],
	'CATALOG_PRICE_BASE_ID'=>$_REQUEST['catalog_base_price_id'],
	'SKU_IBLOCK_ID'=>(intval($_REQUEST['sku_iblock_id'])>0 ? $_REQUEST['sku_iblock_id'] : 0),
	'CML2_LINK_CODE'=>(intval($CML2_LINK_CODE)>0 ? $CML2_LINK_CODE : 0),
	"FIRST_ROW"=>$firstRow,
	"HIGHEST_ROW"=>IntVal($_REQUEST["xls_highest_row"]),
	"DIAPAZONE_A"=>$firstColumn,
	"DIAPAZONE_Z"=>$highestColumn,
	"SHEET_ID" =>$_SESSION['ARR_XLS_DATA']["SHEET_ID"],
	"LAST_ROW_TYPE"=>$_SESSION['ARR_XLS_DATA']["LAST_ROW_TYPE"],
	'ERR_COUNT'=>0,
	'UPDATE_COUNT'=>0,
	'ADD_COUNT'=>0,
	"MAKE_TRANSLIT_CODE"=>$MAKE_TRANSLIT_CODE
);
	//echo "<pre>"; var_dump($_SESSION["MCART_XLS_ARRAY"]); echo "</pre>";

if ($IS_SAVE_PROFILE)
{
	$arrData = array(
		"NAME"=>$PROFILE_NAME,
		"IBLOCK_ID"=>$_REQUEST['xls_iblock_id'],
		"SECTION_ID"=>$_REQUEST['xls_iblock_section_id'],
		"IDENTIFY" => $_REQUEST['XLS_IDENTIFY'],
		"DATA_ROW"=>$firstRow,
		"TITLE_ROW"=>$titleRow,
		"DIAPAZONE_A"=>$firstColumn,
		"DIAPAZONE_Z"=>$highestColumn,
		"FIELDS"=>$_REQUEST['XLS_GLOBALS'],
		"SHEET_ID" =>$_SESSION['ARR_XLS_DATA']["SHEET_ID"],
		"SKU_IBLOCK_ID" => (intval($_REQUEST['sku_iblock_id'])>0 ? $_REQUEST['sku_iblock_id'] : 0),
		"CML2_LINK_CODE" =>(intval($CML2_LINK_CODE)>0 ? $CML2_LINK_CODE : 0),
		"SECTION_NEW" =>$_REQUEST['xls_iblock_section_id_new'],
		"NEED_TRANSLIT"=>$MAKE_TRANSLIT_CODE
	);

	$new_profile_id = CMcartXlsProfile2::AddRows(
		'main_profile',
		array('worksheet', 'iblock_id', 'row_first', 'row_title', 'column_firsl', 'column_last',
				'need_offer', 'need_translit', 'section_id', 'section_id_new', 'name'),
		array($arrData["SHEET_ID"], $arrData["IBLOCK_ID"], $arrData["DATA_ROW"], $arrData["TITLE_ROW"], "'".$arrData["DIAPAZONE_A"]."'", "'".$arrData["DIAPAZONE_Z"]."'",
				$arrData["SKU_IBLOCK_ID"], $arrData["NEED_TRANSLIT"], $arrData["SECTION_ID"], $arrData["SECTION_NEW"], "'".$arrData["NAME"]."'"
			)
	);

	CMcartXlsProfile2::AddRows('mcart_profile_property',
	array('profile_id', 'column_litera', 'field_code', 'action', 'subaction', 'params', 'identify'),
	array($new_profile_id,
	"'".$arrData["IDENTIFY"]['xls']."'",
	"'".$arrData["IDENTIFY"]['bx']."'",
	"'".$arrData["IDENTIFY"]['modify_type']."'",
	"'".$arrData["IDENTIFY"]['modify_subaction']."'",
	"'".$arrData["IDENTIFY"]['modify_subaction_params']."'",
	1)
	);

	foreach ($arrData["FIELDS"]['xls'] as $key=>$xls)
	{
		CMcartXlsProfile2::AddRows('mcart_profile_property',
			array('profile_id', 'column_litera', 'field_code', 'action', 'subaction', 'params', 'identify'),
			array($new_profile_id,
			"'".$xls."'",
			"'".$arrData["FIELDS"]['bx'][$key]."'",
			"'".$arrData["FIELDS"]['modify_type'][$key]."'",
			"'".$arrData["FIELDS"]['modify_subaction'][$key]."'",
			"'".$arrData["FIELDS"]['modify_subaction_params'][$key]."'",
			0)
			);
	}
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/admin/mcart_xls_import_step_3.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>