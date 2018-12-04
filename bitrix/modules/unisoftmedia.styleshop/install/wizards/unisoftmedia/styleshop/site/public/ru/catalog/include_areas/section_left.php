<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.top",
	"sidebar",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"BASKET_URL" => SITE_DIR."personal/cart/",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CAROUSEL_ITEMS" => "3",
		"CAROUSEL_RESPONSIVE" => "",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISPLAY_COMPARE" => "N",
		"ELEMENT_COUNT" => "5",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "",
		"HIDE_NOT_AVAILABLE" => "N",
		"HOVER_IMAGE" => "Y",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"IBLOCK_TYPE" => "catalog",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MAX_HEIGHT" => "115",
		"MAX_WIDTH" => "115",
		"MESS_BTN_ADDED_TO_BASKET" => "В корзине",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_IN_AVAILABLE" => "Есть",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_TITLE" => "Лидеры продаж",
		"MOUSE_DRAG" => "Y",
		"OFFERS_CART_PROPERTIES" => array("COLOR_R","SIZES_CLOTHES"),
		"OFFERS_FIELD_CODE" => array("",""),
		"OFFERS_LIMIT" => "0",
		"OFFERS_PROPERTY_CODE" => array("COLOR_R","SIZES_CLOTHES",""),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_TREE_PROPS" => array("COLOR_R","SIZES_CLOTHES"),
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array("BASE"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PROPERTY_CODE" => array("",""),
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SEF_MODE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SLIDER_AUTOPLAY" => "N",
		"SLIDER_AUTOPLAY_HOVER_PAUSE" => "N",
		"SLIDER_LOOP" => "N",
		"SLIDER_NAV" => "Y",
		"SLIDER_SHOW_SPEED" => "8000",
		"SLIDER_SPEED" => "1000",
		"TEMPLATE_THEME" => "blue",
		"USE_CAROUSEL" => "Y",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"VIEW_MODE" => "SECTION"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.viewed.products",
	"sidebar",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_#OFFERS_IBLOCK_ID#" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"BASKET_URL" => SITE_DIR."personal/cart/",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CAROUSEL_ITEMS" => "3",
		"CAROUSEL_RESPONSIVE" => "",
		"CART_PROPERTIES_#CATALOG_IBLOCK_ID#" => array("",""),
		"CART_PROPERTIES_#OFFERS_IBLOCK_ID#" => array("COLOR_R","SIZES_CLOTHES","",""),
		"CONVERT_CURRENCY" => "N",
		"DEPTH" => "",
		"DETAIL_URL" => "",
		"HIDE_NOT_AVAILABLE" => "N",
		"HOVER_IMAGE" => "Y",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"IBLOCK_TYPE" => "catalog",
		"LABEL_PROP_#CATALOG_IBLOCK_ID#" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MAX_HEIGHT" => "115",
		"MAX_WIDTH" => "115",
		"MESS_BTN_ADDED_TO_BASKET" => "В корзине",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_IN_AVAILABLE" => "Есть",
		"MESS_NOT_AVAILABLE" => "",
		"MESS_TITLE" => "Вы смотрели",
		"MOUSE_DRAG" => "Y",
		"OFFER_TREE_PROPS_#OFFERS_IBLOCK_ID#" => array("COLOR_R","SIZES_CLOTHES"),
		"PAGE_ELEMENT_COUNT" => "6",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array("BASE"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE_#CATALOG_IBLOCK_ID#" => array("",""),
		"PROPERTY_CODE_#OFFERS_IBLOCK_ID#" => array("CML2_LINK","COLOR_R","SIZES_CLOTHES",""),
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_CODE" => "",
		"SECTION_ELEMENT_ID" => "",
		"SECTION_ID" => "",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_IMAGE" => "Y",
		"SHOW_NAME" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_PRODUCTS_#CATALOG_IBLOCK_ID#" => "Y",
		"SLIDER_AUTOPLAY" => "N",
		"SLIDER_AUTOPLAY_HOVER_PAUSE" => "N",
		"SLIDER_LOOP" => "N",
		"SLIDER_NAV" => "Y",
		"SLIDER_SHOW_SPEED" => "8000",
		"SLIDER_SPEED" => "1000",
		"TEMPLATE_THEME" => "blue",
		"USE_CAROUSEL" => "Y",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.bestsellers",
	"sidebar",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_#OFFERS_IBLOCK_ID#" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BASKET_URL" => SITE_DIR."personal/cart/",
		"BY" => "AMOUNT",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CAROUSEL_ITEMS" => "3",
		"CAROUSEL_RESPONSIVE" => "",
		"CART_PROPERTIES_#CATALOG_IBLOCK_ID#" => array("",""),
		"CART_PROPERTIES_#OFFERS_IBLOCK_ID#" => array("COLOR_R","SIZES_CLOTHES","",""),
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISPLAY_COMPARE" => "N",
		"FILTER" => array("CANCELED","ALLOW_DELIVERY","PAYED","DEDUCTED","N","P","F"),
		"HIDE_NOT_AVAILABLE" => "N",
		"HOVER_IMAGE" => "Y",
		"LABEL_PROP_#CATALOG_IBLOCK_ID#" => "-",
		"MAX_HEIGHT" => "115",
		"MAX_WIDTH" => "115",
		"MESS_BTN_ADDED_TO_BASKET" => "В корзине",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_TITLE" => "Лидер продаж",
		"MOUSE_DRAG" => "Y",
		"OFFER_TREE_PROPS_#OFFERS_IBLOCK_ID#" => array("COLOR_R","SIZES_CLOTHES"),
		"PAGE_ELEMENT_COUNT" => "10",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PERIOD" => "0",
		"PRICE_CODE" => array("BASE"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE_#CATALOG_IBLOCK_ID#" => array("",""),
		"PROPERTY_CODE_#OFFERS_IBLOCK_ID#" => array("CML2_LINK","COLOR_R","SIZES_CLOTHES",""),
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_IMAGE" => "Y",
		"SHOW_NAME" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_PRODUCTS_#CATALOG_IBLOCK_ID#" => "Y",
		"SLIDER_AUTOPLAY" => "N",
		"SLIDER_AUTOPLAY_HOVER_PAUSE" => "N",
		"SLIDER_LOOP" => "N",
		"SLIDER_NAV" => "Y",
		"SLIDER_SHOW_SPEED" => "8000",
		"SLIDER_SPEED" => "1000",
		"USE_CAROUSEL" => "Y",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?>