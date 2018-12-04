<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;

if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_NAME']))
	$APPLICATION->AddHeadString('<meta property="og:image" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_NAME'].'" />');
else if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_NAME']))
	$APPLICATION->AddHeadString('<meta property="og:image" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_NAME'].'" />');

if(isset($arResult['CANONICAL_PAGE_URL']) && !empty($arResult['CANONICAL_PAGE_URL']))
	$APPLICATION->AddHeadString('<meta property="og:url" content="'.$arResult['CANONICAL_PAGE_URL'].'" />');

if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE']))
	$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE'].'" />');
else
	$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult['NAME'].'" />');

if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION']))
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'].'" />');