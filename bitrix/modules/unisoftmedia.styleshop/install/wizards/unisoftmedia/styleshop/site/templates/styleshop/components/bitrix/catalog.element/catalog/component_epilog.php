<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('<meta property="og:type" content="website" />');
Asset::getInstance()->addString('<meta property="og:site_name" content="'.\Bitrix\Main\Config\Option::get('main', 'site_name').'" />');

$baseUrl = $_SERVER['HTTP_HOST'];

if(isset($arResult['PREVIEW_PICTURE']) && !empty($arResult['PREVIEW_PICTURE']))
	Asset::getInstance()->addString('<meta property="og:image" content="//'.$baseUrl.$arResult['PREVIEW_PICTURE']['SRC'].'" />');

if(isset($arResult['CANONICAL_PAGE_URL']) && !empty($arResult['CANONICAL_PAGE_URL']))
	Asset::getInstance()->addString('<meta property="og:url" content="'.$arResult['CANONICAL_PAGE_URL'].'" />');

if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE']))
	Asset::getInstance()->addString('<meta property="og:title" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE'].'" />');
else
	Asset::getInstance()->addString('<meta property="og:title" content="'.$arResult['NAME'].'" />');

if(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION']))
	Asset::getInstance()->addString('<meta property="og:description" content="'.$arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'].'" />');

unset($baseUrl);