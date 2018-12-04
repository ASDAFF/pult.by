<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"left",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "left",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "N"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sender.subscribe",
	"sidebar",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CONFIRMATION" => "Y",
		"MESS_DESC" => "Подпишитесь и получайте дополнительные скидки",
		"MESS_TITLE" => "Будь в курсе первым!",
		"PAGE" => SITE_DIR."personal/subscribe/",
		"SET_TITLE" => "N",
		"SHOW_HIDDEN" => "N",
		"USE_PERSONALIZATION" => "Y"
	)
);?><div class="block promo-banner h-base">
 <a class="effect hover-effect03" href="#">
	<div class="block-img" style="background-image: url('/bitrix/templates/#TEMPLATE_ID#/images/banners/img-sidebar.jpg'); height: 460px;">
	</div>
	<div class="panel-body" style="top: 71%;right: 0;">
		<h2 class="font-weight-bold">Скидка</h2>
 <span class="font-weight-bold" style="color: #555555;">
		на каждый второй товар <br>
		 до <strong style="color: #ff5555;font-size: 25px;">25%</strong></span>
	</div>
 </a>
</div>