<?
IncludeModuleLangFile(__FILE__);
$moduleID = 'alexkova.megametatags';
$className = 'CBIblockMegaMetaTags';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleID.'/classes/general/'.$className.'.php');

Class alexkova_megametatags extends CModule
{
	var $MODULE_ID = 'alexkova.megametatags';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_URI = 'http://kuznica74.ru/';

	function alexkova_megametatags()
	{
		$this->MODULE_ID = 'alexkova.megametatags';
		$this->MODULE_NAME = GetMessage("KZNC_METATAGS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KZNC_METATAGS_MOD_DESCR");
		$this->PARTNER_NAME = GetMessage("KZNC_METATAGS_PARTNER_NAME");
		$this->PARTNER_URI = 'http://kuznica74.ru/';
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
	}



	function InstallDB()
	{
		RegisterModule($this->MODULE_ID);

		//Default options
		COption::SetOptionString($this->MODULE_ID, "SHOW_DEBUGGING_INFO", 1);

		$h1Code = CBIblockMegaMetaTags::$proph1Code;
		$titleCode = CBIblockMegaMetaTags::$propTitleCode;
		$keywordsCode = CBIblockMegaMetaTags::$propKeyWordsCode;
		$descriptionCode = CBIblockMegaMetaTags::$propDescriptionCode;
		$robotsCode = CBIblockMegaMetaTags::$propRobotsCode;
		$indexCode = CBIblockMegaMetaTags::$propIndexCode;
		$followCode = CBIblockMegaMetaTags::$propFollowCode;
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$h1Code, 'h1');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$titleCode, 'title');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$keywordsCode, 'keywords');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$descriptionCode, 'description');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$robotsCode, 'robots');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$indexCode, 'index');
		COption::SetOptionString($this->MODULE_ID, "PROPERTY_".$followCode, 'follow');

		//Create IB
		CBIblockMegaMetaTags::createIBlock();
		return true;
	}

	function UnInstallDB($delTables)
	{
		if ($delTables){
			CBIblockMegaMetaTags::deleteIBlock();
		}
		UnRegisterModule($this->MODULE_ID);
		COption::RemoveOption($this->MODULE_ID);
		return true;
	}

	function InstallEvents()
	{
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "CBIblockMegaMetaTags", "SetIBlockAllKeys");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, "CBIblockMegaMetaTags", "SetIBlockAllKeys");
		RegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CBIblockMegaMetaTags", "ShowKeyList");
		RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CMegaMetaKeys", "SetMegaTags");
		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "CBIblockMegaMetaTags", "SetIBlockAllKeys");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, "CBIblockMegaMetaTags", "SetIBlockAllKeys");
		UnRegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CBIblockMegaMetaTags", "ShowKeyList");
		UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CMegaMetaKeys", "SetMegaTags");
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/components/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components/kuznica",
			true, true
		);

		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/admin/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools",
			true, true
		);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/kuznica/metatags");
		DeleteDirFilesEx("/bitrix/components/kuznica/metatags.keysautoset");
		DeleteDirFilesEx("/bitrix/tools/{$this->MODULE_ID}");

		return true;
	}

	function DoInstall()
	{
		if (!IsModuleInstalled($this->MODULE_ID))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
		}
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = intval($step);
		if ($step < 1)
		{
			$APPLICATION->IncludeAdminFile(
				GetMessage("TAGS_DELETE_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php"
			);
		}else{
			$this->UnInstallDB($_REQUEST["del_IB"]);
			$this->UnInstallEvents();
			$this->UnInstallFiles();
			UnRegisterModule($this->MODULE_ID);
		}
	}
}
?>
