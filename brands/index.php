<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog",
	"brands",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"ALSO_BUY_ELEMENT_COUNT" => "5",
		"ALSO_BUY_MIN_BUYES" => "1",
		"BANNERS1_IBLOCK_ID" => "4",
		"BANNERS1_IBLOCK_TYPE" => "banners",
		"BASKET_URL" => "/site_zf/personal/cart/",
		"BIG_DATA_RCM_TYPE" => "personal",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CAROUSEL_DOTS_VERTICAL" => "Y",
		"CATALOG_SEO_TEXT" => "CATALOG_SEO_TEXT_DOWN",
		"COMMON_ADD_TO_BASKET_ACTION" => "",
		"COMMON_SHOW_CLOSE_POPUP" => "Y",
		"COMPARE_ELEMENT_SORT_FIELD" => "sort",
		"COMPARE_ELEMENT_SORT_ORDER" => "asc",
		"COMPARE_FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"DETAIL_PICTURE",2=>"",),
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"COMPARE_OFFERS_FIELD_CODE" => array(0=>"NAME",1=>"PREVIEW_PICTURE",2=>"DETAIL_PICTURE",3=>"",),
		"COMPARE_OFFERS_PROPERTY_CODE" => array(0=>"ARTNUMBER",1=>"COLOR_R",2=>"SIZES_C",3=>"SIZES_CLOTHES",4=>"",),
		"COMPARE_PROPERTY_CODE" => array(0=>"WARRANTY",1=>"MANUFACTURER",2=>"COUNTRY",3=>"",),
		"COMPONENT_TEMPLATE" => "brands",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"DELIVERY_PROP_CODE" => "DELIVERY",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "Y",
		"DETAIL_ADD_TO_BASKET_ACTION" => array(0=>"ADD",),
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"DETAIL_BLOG_EMAIL_NOTIFY" => "N",
		"DETAIL_BLOG_URL" => "catalog_comments",
		"DETAIL_BLOG_USE" => "Y",
		"DETAIL_BRAND_PROP_CODE" => array(0=>"BRAND_REF",1=>"",),
		"DETAIL_BRAND_PROP_CODE_MANUFACTURE" => "MANUFACTURER",
		"DETAIL_BRAND_USE" => "Y",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_DETAIL_PICTURE_MODE" => "IMG",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"DETAIL_FB_USE" => "N",
		"DETAIL_LIST_PROPERTY_CODE_CHARACTERISTICS" => array(0=>"WARRANTY",1=>"MANUFACTURER",2=>"COUNTRY",3=>"NUMBER_OF_SIM_CARDS",4=>"ENCLOSURE_TYPE",5=>"PLATFORM",6=>"DIAGONAL_SCREEN",7=>"MATERIAL_KORPUS",8=>"MOBILE_NTERFACES",9=>"WIDTH",10=>"HEIGHT",11=>"THICKNESS",12=>"WEIGHT",13=>"",),
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_OFFERS_FIELD_CODE" => array(0=>"NAME",1=>"",),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(0=>"ARTNUMBER",1=>"COLOR_R",2=>"SIZES_C",3=>"SIZES_CLOTHES",4=>"MORE_PHOTO",5=>"",),
		"DETAIL_PROPERTY_CODE" => array(0=>"WARRANTY",1=>"MANUFACTURER",2=>"COUNTRY",3=>"",),
		"DETAIL_PROP_CODE_ARTNUMBER" => "ARTNUMBER",
		"DETAIL_SET_CANONICAL_URL" => "Y",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"DETAIL_SHOW_BASIS_PRICE" => "Y",
		"DETAIL_SHOW_MAX_QUANTITY" => "N",
		"DETAIL_USE_COMMENTS" => "Y",
		"DETAIL_USE_VOTE_RATING" => "Y",
		"DETAIL_VK_USE" => "N",
		"DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_ELEMENT_SELECT_BOX" => "N",
		"DISPLAY_TOP_PAGER" => "Y",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FIELDS" => array(0=>"PHONE",1=>"SCHEDULE",2=>"EMAIL",3=>"",),
		"FILTER_FIELD_CODE" => array(0=>"",1=>"",),
		"FILTER_NAME" => "arrFilter",
		"FILTER_OFFERS_FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"DETAIL_PICTURE",2=>"",),
		"FILTER_OFFERS_PROPERTY_CODE" => array(0=>"ARTNUMBER",1=>"COLOR_R",2=>"SIZES_C",3=>"SIZES_CLOTHES",4=>"",),
		"FILTER_PRICE_CODE" => array(0=>"BASE",),
		"FILTER_PROPERTY_CODE" => array(0=>"BRAND_REF",1=>"",),
		"FILTER_VIEW_MODE" => "vertical",
		"FORUM_ID" => "1",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "3",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "3",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"HIDE_NOT_AVAILABLE" => "N",
		"HOVER_IMAGE" => "Y",
		"IBLOCK_ID" => "9",
		"IBLOCK_TYPE" => "catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"LINK_CATALOG_CREDIT" => "credit/",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"LINK_IBLOCK_ID" => "",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_PROPERTY_SID" => "",
		"LIST_BROWSER_TITLE" => "UF_BROWSER_TITLE",
		"LIST_META_DESCRIPTION" => "UF_META_DESCRIPTION",
		"LIST_META_KEYWORDS" => "UF_KEYWORDS",
		"LIST_OFFERS_FIELD_CODE" => array(0=>"NAME",1=>"PREVIEW_PICTURE",2=>"DETAIL_PICTURE",3=>"",),
		"LIST_OFFERS_LIMIT" => "0",
		"LIST_OFFERS_PROPERTY_CODE" => array(0=>"ARTNUMBER",1=>"COLOR_R",2=>"SIZES_C",3=>"SIZES_CLOTHES",4=>"MORE_PHOTO",5=>"",),
		"LIST_PROPERTY_CODE" => array(0=>"NEWPRODUCT",1=>"SALELEADER",2=>"SPECIALOFFER",3=>"WARRANTY",4=>"",),
		"LIST_PROPERTY_CODE_CHARACTERISTICS" => array(0=>"WARRANTY",1=>"MANUFACTURER",2=>"COUNTRY",3=>"NUMBER_OF_SIM_CARDS",4=>"ENCLOSURE_TYPE",5=>"PLATFORM",6=>"DIAGONAL_SCREEN",7=>"",),
		"MAIN_TITLE" => "Наличие на складах",
		"MAX_HEIGHT" => "400",
		"MAX_WIDTH" => "300",
		"MESSAGES_PER_PAGE" => "10",
		"MESSAGE_404" => "",
		"MESS_BTN_ADDED_TO_BASKET" => "В корзине",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MIN_AMOUNT" => "10",
		"OFFERS_CART_PROPERTIES" => array(0=>"ARTNUMBER",1=>"COLOR_R",2=>"SIZES_C",3=>"SIZES_CLOTHES",),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_TREE_PROPS" => array(0=>"COLOR_R",1=>"SIZES_C",2=>"SIZES_CLOTHES",),
		"ON_PREVIEW_TEXT" => "Y",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "pagenav",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "15",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"PREFIX_KEYWORDS" => "%title%, купить %title%, купить %title% недорого",
		"PREFIX_NAME" => "Товары бренда %s%",
		"PREFIX_TITLE" => "Широкий выбор товаров бренда %title% в интернет магазине %site_name%",
		"PRICE_CODE" => array(0=>"BASE",),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PROP_CODE_ACCESSORIES" => "ACCESSORIES",
		"PROP_CODE_DOCUMENTATIONS" => "DOCUMENTATIONS",
		"QUICK_LOOK_PARAMS" => "QUICK_LOOK_BUTTON",
		"REVIEW_AJAX_POST" => "Y",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"SECTIONS_VIEW_MODE" => "TEXT",
		"SECTION_ADD_TO_BASKET_ACTION" => "ADD",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_TOP_DEPTH" => "1",
		"SEF_FOLDER" => "/site_zf/brands/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => array("sections"=>"","section"=>"#SECTION_CODE#/","element"=>"#SECTION_CODE#/#ELEMENT_CODE#/","compare"=>"compare/","smart_filter"=>"#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/",),
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SHOW_DEACTIVATED" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"SHOW_LINK_TO_FORUM" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_TOP_ELEMENTS" => "N",
		"SKU_VIEW" => "LIST2",
		"SLIDER_IMAGES" => "fade",
		"SLIDER_IMAGES_FADE_SPEED" => "300",
		"SLIDER_MINIIMAGES_FADE_SPEED" => "600",
		"STORES" => array(0=>"1",1=>"2",2=>"3",3=>"4",),
		"STORE_PATH" => "/site_zf/about/store/#store_id#",
		"TOP_ADD_TO_BASKET_ACTION" => "ADD",
		"URL_TEMPLATES_READ" => "",
		"USER_FIELDS" => array(0=>"",1=>"",),
		"USE_ALSO_BUY" => "Y",
		"USE_BIG_DATA" => "Y",
		"USE_BUY_ONE_CLICK" => "Y",
		"USE_CAPTCHA" => "Y",
		"USE_CATALOG_CREDIT" => "Y",
		"USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
		"USE_COMPARE" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FAVORITES" => "Y",
		"USE_FILTER" => "Y",
		"USE_GIFTS_DETAIL" => "Y",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
		"USE_GIFTS_SECTION" => "Y",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_MIN_AMOUNT" => "Y",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "Y",
		"USE_QUICKVIEW" => "Y",
		"USE_RATING" => "Y",
		"USE_REVIEW" => "Y",
		"USE_SALES" => "Y",
		"USE_SALE_BESTSELLERS" => "Y",
		"USE_STORE" => "Y",
		"USE_STORE_PHONE" => "Y",
		"USE_STORE_SCHEDULE" => "Y",
		"USE_ZOOM" => "Y",
		"ZOOM" => "inner",
		"ZOOM_CURSOR" => "crosshair",
		"ZOOM_EASING" => "0",
		"ZOOM_LENS_BORDER" => "0",
		"ZOOM_LENS_BORDER_SIZE" => "1",
		"ZOOM_LENS_BORDER_SIZE_COLOR" => "#dcdcdc",
		"ZOOM_LENS_COLOUR" => "#c9dae6",
		"ZOOM_LENS_LENS_SHAPE" => "round",
		"ZOOM_LENS_SIZE" => "200",
		"ZOOM_MOUSEWHEELZOOM" => "0",
		"ZOOM_WINDOW_HEIGHT" => "500",
		"ZOOM_WINDOW_OFFETX" => "10",
		"ZOOM_WINDOW_POSITION" => "1",
		"ZOOM_WINDOW_WIDTH" => "500"
	)
);?>Accustic Arts, Advance Acoustic, Anthem, Arcam, Atacama, Audia Flight, Audio Phisic,Audio Pro, Audiolab, AudioQuest, Bello, Cambridge Audio, Canton, Chario, Clearaudio, Cornered Audio, Creek, Denon, Dual, Epos, Hegel, Grado, Isol-8, Kef, Marantz, Monitor Audio, Monster Cable, Musical Fidelity, Nad, OmniMount, Oppo, Paradigm, PIEGA, PMC, Pro-Ject, PS Audio, Real Cable, Russound, Sherwood, Siltech, Soundationa, T+A, The Chord Company, Transformatic Audio, Triangle, Van den Hul, Vincent, Wharfedale, Yamaha<br>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>