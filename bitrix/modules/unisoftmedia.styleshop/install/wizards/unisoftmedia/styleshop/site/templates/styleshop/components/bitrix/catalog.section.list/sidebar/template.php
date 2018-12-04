<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$previousLevel = 0;
?>
<?if(is_array($arResult['SECTIONS']) && !empty($arResult['SECTIONS'])):?>
    <div class="panel panel-default hidden-xs hidden-sm">
        <div class="panel-heading"><?=$arResult['SECTION']['NAME']?></div>
        <div class="panel-body section-list">
            <ul>
                <?foreach($arResult['SECTIONS'] as $arItem):?>

                    <?if($previousLevel && $arItem['DEPTH_LEVEL'] < $previousLevel) {
                        echo str_repeat('</ul></li>', ($previousLevel - $arItem['DEPTH_LEVEL']));
                    }?>

                    <li class="menu-item-link menu-item-level-<?=$arItem['DEPTH_LEVEL'] ?>">
                        <a title="<?=$arItem['NAME']?>" href="<?=$arItem['SECTION_PAGE_URL']?>">
                            <span class="name"><?=$arItem['NAME']?></span>
                        </a>
                        <?if($arParams['COUNT_ELEMENTS'] == 'Y') {?>
                            <small class="count">(<?=$arItem['ELEMENT_CNT']?>)</small>
                        <?}?>

                <?endforeach?>

                <?if ($previousLevel > 1)//close last item tags
                {
                    echo  str_repeat('</ul></li>', ($previousLevel-1) );
                }?>

            </ul>
        </div>
    </div>
<?endif?>