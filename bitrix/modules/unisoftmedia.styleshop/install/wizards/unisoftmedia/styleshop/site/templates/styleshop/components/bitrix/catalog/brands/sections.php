<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

?>
<h1><?$APPLICATION->ShowTitle(false)?></h1>
<?$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/top.php",array(),array("MODE"=>"html","NAME"=> Loc::getMessage("TOP")));?>
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.brandblock", 
    "grid_brand", 
    array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ELEMENT_ID" => "",
        "ELEMENT_CODE" => "",
        "PROP_CODE" => $arParams['DETAIL_BRAND_PROP_CODE'],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SEF_FOLDER" => $arParams['SEF_FOLDER'],
        "WIDTH" => "120",
        "HEIGHT" => "120",
        "WIDTH_SMALL" => "120",
        "HEIGHT_SMALL" => "120",
        "COMPONENT_TEMPLATE" => "all_brand"
    ),
    $component
);?>
<?$APPLICATION->IncludeFile($arParams["SEF_FOLDER"]."include_areas/bottom.php",array(),array("MODE"=>"html","NAME"=> Loc::getMessage("BOTTOM")));?>