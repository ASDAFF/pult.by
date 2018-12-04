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
<!-- BEGIN JIVOSITE CODE {literal} -->
<!-- <script type='text/javascript'>
    
(function(){ var widget_id = 'i183wJuNZP';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script> -->
<!-- {/literal} END JIVOSITE CODE -->



<!-- RedConnect -->
<!-- <script id="rhlpscrtg" type="text/javascript" charset="utf-8" async="async"
src="https://web.redhelper.ru/service/main.js?c=6030104"></script>
<div style="display: none"><a class="rc-copyright" 
href="http://redconnect.ru">Сервис обратного звонка RedConnect</a></div> -->
<!--/RedConnect -->
</body><!-- /body -->
</html>