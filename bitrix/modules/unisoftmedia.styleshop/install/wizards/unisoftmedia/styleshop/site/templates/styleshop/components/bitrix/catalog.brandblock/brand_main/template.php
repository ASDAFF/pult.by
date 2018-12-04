<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if(empty($arResult["BRAND_BLOCKS"]))
	return;
$strRand = $this->randString(); ?>

<div class="brand-main-block">
    <?if($arParams['MESS_TITLE']) {
        ?>
        <h2 class="product-title"><?if($arParams['MESS_TITLE_LINK'] == 'Y'):?><a href="<?php echo SITE_DIR ?>brands/"><?endif?><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?><?if($arParams['MESS_TITLE_LINK'] == 'Y'):?></a><?endif?></h2>
        <?
    }

    $sliderOptions = array();
    $sliderOptions['nav'] =  ($arParams['SLIDER_NAV'] == 'Y');
    $sliderOptions['smartSpeed'] = $arParams['SLIDER_SPEED'];
    $sliderOptions['autoplay'] = ($arParams['SLIDER_AUTOPLAY'] == 'Y');
    $sliderOptions['autoplayTimeout'] = $arParams['SLIDER_SHOW_SPEED'];
    $sliderOptions['autoplayHoverPause'] = ($arParams['SLIDER_AUTOPLAY_HOVER_PAUSE'] == 'Y');
    if($arParams['MOUSE_DRAG'] == 'N')
        $sliderOptions['mouseDrag'] = false;
    $sliderOptions['loop'] = ($arParams['SLIDER_LOOP'] == 'Y');
    $sliderOptions['dots'] = false;
    $sliderOptions['margin'] = '10';

    $sliderOptions['responsive'] = [
        '0' => [
            'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_MOBILE']
        ],
        '480' => [
            'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_XS']
        ],
        '700' => [
            'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_SM']
        ],
        '992' => [
            'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_MD']
        ],
        '1200' => [
            'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_LG']
        ]
    ];
    ?>
	<div class="row">
		<div class="brand-block-slider">
			<ul class="brand-block-container owl-carousel Owlcarousel" data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>">
					<?$url = '';
					$i = 1;
					foreach ($arResult["BRAND_BLOCKS"] as $key => $arBB)
					{
						$urlEnabled = false;
						if(0 < strlen($arBB['LINK'])){
							$urlEnabled = true;
						}
					  if($urlEnabled){
						  $url = SITE_DIR.'brands/'.strtolower($arBB['LINK']).'/';
					  }
						$html = '';
			
						if($arBB['TYPE'] == 'ONLY_PIC' || $arBB['TYPE'] == 'PIC_TEXT')
						{
							$html .= '<img src="'.htmlspecialcharsbx($arBB['PICT']['SRC']).'"';
			
							if(strlen($arBB['NAME']) > 0)
								$html .= ' alt="'.htmlspecialcharsbx($arBB['NAME']).'"  title="'.htmlspecialcharsbx($arBB['NAME']).'"';
			
							$html .= '>';
							if($urlEnabled)
								$html = '<a href="'.$url.'">'.PHP_EOL.
								$html.PHP_EOL.
								'</a>';
			
							$html = '<li class="brand-block-container-li col-xs-12">'.PHP_EOL.
								$html.PHP_EOL.
								'</li>';
						}
						++$i;
						echo $html;
					}?>
			</ul>
		</div>
	</div>
</div>