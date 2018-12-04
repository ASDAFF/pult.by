<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(is_array($arResult["BRAND_BLOCKS"]) && !empty($arResult["BRAND_BLOCKS"]))
{
    $arBrandBlocks = array();
    $name = '';
    foreach($arResult["BRAND_BLOCKS"] as $arBrand)
    {
        $name = strtolower($arBrand['NAME']);
        $arBrandBlocks[$name] = $arBrand;
    }
    $arResult["BRAND_BLOCKS"]    = array();
    $arResult["BRAND_BLOCKS"]    = $arBrandBlocks;
    ksort($arResult["BRAND_BLOCKS"]);
}