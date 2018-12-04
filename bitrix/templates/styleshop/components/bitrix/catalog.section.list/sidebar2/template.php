<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$previousLevel = 0;
?>
<?if(is_array($arResult['SECTIONS']) && !empty($arResult['SECTIONS'])):?>
    <nav class="leftmenu block">
			<ul class="nav nav-pills nav-stacked">
        <?foreach($arResult['SECTIONS'] as $arItem):?>

            <?if($previousLevel && $arItem['DEPTH_LEVEL'] < $previousLevel) {
                echo str_repeat('</ul></li>', ($previousLevel - $arItem['DEPTH_LEVEL']));
            }?>
            <li>
              <a title="<?=$arItem['NAME']?>" href="<?=$arItem['SECTION_PAGE_URL']?>">
                 <span class="name"><?=$arItem['NAME']?></span>
              </a>
        <?endforeach?>

        <?if ($previousLevel > 1)//close last item tags
        {
            echo  str_repeat('</ul></li>', ($previousLevel-1) );
        }?>

    	</ul>
	</nav>
<?endif?>