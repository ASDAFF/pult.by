<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (!empty($arResult))
{ ?>
    <ul class="nav navbar-nav responsive-menu" id="top-menu"><?

        $previousLevel = 0;
        $mmenu = '';

        foreach($arResult as $arItem)
        {
            if (!$arItem['IS_PARENT'] && $arItem['PERMISSION'] < 'D')
                continue;

            if ($previousLevel && $arItem['DEPTH_LEVEL'] < $previousLevel) {
                $close = str_repeat('</ul></li>', ($previousLevel - $arItem['DEPTH_LEVEL']));
                $mmenu .= $close;
                echo $close;
                $close = '';
            }


            /* ---------- LINK ---------- */
            ?><li class="menu-item-link menu-item-level-<?php echo $arItem['DEPTH_LEVEL'] ?><?php if('TYPE-1' == $arItem['UF_MENU_TYPE']):?> dropdown-full type-1<?php elseif('TYPE-2' == $arItem['UF_MENU_TYPE']): ?> dropdown-full type-2 <?elseif('TYPE-3' == $arItem['UF_MENU_TYPE']): ?> dropdown-full type-3<?php endif; ?><?php echo $arItem['IS_PARENT'] ? ' dropdown' : '' ?>"<?if ($arItem['IS_PARENT']):?> data-hover="dropdown"<?endif?>>
                <a title="<?=$arItem['TEXT']?>" href="<?=$arItem['LINK']?>" class="<?if ($arItem['IS_PARENT']):?>dropdown-toggle<?endif?><?if ($arItem['SELECTED']):?> active<?endif?>"<?if ($arItem['IS_PARENT']):?> data-toggle="dropdown" role="button"<?endif?>><?=$arItem['TEXT']?><?if ($arItem['IS_PARENT']):?> <span class="caret"></span><?endif?></a>

                    <? $mmenu .= '<li class="mmenu-link"><a href="'.$arItem['LINK'].'">'.$arItem['TEXT'].'</a>';

             if ($arItem['IS_PARENT']): // is_parent ?>
            <ul<?php echo 1 == $arItem['DEPTH_LEVEL']? ' class="dropdown-menu dropdown-menu-right animated fadeIn"' : '' ?>>

            <?$mmenu .= '<ul>';

        endif; // is_parent

        $previousLevel = $arItem['DEPTH_LEVEL'];

        } ?>

        <li class="menu-item-link dropdown more" data-hover="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"></a>
            <ul class="dropdown-menu dropdown-menu-right animated fadeIn" role="menu"></ul>
        </li>

        <?if ($previousLevel > 1)//close last item tags
        {
            $close = str_repeat('</ul></li>', ($previousLevel-1) );
            $mmenu .= $close;
            echo $close;
            $close = '';
        }

        $this->SetViewTarget("mmenu");
        echo $mmenu;
        $this->EndViewTarget();

        ?>

    </ul>
<?php } ?>