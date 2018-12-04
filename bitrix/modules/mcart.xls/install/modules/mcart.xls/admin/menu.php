<?

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetAdditionalCSS("/bitrix/panel/main/mcart_xlsxls_menu.css");

if($APPLICATION->GetGroupRight("mcart.xls")!="D"){
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "mcart.xls",
        "sort" => 800,
		"icon" => "mcart_xls_menu_icon",
        "text" =>  GetMessage("MCART_EXCEL"),
        "title" =>  GetMessage("MCART_EXCEL"),
        
        
        "items_id" => "menu_mcart_xls",
		 "items"       => array()
		
    );
	 $aMenu["items"][] =  array(
        "text" => GetMessage("MCART_EXCEL_IMPORT"),
       
        "icon" => "mcart_xls_menu_icon",
        "page_icon" => "mcart_xls_menu_icon",
        
        "title" => GetMessage("FORM_RESULTS_ALT"),
		
								"url"  => "mcart_xls_start.php?lang=".LANGUAGE_ID,
								"icon" => "mcart_xls_menu_icon",
								"page_icon" => "mcart_xls_menu_icon",
								
								"title" => GetMessage("FORM_RESULTS_ALT"),
								"items"       => array()
       );
	   /*$aMenu["items"][] =  array(
        "text" => GetMessage("MCART_TASKPLUS_USERS"),
       
        "icon" => "form_menu_icon",
        "page_icon" => "form_page_icon",
        
        "title" => GetMessage("FORM_RESULTS_ALT"),
		"items"       => array(  0=>array(
								"text" => GetMessage("MCART_TASKPLUS_USERS_MASS_CHANGEPHOTO"),
								"url"  => "tasksplus_users_mass_changephoto.php?lang=".LANGUAGE_ID,
								"icon" => "form_menu_icon",
								"page_icon" => "form_page_icon",
								
								"title" => GetMessage("FORM_RESULTS_ALT"),
								"items"       => array()),
								
								
								1=>array("text" => GetMessage("MCART_TASKPLUS_USERS_MASS_CHANGEFIELD"),
								"url"  => "users_field_change.php?lang=".LANGUAGE_ID,
								"icon" => "form_menu_icon",
								"page_icon" => "form_page_icon",
								
								"title" => GetMessage("FORM_RESULTS_ALT"),
								"items"       => array())
								
								)
       );
	   
	  */ 
	   
    return $aMenu;
	
}
return false;


?>