<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$module_id = 'unisoftmedia.styleshop';

\Bitrix\Main\Config\Option::set("sale", "SHOP_SITE_".WIZARD_SITE_ID, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set("main", "auth_components_template", "flat");
\Bitrix\Main\Config\Option::set("fileman", "propstypes", serialize(array("description"=>\Bitrix\Main\Localization\Loc::getMessage("MAIN_OPT_DESCRIPTION"), "keywords"=>\Bitrix\Main\Localization\Loc::getMessage("MAIN_OPT_KEYWORDS"), "title"=>\Bitrix\Main\Localization\Loc::getMessage("MAIN_OPT_TITLE"), "keywords_inner"=>\Bitrix\Main\Localization\Loc::getMessage("MAIN_OPT_KEYWORDS_INNER"))), '', WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set("search", "suggest_save_days", 250);
\Bitrix\Main\Config\Option::set("search", "use_tf_cache", "Y");
\Bitrix\Main\Config\Option::set("search", "use_word_distance", "Y");
\Bitrix\Main\Config\Option::set("search", "use_social_rating", "Y");
\Bitrix\Main\Config\Option::set("iblock", "use_htmledit", "Y");
\Bitrix\Main\Config\Option::set("main", "optimize_css_files", "N");
\Bitrix\Main\Config\Option::set("main", "captcha_registration", "N");


\Bitrix\Main\Config\Option::set($module_id, 'responsive', 1, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'type_header', 1, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'top_panel_color', 'light', WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'detail_column', 'two_columns_right', WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'section_column', 'two_columns_left', WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'menu_color', 1, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'googlefonts', serialize(array('Open Sans')), WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set($module_id, 'template', 'template1', '', WIZARD_SITE_ID);


// Template 1
\Bitrix\Main\Config\Option::set($module_id, 'section_template1', serialize(
	array(
		'1' => array('add_style' => 'padding-top: 20px;'),
		'3' => array('add_class' => 'block-top-sm '),
		'9' => array('bg_image' => '/bitrix/templates/styleshop/images/parallax.jpg','add_class' => 'banner-offer h-base parallax block-top'),
		'13' => array('add_class' => 'FlexBox-Row-Wrap')
	)
), '', WIZARD_SITE_ID);

// Template 2

// template2_sidebar
\Bitrix\Main\Config\Option::set($module_id, 'section_template2_sidebar', serialize(
	array(
		'18' => array('add_class' => 'sidebar block-top hidden-xs hidden-sm '),
		'35' => array('add_class' => 'hidden-xs')
	)
), '', WIZARD_SITE_ID);

// template_sidebar2
\Bitrix\Main\Config\Option::set($module_id, 'section_template_sidebar2', serialize(
	array(
		'1' => array('add_style' => 'background-color: #f5f5f5;'),
		'3' => array('add_style' => 'padding: 0;margin-left: -10px;'),
		'5' => array('add_class' => 'sidebar block-top hidden-xs hidden-sm '),
		'15' => array('add_class' => 'sidebar block-top'),
		'37' => array('add_class' => 'block-top'),
		'34' => array('add_class' => 'block-top'),
		'32' => array('add_style' => 'margin-top: 30px;')
	)
), '', WIZARD_SITE_ID);

// template_sidebar3
\Bitrix\Main\Config\Option::set($module_id, 'section_template_sidebar3', serialize(
	array(
		'1' => array('add_class' => 'block-top'),
		'2' => array('add_class' => 'sidebar hidden-xs hidden-sm '),
		'9' => array('add_class' => 'block '),
		'34' => array('add_class' => 'hidden-xs'),
		'36' => array('add_style' => 'margin-top: 30px;')
	)
), '', WIZARD_SITE_ID);


//socialservices
if (\Bitrix\Main\Config\Option::get("socialservices", "auth_services") == "")
{
	$bRu = (LANGUAGE_ID == 'ru');
	$arServices = array(
		"VKontakte" => "N",  
		"MyMailRu" => "N",
		"Twitter" => "N",
		"Facebook" => "N",
		"Livejournal" => "Y",
		"YandexOpenID" => ($bRu? "Y":"N"),
		"Rambler" => ($bRu? "Y":"N"),
		"MailRuOpenID" => ($bRu? "Y":"N"),
		"Liveinternet" => ($bRu? "Y":"N"),
		"Blogger" => "Y",
		"OpenID" => "Y",
		"LiveID" => "N",
	);
	\Bitrix\Main\Config\Option::set("socialservices", "auth_services", serialize($arServices));
}
?>