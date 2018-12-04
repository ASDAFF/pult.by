<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$previousLevel = 0;
?>
<?if(is_array($arResult['SECTIONS']) && !empty($arResult['SECTIONS'])):?>
    <div class="catalog-categories row">
        <section>
            <?
            $description = '';
            foreach($arResult['SECTIONS'] as $key => $arItem):?>

                <?if($previousLevel && $arItem['DEPTH_LEVEL'] < $previousLevel) {
                    echo str_repeat('</section></div>'.$description.'</article>', ($previousLevel - $arItem['DEPTH_LEVEL']));
                }

                $is_parent = ($arItem['DEPTH_LEVEL'] == 1 && isset($arResult['SECTIONS'][1+$key]) && $arResult['SECTIONS'][1+$key]['DEPTH_LEVEL'] != 1);

                if($arItem['DEPTH_LEVEL'] == 1)
                {
                    $description = $arItem['DESCRIPTION']? '<div class="catalog-categories-description">'.cutText($arItem['DESCRIPTION'], 300).'</div>' : '';

                    $arPic = array();
                    if(intval($arItem['DETAIL_PICTURE']) > 0)
                    {
                        $arPic = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'],
                            array(
                                "width" => 120,
                                "height" => 120
                            ),
                            BX_RESIZE_IMAGE_PROPORTIONAL,
                            true
                        );
                    }
                    else 
                    {
                        $arPic['src'] = $this->GetFolder().'/images/no_photo.png';
                    }
                }

                if($is_parent)
                {
                    ?><article class="col-xs-12 col-sm-6">
                        <div class="row">
                            <div class="catalog-categories-item-pict col-xs-12 col-sm-4 col-md-3">
                                <?if(!empty($arPic)):?>
                                    <a href="<?php echo $arItem['SECTION_PAGE_URL'] ?>" title="<?php echo $arItem['NAME'] ?>">
                                        <img src="<?php echo $arPic['src'] ?>" alt="<?php echo $arItem['NAME'] ?>" />
                                    </a>
                                <?endif?>
                            </div>
                            <section class="list-inline col-xs-12 col-sm-8 col-md-9">
                                <article class="catalog-categories-item level-<?php echo $arItem['DEPTH_LEVEL'] ?>">
                                    <a href="<?php echo $arItem['SECTION_PAGE_URL'] ?>" title="<?php echo $arItem['NAME'] ?>"><?php echo $arItem['NAME'] ?></a>
                                </article><?
                }
                else if($arItem['DEPTH_LEVEL'] == 1)
                {
                    ?><article class="col-xs-12 col-sm-6">
                         <div class="row">
                            <div class="catalog-categories-item-pict col-xs-12 col-sm-4 col-md-3">
                                <?if(!empty($arPic)):?>
                                    <a href="<?php echo $arItem['SECTION_PAGE_URL'] ?>" title="<?php echo $arItem['NAME'] ?>">
                                        <img src="<?php echo $arPic['src'] ?>" alt="<?php echo $arItem['NAME'] ?>" />
                                    </a>
                                <?endif?>
                            </div>
                            <section class="list-inline col-xs-12 col-sm-8 col-md-9">
                                <article class="catalog-categories-item level-<?php echo $arItem['DEPTH_LEVEL'] ?>">
                                    <a href="<?php echo $arItem['SECTION_PAGE_URL'] ?>" title="<?php echo $arItem['NAME'] ?>"><?php echo $arItem['NAME'] ?></a>
                                </article>
                            </section>
                        </div>
                            <?php echo $description ?>
                        </article><?
                }
                else
                {
                    ?><article class="catalog-categories-item level-<?php echo $arItem['DEPTH_LEVEL'] ?>">
                        <a href="<?php echo $arItem['SECTION_PAGE_URL'] ?>" title="<?php echo $arItem['NAME'] ?>"><?php echo $arItem['NAME'] ?></a>
                        <?if($arParams['COUNT_ELEMENTS'] == 'Y') {?>
                            <small class="count">(<?=$arItem['ELEMENT_CNT']?>)</small>
                        <?}?>
                    </article><?
                }

                $previousLevel = $arItem['DEPTH_LEVEL'];?>


            <?endforeach?>

            <?if ($previousLevel > 1)//close last item tags
            {
                echo  str_repeat('</section></div>'.$description.'</article>', ($previousLevel-1) );
            }?>
        </section>
    </div>
<?endif?>