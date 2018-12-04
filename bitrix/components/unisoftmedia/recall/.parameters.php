<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "RECALL_FORM", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"USER_CONSENT" => array(),
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("MFP_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"MESS_TITLE" => Array(
			"NAME" => GetMessage("MFP_MESS_TITLE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_MESS_TITLE_DEFAULT"),
			"PARENT" => "BASE",
		),
		"POPUP_FORM" => Array(
			"NAME" => GetMessage("MFP_POPUP_FORM"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
			"REFRESH" => "Y"
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("MFP_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"), 
			"PARENT" => "BASE",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TO"),
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")),
			"PARENT" => "BASE",
		),
		"INCLUDE_FIELDS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCS_INCLUDE_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => array(
				"NAME" => GetMessage('CP_BCS_INCLUDE_FIELDS_NAME'),
				"EMAIL" => GetMessage('CP_BCS_INCLUDE_FIELDS_EMAIL'),
				"PHONE" => GetMessage('CP_BCS_INCLUDE_FIELDS_PHONE'),
				"MESSAGE" => GetMessage('CP_BCS_INCLUDE_FIELDS_MESSAGE')
			),
			"DEFAULT" => ""
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("MFP_REQUIRED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => Array("NONE" => GetMessage("MFP_ALL_REQ"), "NAME" => GetMessage("MFP_NAME"), "EMAIL" => "E-mail",  "PHONE" => GetMessage('CP_BCS_INCLUDE_FIELDS_PHONE'), "MESSAGE" => GetMessage("MFP_MESSAGE")),
			"DEFAULT"=>"", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TEMPLATES"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
		"USE_MASK" => Array(
			"NAME" => GetMessage("MFP_USE_MASK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N", 
			"PARENT" => "BASE",
			"REFRESH" => "Y"
		),
		"USE_ONECLICK" => Array(
			"NAME" => GetMessage("MFP_USE_ONECLICK"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N", 
			"PARENT" => "BASE",
			"REFRESH" => "Y"
		)
	)
);
if (isset($arCurrentValues['USE_MASK']) && $arCurrentValues['USE_MASK'] == 'Y')
{
	$arComponentParameters['PARAMETERS']['MASK_PHONE'] = Array(
		"NAME" => GetMessage("MFP_MASK_PHONE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("MFP_MASK_PHONE_DEFAULT"),
		"PARENT" => "BASE"
	);
}
if (isset($arCurrentValues['USE_ONECLICK']) && $arCurrentValues['USE_ONECLICK'] == 'Y')
{
	/*$arComponentParameters['PARAMETERS']['ONECLICK_ORDER'] = Array(
		"NAME" => GetMessage("MFP_ONECLICK_ORDER"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N", 
		"PARENT" => "BASE"
	);*/
	$arComponentParameters['PARAMETERS']['ELEMENT_ID'] = Array(
		"NAME" => GetMessage("MFP_ELEMENT_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => '={$_REQUEST["PRODUCT_ID"]}',
		"PARENT" => "BASE"
	);
}
if (isset($arCurrentValues['POPUP_FORM']) && $arCurrentValues['POPUP_FORM'] == 'N')
{
	$arComponentParameters['PARAMETERS']['MODE_AJAX'] = Array(
		"NAME" => GetMessage("MFP_MODE_AJAX"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N", 
		"PARENT" => "BASE"
	);
}


?>