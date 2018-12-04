<?
if (!CModule::IncludeModule("iblock"))
	return false;

IncludeModuleLangFile(__FILE__);
$module_id = 'esol.importxml';

$aMenu = array();

global $USER;
$bUserIsAdmin = $USER->IsAdmin();

$bHasWRight = false;
$rsIBlocks = CIBlock::GetList(array("SORT"=>"asc", "NAME"=>"ASC"), array("MIN_PERMISSION" => "U"));
if($arIBlock = $rsIBlocks->Fetch())
{
	$bHasWRight = true;
}

if($APPLICATION->GetGroupRight($module_id) < "W")
{
	$bHasWRight = false;
}

if($bUserIsAdmin || $bHasWRight)
{
	if($bUserIsAdmin || $bHasWRight)
	{
		$aSubMenu[] = array(
			"text" => GetMessage("ESOL_MENU_IMPORT_TITLE"),
			"url" => "esol_import_xml.php?lang=".LANGUAGE_ID,
			"more_url" => array("esol_import_xml_profile_list.php"),
			"title" => GetMessage("ESOL_MENU_IMPORT_TITLE"),
			"module_id" => "esol.importxml",
			"items_id" => "menu_esol_importxml",
			"sort" => 100,
			"section" => "esol_importxml",
		);
		
		/*$aSubMenu[] = array(
			"text" => GetMessage("ESOL_MENU_IMPORT_TITLE_STAT"),
			"url" => "esol_import_xml_event_log.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("ESOL_MENU_IMPORT_TITLE_STAT"),
			"module_id" => "esol.importxml",
			"items_id" => "menu_esol_importxml",
			"sort" => 300,
			"section" => "esol_importxml",
		);*/
		
		$aMenu[] = array(
			"parent_menu" => "global_menu_content",
			"section" => "esol_importxml",
			"sort" => 1200,
			"text" => GetMessage("ESOL_MENU_IMPORT_TITLE_PARENT"),
			"title" => GetMessage("ESOL_MENU_IMPORT_TITLE_PARENT"),
			"icon" => "esol_importxml_menu_import_icon",
			"items_id" => "menu_esol_importxml_parent",
			"module_id" => "esol.importxml",
			"items" => $aSubMenu,
		);
	}
}

return $aMenu;
?>