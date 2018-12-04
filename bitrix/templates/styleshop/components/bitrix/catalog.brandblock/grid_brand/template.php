<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult["BRAND_BLOCKS"]))
    return;
?>
<div class="row">
    <sectipn class="brands-grid">
        <?foreach ($arResult["BRAND_BLOCKS"] as $key => $arBB)
        {
            $useLink = $arBB['LINK'] !== false;

            if($arBB['TYPE'] == 'ONLY_PIC' || $arBB['TYPE'] == 'PIC_TEXT')
            {
                echo '<article class="col-xs-6 col-sm-4 col-md-3 col-lg-2">';
                if($useLink)
                    echo '<a href="'.str_replace('//', '/', $arParams['SEF_FOLDER'].strtolower($arBB['LINK']).'/').'">';

                ?><img src="<?php echo $arBB['PICT']['SRC'] ?>" alt="<?php echo htmlspecialcharsbx($arBB['NAME']) ?>" /><?

                if ($useLink)
                    echo '</a>';
                echo '</article>';
            }
        }?>
    </sectipn>
</div>