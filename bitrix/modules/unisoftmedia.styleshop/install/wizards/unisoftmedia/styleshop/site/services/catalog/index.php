<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$catalogSubscribe = $wizard->GetVar("catalogSubscribe");
$curSiteSubscribe = ($catalogSubscribe == "Y") ? array("use" => "Y", "del_after" => "100") : array("del_after" => "100");
$subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
$arSubscribe = unserialize($subscribe);
$arSubscribe[WIZARD_SITE_ID] = $curSiteSubscribe;
COption::SetOptionString("sale", "subscribe_prod", serialize($arSubscribe));

$catalogView = $wizard->GetVar("catalogView");
if (!in_array($catalogView, array("bar", "list", "price_list"))) $catalogView = "list";
COption::SetOptionString("eshop", "catalogView", $catalogView, false, WIZARD_SITE_ID);

$useStoreControl = $wizard->GetVar("useStoreControl");
$useStoreControl = ($useStoreControl == "Y") ? "Y" : "N";
$curUseStoreControl = COption::GetOptionString("catalog", "default_use_store_control", "N");
COption::SetOptionString("catalog", "default_use_store_control", $useStoreControl);

$productReserveCondition = $wizard->GetVar("productReserveCondition");
$productReserveCondition = (in_array($productReserveCondition, array("O", "P", "D", "S"))) ? $productReserveCondition : "P";
COption::SetOptionString("sale", "product_reserve_condition", $productReserveCondition);

if (CModule::IncludeModule("catalog"))
{
	if($useStoreControl == "Y" && $curUseStoreControl == "N")
	{
		$dbStores = CCatalogStore::GetList(array(), array("ACTIVE" => 'Y'));
		if(!$dbStores->Fetch())
		{
			$arStoreFields = array(
				"TITLE" => GetMessage("CAT_STORE_NAME"),
				"ADDRESS" => GetMessage("STORE_ADR_1"),
				"DESCRIPTION" => GetMessage("STORE_DESCR_1"),
				"GPS_N" => GetMessage("STORE_GPS_N_1"),
				"GPS_S" => GetMessage("STORE_GPS_S_1"),
				"PHONE" => GetMessage("STORE_PHONE_1"),
				"SCHEDULE" => GetMessage("STORE_PHONE_SCHEDULE"),
			);
			$newStoreId = CCatalogStore::Add($arStoreFields);
			if($newStoreId)
			{
				CCatalogDocs::synchronizeStockQuantity($newStoreId);
			}
            
            $arStoreFields1 = array(
				"TITLE" => GetMessage("CAT_STORE_NAME_2"),
				"ADDRESS" => GetMessage("STORE_ADR_2"),
				"DESCRIPTION" => GetMessage("STORE_DESCR_2"),
				"GPS_N" => GetMessage("STORE_GPS_N_2"),
				"GPS_S" => GetMessage("STORE_GPS_S_2"),
				"PHONE" => GetMessage("STORE_PHONE_2"),
				"SCHEDULE" => GetMessage("STORE_PHONE_SCHEDULE_2"),
			);
			$newStoreId1 = CCatalogStore::Add($arStoreFields1);
			if($newStoreId1)
			{
				CCatalogDocs::synchronizeStockQuantity($newStoreId1);
			}
            
            $arStoreFields2 = array(
				"TITLE" => GetMessage("CAT_STORE_NAME_3"),
				"ADDRESS" => GetMessage("STORE_ADR_3"),
				"DESCRIPTION" => GetMessage("STORE_DESCR_3"),
				"GPS_N" => GetMessage("STORE_GPS_N_3"),
				"GPS_S" => GetMessage("STORE_GPS_S_3"),
				"PHONE" => GetMessage("STORE_PHONE_3"),
				"SCHEDULE" => GetMessage("STORE_PHONE_SCHEDULE_3"),
			);
			$newStoreId2 = CCatalogStore::Add($arStoreFields2);
			if($newStoreId2)
			{
				CCatalogDocs::synchronizeStockQuantity($newStoreId2);
			}
            
            $arStoreFields3 = array(
				"TITLE" => GetMessage("CAT_STORE_NAME_4"),
				"ADDRESS" => GetMessage("STORE_ADR_4"),
				"DESCRIPTION" => GetMessage("STORE_DESCR_4"),
				"GPS_N" => GetMessage("STORE_GPS_N_4"),
				"GPS_S" => GetMessage("STORE_GPS_S_4"),
				"PHONE" => GetMessage("STORE_PHONE_4"),
				"SCHEDULE" => GetMessage("STORE_PHONE_SCHEDULE_4"),
			);
			$newStoreId3 = CCatalogStore::Add($arStoreFields3);
			if($newStoreId3)
			{
				CCatalogDocs::synchronizeStockQuantity($newStoreId3);
			}
		}
	}
	/*$arStores = array();
	$dbStore= CCatalogStore::GetList(array(), array("XML_ID" => "mebel"), false, false, array("ID"));
	if (!$arStore = $dbStore->Fetch())
	{
		$arNewStore =  array(
			"TITLE" => GetMessage("STORE_NAME_1"),
			"ACTIVE" => "N",
			"ADDRESS" => GetMessage("STORE_ADR_1"),
			"DESCRIPTION" => GetMessage("STORE_DESCR_1"),
			"USER_ID" => $USER->GetID(),
			"GPS_N" => GetMessage("STORE_GPS_N_1"),
			"GPS_S" => GetMessage("STORE_GPS_S_1"),
			"PHONE" => GetMessage("STORE_PHONE_1"),
			"SCHEDULE" => "24/7",
			"XML_ID" => "mebel",
		);
		CCatalogStore::Add($arNewStore);
	}  */
}

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

COption::SetOptionString("catalog", "allow_negative_amount", "Y");
COption::SetOptionString("catalog", "default_can_buy_zero", "Y");
COption::SetOptionString("catalog", "default_quantity_trace", "Y");
?>