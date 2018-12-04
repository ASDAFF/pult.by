<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$moduleId = 'alexkova.megametatags';
if (CModule::IncludeModuleEx($moduleId) != 1){
	return false;
}
global $APPLICATION, $USER;

$arResult = array();
$arCache = array();

//cache path
$arCache[] = $APPLICATION->GetCurPage();

//cache vars
if ($GLOBALS[$arParams['ADD_IN_CACHE']]){
	$arCache[] = $GLOBALS[$arParams['ADD_IN_CACHE']];
}

//cache keys
$arKeysInCache = CMegaMetaKeys::getCacheKeys();

if ($arKeysInCache){
	$arCache[] = $arKeysInCache;
}

if ($this->StartResultCache(false,$arCache)){

	$arResult['TAG_RULES'] = CBIblockMegaMetaTags::getIBElementByPage();
	$arResult['TAG_RULES'] = CMegaMetaRules::filterIBElementsTargeting($arResult['TAG_RULES']);
	$arResult['TAG_RULES'] = CMegaMetaRules::filterIBElementsKeys($arResult['TAG_RULES']);
	$arResult['ELEMENT'] = CMegaMetaRules::filterIBElementsSort($arResult['TAG_RULES']);
	if (!$arResult["ELEMENT"]){
		$this->AbortResultCache();
	}
	$this->SetResultCacheKeys(array("ELEMENT"));
	$this->IncludeComponentTemplate();
}

$tags = CMegaMetaKeys::setTags($arResult['ELEMENT']);
$arResult['TAGS'] = $tags['TAGS'];
$arResult['WHERE_SET'] = $tags['WHERE_SET'];
$arResult['IN_CACHE'] = $tags['IN_CACHE'];
$arResult['KEYS'] = $tags['KEYS'];
$arResult['AREA_IN'] = $APPLICATION->GetShowIncludeAreas();
$arResult['SHOW_DEBUG'] = (intval(COption::GetOptionString($moduleId, "SHOW_DEBUGGING_INFO"))) ? "Y" : NULL;

//get curren metatags
$arResult['CURRENT_TAGS'][CBIblockMegaMetaTags::$proph1Code] = $APPLICATION->GetPageProperty('h1');
$arResult['CURRENT_TAGS'][CBIblockMegaMetaTags::$propTitleCode] = $APPLICATION->GetTitle();
$arResult['CURRENT_TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode] = $APPLICATION->GetPageProperty('keywords');
$arResult['CURRENT_TAGS'][CBIblockMegaMetaTags::$propDescriptionCode] = $APPLICATION->GetPageProperty('description');

//SEOTEXT
$KNSEOTEXT1 = $APPLICATION->GetPageProperty('KNSEOTEXT1');
if ($KNSEOTEXT1){
	$arResult['CURRENT_TAGS']['preview_text(KNSEOTEXT1)'] = $KNSEOTEXT1;
}
$KNSEOTEXT2 = $APPLICATION->GetPageProperty('KNSEOTEXT2');
if ($KNSEOTEXT2){
	$arResult['CURRENT_TAGS']['detail_text(KNSEOTEXT2)'] = $KNSEOTEXT2;
}

//element link
$type_iblock = CBIblockMegaMetaTags::$iBlockTypeId;
$arResult['ELEMENT']['LINK'] = "/bitrix/admin/iblock_element_edit.php?ID={$arResult['ELEMENT']['ID']}&type=$type_iblock&IBLOCK_ID={$arResult['ELEMENT']['IBLOCK_ID']}&find_section_section=-1";

if ($USER->isAdmin() && $arResult['AREA_IN']){
	include_once('incPage.php');
}

?>