<?
IncludeModuleLangFile( __FILE__);


if(class_exists("mcart_xls")) 
	return;

Class mcart_xls extends CModule
{
	var $MODULE_ID = "mcart.xls";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	
	
	function mcart_xls() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=TASKFROMEMAIL_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=TASKFROMEMAIL_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("xls_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("xls_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.xls"))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		
		return true;
	}

	
	function InstallDB() {
		global $DB;
                
                /*
                 *  global $DB;
   
   // создаем таблицу для хранения данных авторизации пользователей,
   // полученных методом кодирования паролей в MyBB(см. задача №3)
   $DB -> Query("CREATE TABLE IF NOT EXISTS old_user (
      LOGIN varchar(60) NOT NULL,
      PASSWORD varchar(64) NOT NULL,
      SALT varchar(64) NOT NULL,
      PRIMARY KEY (LOGIN));"
   );
                 */      
                
		//$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.xls/install/createtable.sql");
                 $DB -> Query("CREATE TABLE IF NOT EXISTS mcart_xls (
	id int(11) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	name_field int(11),
	identify varchar(255),
	sheet_id int(11),
	iblock_id int(11),
	section_id int(11),
	data_row int(11),
	title_row int(11),
	diapazone_a varchar(2),
	diapazone_z varchar(2),
	sku_iblock_id int(11),
	cml2_link_code varchar(20),
	PRIMARY KEY (id)
)"
   );
   $DB -> Query("CREATE TABLE IF NOT EXISTS main_profile (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  worksheet int(10) NOT NULL,
  iblock_id int(10) NOT NULL,
  row_first int(10) NOT NULL,
  row_title int(10) NOT NULL,
  column_firsl varchar(2) NOT NULL,
  column_last varchar(2) NOT NULL,
  row_end_label varchar(15) NOT NULL,
  need_offer tinyint(1) NOT NULL,
  section_id int(10) NOT NULL,
  section_id_new int(10) NOT NULL,
  need_translit tinyint(1) NOT NULL,
  PRIMARY KEY (id)
)"
   );
   $DB -> Query("CREATE TABLE IF NOT EXISTS mcart_profile_property (
  id int(11) NOT NULL AUTO_INCREMENT,
  profile_id int(10) NOT NULL,
  column_litera varchar(4) NOT NULL,
  field_code varchar(200) NOT NULL,
  action varchar(200) NOT NULL,
  subaction varchar(20) NOT NULL,
  params text NOT NULL,
  identify tinyint(1) NOT NULL,
  PRIMARY KEY (id)
)"
   );
                
		RegisterModule("mcart.xls");	
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		global $DB;
                $DB -> Query("DROP TABLE IF EXISTS mcart_xls");
                $DB -> Query("DROP TABLE IF EXISTS mcart_xls_fields");
        		//$DB -> Query("DROP TABLE IF EXISTS mcart_profile_property");
		//$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.xls/install/droptable.sql");
		UnRegisterModule("mcart.xls");
		return true;
	}
	
	
	
	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/panel", $_SERVER["DOCUMENT_ROOT"]."/bitrix/panel", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/include", $_SERVER["DOCUMENT_ROOT"]."/bitrix/include", true, true);
	return true;
	}
	
	function UnInstallFiles()
	{	
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/panel/main/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/panel/main");
		DeleteDirFilesEx("/bitrix/include/mcart.xls");	
		DeleteDirFilesEx("/bitrix/images/mcart.xls");
		return true;
	}
	
	

	
} //end class
?>