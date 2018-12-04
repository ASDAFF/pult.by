<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult["BRAND_BLOCKS"]))
    return;
?>
<div class="brands">
    <div class="brands-index-list">
        <?
        $index = '';
        foreach($arResult["BRAND_BLOCKS"] as $alfa)
        {
            $alfa['NAME'] = strtoupper($alfa['NAME']);
            if($index == $alfa['NAME']{0})
                continue;
            $index = htmlspecialcharsbx($alfa['NAME']{0});
            ?><a href="#<?=$index?>"><?=$index?></a><?
        }?>
    </div>
    <div class="brands-container">
        <ul class="list-unstyled brands-container-list">
 <?
        $index = '';
        foreach ($arResult["BRAND_BLOCKS"] as $key => $arBB)
        {
            $useLink = $arBB['LINK'] !== false;
            if (!$useLink)
                continue;

            if($index && $index != $arBB['NAME']{0})
                echo '</ul></li></div>';

            $arBB['NAME'] = htmlspecialcharsbx($arBB['NAME']);

            if($index != $arBB['NAME']{0})
                echo '<div class="brands-block"><li class="brands-container-list-name col-xs-1" id="'.strtoupper($arBB['NAME']{0}).'">'.strtoupper($arBB['NAME']{0}).'</li><li class="col-xs-11 pull-right"><ul class="list-unstyled">';

            $arBB['LINK'] = htmlspecialcharsbx($arBB['LINK']);
            ?>
            <li>
               <a href="<?php echo str_replace('//', '/', $arParams['SEF_FOLDER'].strtolower($arBB['LINK']).'/') ?>"><?php echo $arBB['NAME'] ?></a>
            </li>
<?
            $index = $arBB['NAME']{0};
        }

        if($index)
            echo '</ul></li></div>';

        ?>     
        </ul>
    </div>
</div>