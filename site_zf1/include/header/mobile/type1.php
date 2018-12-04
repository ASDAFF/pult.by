<?use Bitrix\Main\Localization\Loc;?>
<?global $theme;?>
<header id="header-mobile" class="hidden-md hidden-lg mm-slideout" data-spy="affix" data-offset-top="50">
	<div class="container">
		<div class="row">
			<div class="block-table">
				<div class="block-table-row">
					<div class="un-col-xs-20 un-col-sm-20 block-table-td">
						<div class="navbar-inverse">
							<button type="button" id="mm-slideout" class="navbar-toggle collapsed visible-xs visible-sm" data-toggle="collapse">
						    <span class="icon-bar"></span>
						    <span class="icon-bar"></span>
						    <span class="icon-bar"></span>
						  </button>
					  </div>
					</div>
					<div class="un-col-xs-20 un-col-sm-20 block-table-td mobile-search-btn">
						<a class="mobile-search-button" href="#"></a>
					</div>
					<div class="un-col-xs-20 un-col-sm-20 block-table-td">
						<?$APPLICATION->IncludeComponent(
								"bitrix:system.auth.form", 
								"mobile", 
								array(
									"REGISTER_URL" => SITE_DIR."auth/",
									"PROFILE_URL" => SITE_DIR."personal/",
									"SHOW_ERRORS" => "N",
									"FORGOT_PASSWORD_URL" => "",
									"USE_AUTH_Wishlist" => "Y",
									"COMPONENT_TEMPLATE" => "auth"
								),
								false
							);
						?>
					</div>
					<div class="un-col-xs-20 un-col-sm-20 block-table-td">
						<?$APPLICATION->IncludeComponent(
							"unisoftmedia:wishlist.list", 
							"mobile", 
							array(
								"COMPONENT_TEMPLATE" => "mobile",
								"PATH_TO_WISHLIST" => SITE_DIR."personal/wishlist/",
								"MAX_WIDTH_WISHLIST" => "70",
								"MAX_HEIGHT_WISHLIST" => "70",
								"SHOW_PRODUCTS" => "N"
							),
							false
						);?>
					</div>
					<div class="un-col-xs-20 un-col-sm-20 block-table-td">
						<div class="hminicart">
               <!-- minicart -->
								<?php $APPLICATION->IncludeComponent(
									"bitrix:sale.basket.basket.line", 
									"mobile", 
									array(
										"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
										"PATH_TO_PERSONAL" => SITE_DIR."personal/",
										"SHOW_PERSONAL_LINK" => "N",
										"SHOW_NUM_PRODUCTS" => "Y",
										"SHOW_TOTAL_PRICE" => "N",
										"SHOW_PRODUCTS" => "N",
										"POSITION_FIXED" => "N",
										"SHOW_EMPTY_VALUES" => "N",
										"SHOW_AUTHOR" => "N",
										"PATH_TO_REGISTER" => SITE_DIR."login/",
										"PATH_TO_PROFILE" => SITE_DIR."personal/",
										"SHOW_DELAY" => "N",
										"SHOW_NOTAVAIL" => "N",
										"SHOW_SUBSCRIBE" => "N",
										"SHOW_IMAGE" => "Y",
										"SHOW_PRICE" => "Y",
										"SHOW_SUMMARY" => "Y",
										"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
										"COMPONENT_TEMPLATE" => "mobile",
										"HIDE_ON_BASKET_PAGES" => "N"
									),
									false
								);?>
						<!-- /minicart -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<aside id="search-overlay" data-state="closed">
<!-- Search -->
	<?php $APPLICATION->IncludeComponent(
		"bitrix:search.title", 
		"mobile", 
		array(
			"NUM_CATEGORIES" => "1",
			"TOP_COUNT" => "5",
			"ORDER" => "date",
			"USE_LANGUAGE_GUESS" => "N",
			"CHECK_DATES" => "N",
			"SHOW_OTHERS" => "N",
			"PAGE" => SITE_DIR."catalog/",
			"CATEGORY_0_TITLE" => "",
			"CATEGORY_0" => array(
				0 => "iblock_catalog",
				),
			"SHOW_INPUT" => "Y",
			"INPUT_ID" => "title-search-input-mobile",
			"CONTAINER_ID" => "title-search-mobile",
			"PRICE_CODE" => array(
				0 => "BASE",
				),
			"PRICE_VAT_INCLUDE" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"SHOW_PREVIEW" => "Y",
			"PREVIEW_WIDTH" => "75",
			"PREVIEW_HEIGHT" => "75",
			"CONVERT_CURRENCY" => "N",
			"COMPONENT_TEMPLATE" => "mobile",
			"CATEGORY_0_iblock_catalog" => array(
				0 => "all",
				)
			),
		false
		); ?>
	<!-- /Search -->
</aside>