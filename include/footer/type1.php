<?use Bitrix\Main\Localization\Loc;?>

<div id="soc-subscribe">
    <div class="container">
        <div class="row">
            <div class="footer1 clearfix">
                <div class="col-sm-5 hidden-xs">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
	"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/footer_socs.php",
		"AREA_FILE_RECURSIVE" => "N",
		"EDIT_MODE" => "html"
	),
	false,
	array(
	"HIDE_ICONS" => "N",
		"ACTIVE_COMPONENT" => "Y"
	)
);?>
                </div>
                <div class="col-sm-7">
                        <?php
                        $APPLICATION->IncludeComponent("bitrix:sender.subscribe", "footer", array(
	"USE_PERSONALIZATION" => "Y",
		"SHOW_HIDDEN" => "N",
		"PAGE" => SITE_DIR."personal/subscribe/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "footer",
		"CONFIRMATION" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "undefined",
		"SET_TITLE" => "N",
		"MESS_TITLE" => "Подписаться"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
                </div>
            </div>
        </div>
    </div>
</div>
<footer id="footer">
    <div class="container">
        <div class="row">
            <ul class="footer-container">
                <li class="footer-item footer-1 col-sm-6 col-md-3">
                    <h4 class="footer-h4">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
	"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/footer_col_hedline_1.php",
		"AREA_FILE_RECURSIVE" => "N",
		"EDIT_MODE" => "html"
	),
	false,
	array(
	"HIDE_ICONS" => "N",
		"ACTIVE_COMPONENT" => "Y"
	)
);?>
					</h4>
                    <div class="footer-content">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
	"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/footer_col_1.php",
		"AREA_FILE_RECURSIVE" => "N",
		"EDIT_MODE" => "html"
	),
	false,
	array(
	"HIDE_ICONS" => "N",
		"ACTIVE_COMPONENT" => "N"
	)
);?>
                    </div>
                </li>
                <li class="footer-item footer-2 col-sm-6 col-md-3">
                    <h4 class="footer-h4">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR."include/footer_col_hedline_2.php",
                                "AREA_FILE_RECURSIVE" => "N",
                                "EDIT_MODE" => "html",
                            ),
                            false,
                            Array('HIDE_ICONS' => 'N')
                        );?>
					</h4>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/footer_col_2.php",
                            "AREA_FILE_RECURSIVE" => "N",
                            "EDIT_MODE" => "html",
                        ),
                        false,
                        Array('HIDE_ICONS' => 'N')
                    );?>
                </li>
                <li class="footer-item footer-3 col-sm-6 col-md-3">
                    <h4 class="footer-h4">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR."include/footer_col_hedline_3.php",
                                "AREA_FILE_RECURSIVE" => "N",
                                "EDIT_MODE" => "html",
                            ),
                            false,
                            Array('HIDE_ICONS' => 'N')
                        );?>
					</h4>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/footer_col_3.php",
                            "AREA_FILE_RECURSIVE" => "N",
                            "EDIT_MODE" => "html",
                        ),
                        false,
                        Array('HIDE_ICONS' => 'N')
                    );?>
                </li>
                <li class="footer-item footer-4 col-sm-6 col-md-3">
                    <h4 class="footer-h4">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR."include/footer_col_hedline_4.php",
                                "AREA_FILE_RECURSIVE" => "N",
                                "EDIT_MODE" => "html",
                            ),
                            false,
                            Array('HIDE_ICONS' => 'N')
                        );?>
					</h4>
                    <address>
                        <ul class="list-unstyled footer-contact-list">
                            <li><p class="link_address"><i class="fa fa-map-marker"></i> <span class="link_address-block"><strong><?php echo Loc::getMessage('FOOTER_ADRESS') ?></strong><br />
                                <span>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    Array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_address.php",
                                        "AREA_FILE_RECURSIVE" => "N",
                                        "EDIT_MODE" => "html",
                                    ),
                                    false,
                                    Array('HIDE_ICONS' => 'N')
                                );?></span>
                                    </span>
                                </p></li>
                            <li><p class="link_address"><i class="fa fa-phone"></i> <span class="link_address-block"><strong><?php echo Loc::getMessage('FOOTER_PHONE') ?></strong><br />
                               <span><?$APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR."include/contact.php",
                                            "AREA_FILE_RECURSIVE" => "N",
                                            "EDIT_MODE" => "html",
                                        ),
                                        false,
                                        Array('HIDE_ICONS' => 'N')
                                    );?></span>
                                    </span>
                                </p></li>
                            <li><p class="link_address"><i class="fa fa-envelope"></i> <span class="link_address-block"><strong><?php echo Loc::getMessage('FOOTER_EMAIL') ?></strong><br />
                                <span><a class="text-darker" href="mailto:<?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR."include/contact_info_email.php",
                                            "AREA_FILE_RECURSIVE" => "N",
                                            "EDIT_MODE" => "html",
                                        ),
                                        false,
                                        Array('HIDE_ICONS' => 'Y')
                                    );?>"><?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR."include/contact_info_email.php",
                                            "AREA_FILE_RECURSIVE" => "N",
                                            "EDIT_MODE" => "html",
                                        ),
                                        false,
                                        Array('HIDE_ICONS' => 'N')
                                    );?></a></span>
                                    </span>
                                </p></li>
                            <li><p class="link_address"><!-- <i class="fa fa-skype"></i> <span class="link_address-block"><strong><?php echo Loc::getMessage('FOOTER_SKYPE') ?></strong><br /> -->
                                <span><?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR."include/footer_skype.php",
                                            "AREA_FILE_RECURSIVE" => "N",
                                            "EDIT_MODE" => "html",
                                        ),
                                        false,
                                        Array('HIDE_ICONS' => 'N')
                                    );?></span>
                                </p></li>
                        </ul>
                    </address>
                </li>
            </ul>
        </div>
    </div>
    <div class="container hidden-sm hidden-md hidden-lg">
        <div class="row">
            <div class="col-sm-12 soc-mobile">
                <div class="soc-name"><?php echo Loc::getMessage('FOOTER_SOC') ?></div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/footer_socs.php",
                        "AREA_FILE_RECURSIVE" => "N",
                        "EDIT_MODE" => "html",
                    ),
                    false,
                    Array('HIDE_ICONS' => 'Y')
                );?>
            </div>
        </div>
    </div>
</footer>
<footer id="footer2">
    <div class="container">
        <div class="row">
            <div class="block-table">
                <div class="block-table-row">
                    <div class="col-xs-12 col-sm-6 block-table-td">
                        <div class="footer2 footer2-block">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                Array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/copyright.php",
                                    "AREA_FILE_RECURSIVE" => "N",
                                    "EDIT_MODE" => "html",
                                ),
                                false,
                                Array('HIDE_ICONS' => 'N')
                            );?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 block-table-td">
                        <div class="footer2-paymethod footer2-block">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                Array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/paymenticons.php",
                                    "AREA_FILE_RECURSIVE" => "N",
                                    "EDIT_MODE" => "html",
                                ),
                                false,
                                Array('HIDE_ICONS' => 'N')
                            );?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?if(!preg_match("~^".SITE_DIR."(catalog\/compare)/~", $APPLICATION->GetCurPage(true))):?>
    <?$APPLICATION->IncludeComponent(
    	"unisoftmedia:catalog.compare.list", 
    	"catalog", 
    	array(
    		"COMPONENT_TEMPLATE" => "catalog",
    		"IBLOCK_TYPE" => "catalog",
    		"IBLOCK_ID" => "9",
    		"AJAX_MODE" => "N",
    		"AJAX_OPTION_JUMP" => "N",
    		"AJAX_OPTION_STYLE" => "Y",
    		"AJAX_OPTION_HISTORY" => "N",
    		"AJAX_OPTION_ADDITIONAL" => "",
    		"COMPARE_URL" => SITE_DIR."catalog/compare/",
    		"NAME" => "CATALOG_COMPARE_LIST",
    		"ACTION_VARIABLE" => "action",
    		"PRODUCT_ID_VARIABLE" => "id",
    		"MAX_WIDTH" => "70",
    		"MAX_HEIGHT" => "70"
    	),
    	false,
    	array(
    		"HIDE_ICONS" => "N"
    	)
    );?>
<?endif?>