<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__); ?>

<?if($curPage != SITE_DIR.'index.php'):?>

</div>
    
<!--container--></div>
<!--row--></div>
<?endif?>

</main>
<?/***************** footer *******************/?>
        <?$APPLICATION->IncludeComponent(
        "bitrix:main.include", 
        ".default",
        Array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => SITE_DIR."include/footer/type1.php",
            "AREA_FILE_RECURSIVE" => "N",
            "EDIT_MODE" => "html"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
    <?/***************** footer end *******************/?>
</div>
</body><!-- /body -->
</html>