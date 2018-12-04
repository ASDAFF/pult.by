<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
if('Y' == $arParams['SORTERED_SHOW'] && is_array($arResult['SORTER']) && !empty($arResult['SORTER']))
{
    ?><div class="sort-by">
        <label><?=GetMessage('SORTER')?></label>
        <span class="selectbox">
            <div class="select">
                <div class="text"><?=$arResult['SORTER_SELECTED']['NAME']?></div>
                <p class="trigger"><i class="arrow"></i></p>
            </div>
            <div class="dropdown hide" style="position: absolute;">
                <ul><?
                
                foreach($arResult['SORTER'] as $arSort)
                {
                    ?><li <?if($arSort['ASC']['SELECTED']):?>class="sel selected"<?endif?>><a href="<?=$arSort['ASC']['URL']?>"><?=$arSort['ASC']['NAME']?></a></li><?
                }
              ?></ul>
            </div>
        </span>  
    </div><?
    ?><a class="sortbutton<?=($arResult['SORTER_SELECTED']['TYPE'] == 'asc')? ' up' : ' down'?>" href="<?=$arResult['SORTER_SELECTED']['URL2']?>"></a><?
}
?><div class="view-mode">
    <label><?=GetMessage('VIEW')?></label>
    <div class="view-mode-block-one selected">
        <a href="javascript:void(0)" class="grid images-grid" data-view="view-mode-1"></a>
    </div>
    <div class="view-mode-block-two">
        <a href="javascript:void(0)" class="grid images-grid" data-view="view-mode-2"></a>
    </div>
</div><?
if('Y' == $arParams['OUTPUT_LIST_NUM_SHOW'] && is_array($arResult['OUTPUT_LIST_NUM']) && !empty($arResult['OUTPUT_LIST_NUM']))
{
    ?><div class="limiter">
        <label><?=GetMessage('LIST_NUM')?></label>
        <span class="selectbox">
            <div class="select">
                <div class="text"><?=$arResult['OUTPUT_LIST_NUM_SELECTED']['NAME']?></div>
                <p class="trigger"><i class="arrow"></i></p>
            </div>
            <div class="dropdown hide" style="position: absolute;">
                <ul>
    <?
    foreach($arResult['OUTPUT_LIST_NUM'] as $items)
    {
        ?><li <?if($items['SELECTED']):?>class="sel selected"<?endif?>><a href="<?=$items['URL']?>"><?=$items['NAME']?></a></li><?
    }
                ?></ul>
            </div>
        </span>
    </div><?
}
$APPLICATION->ShowViewContent("nav_top");