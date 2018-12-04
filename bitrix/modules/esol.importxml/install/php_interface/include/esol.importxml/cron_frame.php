<?
@set_time_limit(0);
define('NOT_CHECK_PERMISSIONS', true);
if(!ini_get('date.timezone') && function_exists('date_default_timezone_set')){@date_default_timezone_set("Europe/Moscow");}
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/../../../..');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
@set_time_limit(0);
CModule::IncludeModule("iblock");
CModule::IncludeModule('catalog');
CModule::IncludeModule("currency");
$module_id = 'esol.importxml';
CModule::IncludeModule($module_id);
$PROFILE_ID = $argv[1];

/*Remove old dirs*/
\Bitrix\EsolImportxml\Utils::RemoveTmpFiles(0);
/*/Remove old dirs*/

if(strlen($PROFILE_ID)==0)
{
	echo date('Y-m-d H:i:s').": profile id is not set\r\n";
	die();
}

$oProfile = new \Bitrix\EsolImportxml\Profile();
$oProfile->Apply($SETTINGS_DEFAULT, $SETTINGS, $PROFILE_ID);
$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
$params['MAX_EXECUTION_TIME'] = (isset($MAX_EXECUTION_TIME) && (int)$MAX_EXECUTION_TIME > 0 ? $MAX_EXECUTION_TIME : 0);

$needCheckSize = (bool)(COption::GetOptionString($module_id, 'CRON_NEED_CHECKSIZE', 'N')=='Y');
$needImport = true;
if($needCheckSize)
{
	$arProfileFields = $oProfile->GetFieldsByID($PROFILE_ID);
	$checkSum = $arProfileFields['FILE_HASH'];
}

$fileSum = '';
$DATA_FILE_NAME = $params['URL_DATA_FILE'];
if($params['EXT_DATA_FILE'] || $params['EMAIL_DATA_FILE'])
{
	$newFileId = 0;
	$fileLink = '';
	if($params['EMAIL_DATA_FILE'])
	{
		if($newFileId = \Bitrix\EsolImportxml\SMail::GetNewFile($params['EMAIL_DATA_FILE']))
		{
			$arFile = CFile::GetFileArray($newFileId);
			$fileLink = $_SERVER["DOCUMENT_ROOT"].$arFile['SRC'];
			$fileSum = md5_file($fileLink);
		}
		elseif($checkSum)
		{
			 $fileSum = $checkSum;
		}
	}
	else
	{
		$arFile = \Bitrix\EsolImportxml\Utils::MakeFileArray($params['EXT_DATA_FILE']);
		$fileSum = (file_exists($arFile['tmp_name']) ? md5_file($arFile['tmp_name']) : '');
	}
	
	if($needCheckSize && $checkSum && $checkSum==$fileSum)
	{
		$needImport = false;
	}
	else
	{
		if(!$newFileId && $arFile)
		{
			$newFileId = \Bitrix\EsolImportxml\Utils::SaveFile($arFile, 'kda.importexcel');
		}
	}
	
	if($newFileId > 0)
	{
		$arFile = CFile::GetFileArray($newFileId);
		$DATA_FILE_NAME = $arFile['SRC'];
			
		if($params['DATA_FILE']) CFile::Delete($params['DATA_FILE']);
		
		$SETTINGS_DEFAULT['DATA_FILE'] = $newFileId;
		$SETTINGS_DEFAULT['URL_DATA_FILE'] = $DATA_FILE_NAME;
		$oProfile->Update($PROFILE_ID, $SETTINGS_DEFAULT, $SETTINGS);
	}
}

if(!file_exists($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME))
{
	if(defined("BX_UTF")) $DATA_FILE_NAME = $APPLICATION->ConvertCharsetArray($DATA_FILE_NAME, LANG_CHARSET, 'CP1251');
	else $DATA_FILE_NAME = $APPLICATION->ConvertCharsetArray($DATA_FILE_NAME, LANG_CHARSET, 'UTF-8');
}
if(!file_exists($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME))
{
	echo date('Y-m-d H:i:s').": file not exists\r\n";
	die();
}

$arParams = array();
//$pid = false;
$pid = $PROFILE_ID;
if(COption::GetOptionString($module_id, 'CRON_CONTINUE_LOADING', 'N')=='Y')
{
	//$pid = $PROFILE_ID;
	$oProfile = new \Bitrix\EsolImportxml\Profile();
	$arParams = $oProfile->GetProccessParamsFromPidFile($PROFILE_ID);
	if($arParams===false)
	{
		echo date('Y-m-d H:i:s').": import in process\r\n";
		die();
	}
}
if(!is_array($arParams)) $arParams = array();
if(empty($arParams) && !$needImport)
{
	echo date('Y-m-d H:i:s').": file is loaded\r\n";
	die();
}

$ie = new \Bitrix\EsolImportxml\Importer($DATA_FILE_NAME, $params, $EXTRASETTINGS, $arParams, $pid);
$arResult = $ie->Import();

if(COption::GetOptionString($module_id, 'CRON_REMOVE_LOADED_FILE', 'N')=='Y')
{
	if(file_exists($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME))
	{
		unlink($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);
	}
	
	if($params['EXT_DATA_FILE'] && is_file($params['EXT_DATA_FILE']))
	{
		unlink($params['EXT_DATA_FILE']);
	}
}
echo date('Y-m-d H:i:s').": import complete\r\n".CUtil::PhpToJSObject($arResult)."\r\n";
?>