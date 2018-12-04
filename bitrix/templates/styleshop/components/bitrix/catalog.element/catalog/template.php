<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
global $theme;
$OFFERS = (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))? true : false;

if($arResult['CATALOG_SUBSCRIBE'] == 'Y')
	$showSubscribeBtn = true;
else
	$showSubscribeBtn = false;

$strMainID = $this->GetEditAreaId($arResult['ID']).$arResult['ID_RAND'];
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$arItemIDs = array(
  'PRODUCT_ID' => $arResult['ID'],
	'ID' => $strMainID,
  'NAME' => $strMainID.'_name',
	'PICT' => $strMainID.'_pict',
	'SECOND_PICT' => $strMainID.'_secondpict',
	'MAIN_PROPS' => $strMainID.'_main_props',
  'AVAILABLE' => $strMainID.'_available',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
  'NAME_MEASURE' => $strMainID.'_name_measure',
	'BUY_LINK' => $strMainID.'_buy_link',
  'COMPARE' => $strMainID.'_compare',
  'FAVORITES' => $strMainID.'_favorites',
	'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
  'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'PRICE' => $strMainID.'_price',
  'OLD_PRICE' => $strMainID.'_old_price',
	'DSC_PERC' => $strMainID.'_dsc_perc',
	'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
  'SLIDER_LIST' => $strMainID.'_slider_list',
	'PROP_DIV' => $strMainID.'_sku_tree',
	'PROP' => $strMainID.'_prop_',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
  'OFFER_GROUP' => $strMainID.'_set_group_',
  'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail'
 );
$arJSParams = array(
	'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
	'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
	'USE_STORE' => ($arParams['USE_STORE'] == 'Y' && !$arParams['AJAX_QUICKVIEW']),
	'SHOW_ADD_BASKET_BTN' => false,
	'SHOW_BUY_BTN' => true,
	'SHOW_ABSENT' => true,
	'USE_SUBSCRIBE' => $showSubscribeBtn,
	'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
	'SECOND_PICT' => $arResult['SECOND_PICT'],
	'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
	'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
	'PRODUCT' => array(
		'ID' => $arItem['ID'],
		'NAME' => $arItemIDs['NAME'],
	),
	'VISUAL' => array(
		'ID' => $arItemIDs['ID'],
    'AVAILABLE_ID' => $arItemIDs['AVAILABLE'],
		'PICT_ID' => $arItemIDs['PICT'],
		'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
		'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
 		'NAME_MEASURE' => $arItemIDs['NAME_MEASURE'],
		'PRICE_ID' => $arItemIDs['PRICE'],
    'OLD_PRICE_ID' => $arItemIDs['OLD_PRICE'],
		'TREE_ID' => $arItemIDs['PROP_DIV'],
		'TREE_ITEM_ID' => $arItemIDs['PROP'],
		'BUY_ID' => $arItemIDs['BUY_LINK'],
    'COMPARE_ID' => $arItemIDs['COMPARE'],
    'FAVORITES_ID' => $arItemIDs['FAVORITES'],
		'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
		'DSC_PERC' => $arItemIDs['DSC_PERC'],
		'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
		'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
    'SLIDER_LIST_OF_ID' => $arItemIDs['SLIDER_LIST_OF_ID'],
    'SLIDER_LIST' => $arItemIDs['SLIDER_LIST'],
    'OFFER_GROUP' =>$arItemIDs['OFFER_GROUP'],
    'IN_STOCK' => Loc::getmessage('CATALOG_IN_STOCK'),
    'NOT_STOCK' => Loc::getmessage('CATALOG_NOT_STOCK'),
    'SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_LINK'],
    'NOT_AVAILABLE_MESS' => $arItemIDs['NOT_AVAILABLE_MESS']
	),
	'OFFERS' => $arResult['JS_OFFERS'],
  'QUICKBUY_ID' => $arResult['PROPERTIES']['ID']['VALUE'],
  'PRODUCTDAY_ID' => $arResult['PROPERTIES']['ID']['VALUE'],
  'QUICKBUY_PROP' => $arResult['PROPERTIES']['QUICKBUY']['VALUE'],
  'PRODUCTDAY_PROP' => $arResult['PROPERTIES']['PRODUCTDAY']['VALUE'], 
	'OFFER_SELECTED' => $arResult['OFFERS_SELECTED']
);

	if($OFFERS)
	{
		$arSkuProps = array();
    if(is_array($arResult['SKU_PROPS']) && !empty($arResult['SKU_PROPS']))
    {
    	ob_start();
?>
        <div class="block-sku" id="<?php echo $arItemIDs['PROP_DIV']; ?>">
<?
            foreach($arResult['SKU_PROPS'] as $arProp)
            {
              if (!isset($arResult['OFFERS_PROP'][$arProp['CODE']]))
               continue;

              $arSkuProps[] = array(
								'ID' => $arProp['ID'],
								'SHOW_MODE' => $arProp['SHOW_MODE'],
								'VALUES_COUNT' => $arProp['VALUES_COUNT']
							);
							if(is_array($arProp['VALUES']) && !empty($arProp['VALUES']))
							{
								if($arParams['SKU_VIEW'] == 'DROPDOWN_LIST')
								{
									?>
									<div class="block-sku-detail-sku dropdown" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
										<span class="btn dropdown-toggle btn-default sku-name" data-toggle="dropdown" role="button" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_selected"><?php echo htmlspecialcharsex($arProp['NAME']); ?>: 
											<?if('PICT' == $arProp['SHOW_MODE']):?>
												<span class="cnt_item"></span>
											<?endif?>
											<span class="selection"></span>
											<span class="caret"></span>
										</span>
										<ul class="animated dropdown-menu fadeIn" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
											<?foreach($arProp['VALUES'] as $key => $arOneValue) {
	                      if('PICT' == $arProp['SHOW_MODE']) {
	?>
	                        <li
	                        style="display: none;"
	                        data-onevalue="<?=$arOneValue['ID']; ?>"
	                        data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
	                        data-color="<?=$arOneValue['PICT']['SRC']?>"
	                        data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
	                            <a href="javascript:void(0)" class="color cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>">
		                            <span class="cnt_item"<?if(isset($arOneValue['PICT']['SRC'])):?> style="background-image:url('<?=$arOneValue['PICT']['SRC']?>');"<?endif?>></span>
		                            <?=$arOneValue['NAME']?>
	                            </a>
	                        </li>
	<?
	                      } else{
	?>
	                        <li
	                        style="display: none;"
	                        data-onevalue="<?=$arOneValue['ID']; ?>"
	                        data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
	                        data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
	                            <a href="javascript:void(0)" class="size cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><?=htmlspecialcharsex($arOneValue['NAME'])?></a>
	                        </li>
	<?
	                      }
	                    }?>
										</ul>
									</div><?
								}
								else if($arParams['SKU_VIEW'] == 'DROPDOWN_LIST2')
								{
									?>
									<div class="block-sku-detail-sku <?if('PICT' != $arProp['SHOW_MODE']):?>dropdown<?endif?>" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
                    <span class="<?if('PICT' == $arProp['SHOW_MODE']):?>block-sku-detail-sku-name<?else:?>btn dropdown-toggle btn-default sku-name<?endif?>" <?if('PICT' != $arProp['SHOW_MODE']):?>data-toggle="dropdown" role="button"<?endif?>><?php echo htmlspecialcharsex($arProp['NAME']); ?>: <span class="selection"></span>
                    	<?if('PICT' != $arProp['SHOW_MODE']):?>
                    		<span class="caret"></span>
                    	<?endif?>
                    </span>
                    
                        <ul class="<?if('PICT' == $arProp['SHOW_MODE']):?>block-sku-detail-sku-container-list<?else:?>animated dropdown-menu fadeIn<?endif?>" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
<?
                          foreach($arProp['VALUES'] as $key => $arOneValue) {
                            if('PICT' == $arProp['SHOW_MODE']) {
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <span class="color cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><span class="cnt_item"<?if(isset($arOneValue['PICT']['SRC'])):?> style="background-image:url('<?=$arOneValue['PICT']['SRC']?>');"<?endif?>></span></span>
                              </li>
<?
                            } else{
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <a href="javascript:void(0)" class="size cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><?=htmlspecialcharsex($arOneValue['NAME'])?></a>
                              </li>
<?
                            }
                          }
?>
                        </ul>
                      
                  </div><?
								}
								else if($arParams['SKU_VIEW'] == 'LIST2')
								{
									?><div class="block-sku-detail-sku" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
                    <span class="block-sku-detail-sku-name" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_selected"><?php echo htmlspecialcharsex($arProp['NAME']); ?>: <span class="selection"></span>
                    </span>
                    <div class="block-sku-detail-sku-container">
                    	<div class="bx_scu">
                        <ul class="<?if('PICT' != $arProp['SHOW_MODE']):?>block-sku-detail-sku-container-list<?else:?>block-sku-detail-sku-container-list2<?endif?>" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
<?
                          foreach($arProp['VALUES'] as $key => $arOneValue) {
                            if('PICT' == $arProp['SHOW_MODE']) {
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <span class="color cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><span class="cnt_item" style="background-image:url('<?=(isset($arOneValue['PICT2']['SRC']))?$arOneValue['PICT2']['SRC']:$arOneValue['PICT']['SRC']?>');"></span></span>
                              </li>
<?
                            } else{
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              <?if(isset($arOneValue['DESCRIPTION'])):?>
	                              data-container="body"
	                              data-toggle="popover"
	                              data-placement="top"
	                              data-html="true"
	                              data-content="<?php echo $arOneValue['DESCRIPTION'] ?>"
                              <?endif?>
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <span class="size cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><?=htmlspecialcharsex($arOneValue['NAME'])?></span>
                              </li>
<?
                            }
                          }
?>
                        </ul>
                      </div>
                    </div>
                  </div><?
								}
								else
								{
								?><div class="block-sku-detail-sku" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
                    <span class="block-sku-detail-sku-name" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_selected"><?php echo htmlspecialcharsex($arProp['NAME']); ?>: <span class="selection"></span>
                    </span>
                    <div class="block-sku-detail-sku-container">
                    	<div class="bx_scu">
                        <ul class="block-sku-detail-sku-container-list" id="<?php echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
<?
                          foreach($arProp['VALUES'] as $key => $arOneValue) {
                            if('PICT' == $arProp['SHOW_MODE']) {
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <span class="color cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><span class="cnt_item" style="background-image:url('<?=$arOneValue['PICT']['SRC']?>');"></span></span>
                              </li>
<?
                            } else {
?>
                              <li
                              style="display: none;"
                              data-onevalue="<?=$arOneValue['ID']; ?>"
                              <?if(isset($arOneValue['DESCRIPTION'])):?>
	                              data-container="body"
	                              data-toggle="popover"
	                              data-placement="top"
	                              data-html="true"
	                              data-content="<?php echo $arOneValue['DESCRIPTION'] ?>"
                              <?endif?>
                              data-sku-title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"
                              data-treevalue="<?php echo $arProp['ID'].'_'.$arOneValue['ID']; ?>">
                                  <span class="size cnt" title="<?=htmlspecialcharsex($arOneValue['NAME'])?>"><?=htmlspecialcharsex($arOneValue['NAME'])?></span>
                              </li>
<?
                            }
                          }
?>
                        </ul>
                      </div>
                    </div>
                  </div><?
								}
								
							}
            }
?>
        </div>
<?
			$arSkuTemplate = ob_get_contents();
		  ob_end_clean();
    }
    $arJSParams['TREE_PROPS'] = $arSkuProps;
	}
?>
<div class="detail-product<?=($arParams['AJAX_QUICKVIEW'])? ' quickview' : '' ?> js-item" id="<?=$arJSParams['VISUAL']['ID']?>" itemscope itemtype="http://schema.org/Product">
	<div class="detail-product-container <?=(!$arParams['AJAX_QUICKVIEW'])? 'row' : 'clearfix' ?>">
		<header class="product-name col-xs-12 <?if($arParams['AJAX_QUICKVIEW']):?>col-sm-7<?elseif($arParams['COLUMN'] == 'one_column'):?>col-sm-6<?elseif($arParams['CAROUSEL_DOTS_VERTICAL'] == 'Y'):?>col-sm-6<?else:?>col-sm-7<?endif?>">
				<h1 itemprop="name"><?php 
					echo (
						isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
						? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
						: $arResult["NAME"]
					);
					?></h1>
<?if($arResult['PROPERTIES']['SALELEADER']['VALUE'] != ""):?>
<div style="background: #fdcc0a; padding: 3px; width: 150px">Реальное&nbsp; наличие</div>
<?endif;?>
			</header>
		<div class="detail-product-left col-xs-12 <?if($arParams['AJAX_QUICKVIEW']):?>col-sm-5<?elseif($arParams['COLUMN'] == 'one_column'):?>col-sm-6<?elseif($arParams['CAROUSEL_DOTS_VERTICAL'] != 'Y'):?>col-sm-5<?else:?>col-sm-6<?endif?>">
			<div class="detail-product-images">
				<?
				if(!$arParams['AJAX_QUICKVIEW'] && $arParams['USE_ZOOM'] == 'Y')
				{
					$elevateZoom = [];
					$elevateZoom['zoomType'] = $arParams['PARAMS']['ZOOM'];
					$elevateZoom['borderSize'] = $arParams['PARAMS']['ZOOM_LENS_BORDER_SIZE'];
					$elevateZoom['borderColour'] = $arParams['PARAMS']['ZOOM_LENS_BORDER_SIZE_COLOR'];
					$elevateZoom['lensShape'] = $arParams['PARAMS']['ZOOM_LENS_LENS_SHAPE'];
					$elevateZoom['lensColour'] = $arParams['PARAMS']['ZOOM_LENS_COLOUR'];
					$elevateZoom['lensBorderSize'] = $arParams['PARAMS']['ZOOM_LENS_BORDER'];
					if(0 <= intval($arParams['PARAMS']['ZOOM_EASING']))
						$elevateZoom['easing'] = true;
					if('Y' == $arParams['PARAMS']['ZOOM_MOUSEWHEELZOOM'])
						$elevateZoom['scrollZoom'] = true;
					$elevateZoom['cursor'] = $arParams['PARAMS']['ZOOM_CURSOR'];
					if(0 < intval($arParams['PARAMS']['ZOOM_WINDOW_POSITION']))
						$elevateZoom['zoomWindowPosition'] = intval($arParams['PARAMS']['ZOOM_WINDOW_POSITION']);

					if(0 <= intval($arParams['PARAMS']['ZOOM_WINDOW_OFFETX']))
						$elevateZoom['zoomWindowOffetx'] = intval($arParams['PARAMS']['ZOOM_WINDOW_OFFETX']);
					$elevateZoom['zoomWindowWidth'] = $arParams['PARAMS']['ZOOM_WINDOW_WIDTH'];
					$elevateZoom['zoomWindowHeight'] = $arParams['PARAMS']['ZOOM_WINDOW_HEIGHT'];
					$elevateZoom['lensSize'] = $arParams['PARAMS']['ZOOM_LENS_SIZE'];
				}

				$sliderOptions = [];

				if(!$arParams['AJAX_QUICKVIEW'])
					$sliderOptions['dotsVertical'] = ($arParams['CAROUSEL_DOTS_VERTICAL'] == 'Y' || $arParams['COLUMN'] == 'one_column');
				else
					$sliderOptions['dotsVertical'] = false;

				$sliderOptions['zoom'] = (!$arParams['AJAX_QUICKVIEW'] && $arParams['USE_ZOOM'] == 'Y');

				$sliderOptions['responsive'] = [
					'0' => [
						'dotsVertical' => false,
						'zoom' => false
					],
					'992' => [
						'dotsVertical' => $sliderOptions['dotsVertical'],
						'zoom' => false
					],
					'1025' => [
						'zoom' => $sliderOptions['zoom']
					]
				];

					$strTitle = (
						isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
						? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
						: $arResult['NAME']
					);
					$strAlt = (
						isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
						? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
						: $arResult['NAME']
					);

					if(!$OFFERS && isset($arResult['MORE_PHOTO_COUNT']) && 0 < $arResult['MORE_PHOTO_COUNT'])
					{
?>
						<div class="detail-product-images-photorama">
<?
							$bigSmall = '';
							foreach($arResult['MORE_PHOTO'] as $k => $arMore_photo):?>
								<?

								if(isset($arMore_photo['ID'])) {
									$arPic = CFile::ResizeImageGet($arMore_photo['ID'],
										array(
											"width" => 512,
											"height" => 700
										),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);

									$arPicSmall = CFile::ResizeImageGet($arMore_photo['ID'],
										array(
											"width" => 90,
											"height" => 100
										),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);
								} else {
									$arPic = array(
										'src' => $arMore_photo['SRC'],
										'width' => $arMore_photo['WIDTH'],
										'height' => $arMore_photo['HEIGHT']
									);
									$arPicSmall = array(
										'src' => $arMore_photo['SRC'],
										'width' => $arMore_photo['WIDTH'],
										'height' => $arMore_photo['HEIGHT']
									);
								}

								$bigSmall .= '<li data-dot="<img width=\''.$arPicSmall["width"].'\' height=\''.$arPicSmall["height"].'\' class=\'img-thumbnail\' src=\''.$arPicSmall["src"].'\' />">';
								$bigSmall .= '<a data-url="'.$arResult["DETAIL_PAGE_URL"].'" data-offerid="'.$arResult['ID'].'" class="owl-zoom" data-zoom-image="'.$arMore_photo['SRC'].'" href="#" title="'.$strTitle.'">';
								$bigSmall .= '<img class="img-thumbnail" src="'.$arPic['src'].'" alt="'.$strAlt.'" />';
								$bigSmall .= '</a>';
								$bigSmall .= '</li>';
?>
						<?endforeach?>
      				<div class="detail-product-images-photorama-img">
      					<ul class="list-unstyled owl-carousel owlMainCarousel" <?if(!$arParams['AJAX_QUICKVIEW'] && $arParams['USE_ZOOM'] == 'Y'):?>data-zoom-options="<?php echo CUtil::PhpToJSObject($elevateZoom, false, true); ?>"<?endif?> data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>">
      						<?php echo $bigSmall ?>
      					</ul>
      					<?if(!$arParams['AJAX_QUICKVIEW']):?>
      						<div class="fancy-image clearfix hidden-xs"><a href="#"><?php echo Loc::getmessage('FANCY_GALLERY') ?></a></div>
        				<?endif?>
        			</div>
        		</div>
<?
					}
					else if($OFFERS)
          {
          	foreach($arResult['OFFERS'] as $key => $arOneOffer)
          	{
          		if(!isset($arOneOffer['MORE_PHOTO_COUNT']) || 0 >= $arOneOffer['MORE_PHOTO_COUNT'])
								continue;
?>
        			<div id="<?=$arJSParams['VISUAL']['SLIDER_LIST_OF_ID'].$arOneOffer['ID'];?>" class="detail-product-images-photorama"<?php echo ($key == $arResult['OFFERS_SELECTED'])? '' : ' style="display: none;"'; ?>>
<?
								$bigSmall = '';
      							foreach($arOneOffer['MORE_PHOTO'] as $k => $arMore_photo):?>
      								<?

    									if(isset($arMore_photo['ID'])) {
												$arPic = CFile::ResizeImageGet($arMore_photo['ID'],
													array(
														"width" => 512,
														"height" => 700
													),
													BX_RESIZE_IMAGE_PROPORTIONAL,
													true
												);

      									$arPicSmall = CFile::ResizeImageGet($arMore_photo['ID'],
													array(
														"width" => 90,
														"height" => 100
													),
													BX_RESIZE_IMAGE_PROPORTIONAL,
													true
												);
											} else {
												$arPic = array(
													'src' => $arMore_photo['SRC'],
													'width' => $arMore_photo['WIDTH'],
													'height' => $arMore_photo['HEIGHT']
												);
												$arPicSmall = array(
													'src' => $arMore_photo['SRC'],
													'width' => $arMore_photo['WIDTH'],
													'height' => $arMore_photo['HEIGHT']
												);
											}

											$bigSmall .= '<li data-dot="<img width=\''.$arPicSmall["width"].'\' height=\''.$arPicSmall["height"].'\' class=\'img-thumbnail\' src=\''.$arPicSmall["src"].'\' />">';
											$bigSmall .= '<a data-url="'.$arResult["DETAIL_PAGE_URL"].'" data-offerid="'.$arOneOffer['ID'].'" class="owl-zoom" data-zoom-image="'.$arMore_photo['SRC'].'" href="#" title="'.$strTitle.'">';
											$bigSmall .= '<img class="img-thumbnail" src="'.$arPic['src'].'" alt="'.$strAlt.'" />';
											$bigSmall .= '</a>';
											$bigSmall .= '</li>';

?>
										<?endforeach?><?$sliderOptions['dotsVertical'] = false?>
        				<div class="detail-product-images-photorama-img">
        					<ul class="list-unstyled owl-carousel owlMainCarousel" <?if(!$arParams['AJAX_QUICKVIEW'] && $arParams['USE_ZOOM'] == 'Y'):?>data-zoom-options="<?php echo CUtil::PhpToJSObject($elevateZoom, false, true); ?>"<?endif?> data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>">
        						<?php echo $bigSmall ?>
        					</ul>
        				</div>
        				<?if(!$arParams['AJAX_QUICKVIEW']):?>
      						<div class="fancy-image clearfix hidden-xs"><a href="#"><?php echo Loc::getmessage('FANCY_GALLERY') ?></a></div>
        				<?endif?>
        			</div>
          	<?}
          }
          unset($bigSmall, $arOneOffer, $arMore_photo);
				?>
				<?
				/************************************************* ajax popup gallery ****************************************************/
				$ajax_gallery = false;
				if($arParams['AJAX'] && isset($_REQUEST['ajax_gallery']) && !empty($_REQUEST['ajax_gallery']))
				{
					$ajax_gallery = true;
					$APPLICATION->RestartBuffer();
				}

				if($ajax_gallery)
				{
					$arOneOffer = [];
					if($OFFERS)
          {
          	$offerid = isset($_REQUEST['offerid'])? intval($_REQUEST['offerid']) : 0;
          	foreach($arResult['OFFERS'] as $Offer)
          	{
          		if($Offer['ID'] == $offerid)
          		{
          			$arOneOffer = $Offer;
          			break;
          		}
          	}
          	unset($Offer);

          	if(empty($arOneOffer))
          		$arOneOffer = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']];
          }
          else
          {
          	$arOneOffer = $arResult;
          }
?>
        		<div class="gallery-popup col-sm-12">
	        		<div class="row">
<?
        			$big_image = array();
    					$big_image = (isset($arOneOffer['MORE_PHOTO']) && 0 < $arOneOffer['MORE_PHOTO_COUNT'])? $arOneOffer['MORE_PHOTO'][0] : '';
?>
	        			<div class="col-sm-9">
	        				<div class="big-image">
        						<img src="<?php echo $big_image['SRC'] ?>" alt="" />
	        				</div>
	        			</div>
	        			<div class="col-sm-3">
		      				<ul class="list-unstyled row">
		    						<?if(is_array($arOneOffer['PREVIEW_PICTURE'])):?>
		    						<?$arPicSmall = CFile::ResizeImageGet($arOneOffer['PREVIEW_PICTURE']['ID'],
													array(
														"width" => 70,
														"height" => 90
													),
													BX_RESIZE_IMAGE_PROPORTIONAL,
													true
												);?>
		      						<li class="col-sm-6">
												<a class="active" data-image="<?php echo $arOneOffer['PREVIEW_PICTURE']['SRC'] ?>" href="#">
														<img class="img-thumbnail" src="<?=$arPicSmall['src']?>" alt="" />
													</a>
											</li>
										<?endif?>
										<?if(isset($arOneOffer['MORE_PHOTO']) && 0 < $arOneOffer['MORE_PHOTO_COUNT']):?>
		    							<?foreach ($arOneOffer['MORE_PHOTO'] as $k => $arMore_photo):?>
		    							<?$arPicSmall = CFile::ResizeImageGet($arMore_photo['ID'],
													array(
														"width" => 70,
														"height" => 90
													),
													BX_RESIZE_IMAGE_PROPORTIONAL,
													true
												);?>
												<li class="col-sm-6">
													<a class="<?=(!$OFFERS && $k == 0)? 'active': ''?>" href="#" data-image="<?php echo $arMore_photo['SRC'] ?>">
															<img class="img-thumbnail" src="<?=$arPicSmall['src']?>" />
													</a>
												</li>
											<?endforeach?>
										<?endif?>
		      				</ul>
	      				</div>
      				</div>
        		</div>
<?
				}

				if($ajax_gallery)
					die;
				/************************************************* ajax popup gallery end ****************************************************/
				?>
			</div>
			<?if(!$arParams['AJAX_QUICKVIEW']):?>
				<section class="social-likes-container">
					<span class="social-likes-name"><?php echo Loc::getmessage('SOCIAL_LIKE') ?></span>
					<div class="social-likes">
						<div class="vkontakte" title="<?php echo Loc::getmessage('SOCIAL_VK_TITLE') ?>"></div>
						<div class="facebook" title="<?php echo Loc::getmessage('SOCIAL_FB_TITLE') ?>"></div>
						<div class="twitter" title="<?php echo Loc::getmessage('SOCIAL_TWITTER_TITLE') ?>"></div>
						<div class="odnoklassniki" title="<?php echo Loc::getmessage('SOCIAL_OK_TITLE') ?>"></div>
					</div>
				</section>
			<?endif?>
		</div>
		<div class="detail-product-right col-xs-12 <?if($arParams['AJAX_QUICKVIEW']):?>col-sm-7<?elseif($arParams['COLUMN'] == 'one_column'):?>col-sm-6<?elseif($arParams['CAROUSEL_DOTS_VERTICAL'] == 'Y'):?>col-sm-6<?else:?>col-sm-7<?endif?>">
			<div class="optionblock row">
			<?if('Y' == $arParams['USE_VOTE_RATING'])
			{
				?><div class="rating-container clearfix col-xs-6">
					<div class="rating">
					<?$APPLICATION->IncludeComponent(
						"bitrix:iblock.vote",
						"stars",
						array(
							"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
							"IBLOCK_ID" => $arParams['IBLOCK_ID'],
							"ELEMENT_ID" => $arResult['ID'],
							"ELEMENT_CODE" => "",
							"MAX_VOTE" => "5",
							"VOTE_NAMES" => array("1", "2", "3", "4", "5"),
							"SET_STATUS_404" => "N",
							"DISPLAY_AS_RATING" => $arParams['VOTE_DISPLAY_AS_RATING'],
							"CACHE_TYPE" => $arParams['CACHE_TYPE'],
							"CACHE_TIME" => $arParams['CACHE_TIME']
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
					</div>
				</div><?
			}?>
			</div>
			<?
				$minPrice = (isset($arResult['RATIO_PRICE']) ? $arResult['RATIO_PRICE'] : $arResult['MIN_PRICE']);
				$boolDiscountShow = (0 < $minPrice['DISCOUNT_DIFF']);
?>
				<section class="prop-block">
					<div class="content_price" itemtype="http://schema.org/Offer" itemprop="offers" itemscope>
						<meta itemprop="priceCurrency" content="<?php echo $minPrice['CURRENCY'] ?>" />
						<meta itemprop="price" content="<?php echo $minPrice['DISCOUNT_VALUE'] ?>">
<?
						if($OFFERS)
						{
							if(0 < $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CATALOG_QUANTITY'])
							{
?>
								<link itemprop="availability" href="http://schema.org/InStock">
<?
							}
						}
						else
						{
							if(0 < $arResult['CATALOG_QUANTITY'])
							{
?>
								<link itemprop="availability" href="http://schema.org/InStock">
<?
							}
						}
?>
						<?if ('Y' == $arParams['SHOW_OLD_PRICE'])
						{?>
							<span style="font-size:38px;" id="<?php echo $arJSParams['VISUAL']['OLD_PRICE_ID']?>" class="old-price"<?php echo ($boolDiscountShow)? '' : ' style="display: none;"' ?>><? echo($boolDiscountShow ? $minPrice['PRINT_VALUE'] : ''); ?></span>
						<?}?>
							<span style="font-size:38px;" id="<?php echo $arJSParams['VISUAL']['PRICE_ID'] ?>" class="price<?php echo ($boolDiscountShow)? ' old' : ' ' ?>"><?php echo $minPrice['PRINT_DISCOUNT_VALUE'] ?></span>
					</div>

				<?if ('Y' == $arParams['BRAND_USE'])
				{
					?><?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", "element", array(
						"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
						"IBLOCK_ID" => $arParams['IBLOCK_ID'],
						"ELEMENT_ID" => $arResult['ID'],
						"ELEMENT_CODE" => "",
						"PROP_CODE" => $arParams['BRAND_PROP_CODE'],
						"CACHE_TYPE" => $arParams['CACHE_TYPE'],
						"CACHE_TIME" => $arParams['CACHE_TIME'],
						"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
						"WIDTH" => "100",
						"HEIGHT" => "60",
						"WIDTH_SMALL" => "100",
            "HEIGHT_SMALL" => "60"
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?><?
				}?>
			</section>
			<section class="prop-container">
<?
				if(!$OFFERS && isset($arResult['PROPERTIES']['ARTNUMBER']) && !empty($arResult['PROPERTIES']['ARTNUMBER']['VALUE']))
				{
					?><dl class="prop clearfix">
						<dt><?php echo $arResult['PROPERTIES']['ARTNUMBER']['NAME'] ?></dt>
						<dd><?php echo $arResult['PROPERTIES']['ARTNUMBER']['VALUE'] ?></dd>
					</dl><?
				}
				else if($arResult['SHOW_OFFERS_PROPS'])
				{
					?><dl class="prop clearfix" id="<?php echo $arJSParams['VISUAL']['DISPLAY_PROP_DIV'] ?>" style="display: none;"></dl><?
				}
?>
			</section>
			<div class="available dropdown">
<?
				$in_stock = 'NOT';
        if(0 < $arResult['CATALOG_QUANTITY'])
        {
            $in_stock = 'IN';
        }
?>
				<a href="#" <?if($arParams['AJAX_QUICKVIEW'] && $OFFERS):?>style="border-bottom: 0;cursor: default;"<?endif?> class="dropdown-toggle" data-toggle="dropdown" role="button">НАЛИЧИЕ НА СКЛАДАХ:<?//php echo Loc::getmessage('CATALOG_STOCK') ?></a>
				<span class="<?=(0 < $arResult['CATALOG_QUANTITY'])? 'text-success' : 'text-danger'?>" data-in="<?php echo Loc::getmessage('CATALOG_IN_STOCK') ?>" data-not="<?php echo Loc::getmessage('CATALOG_NOT_STOCK') ?>" id="<?php echo $arJSParams['VISUAL']['AVAILABLE_ID']?>"><?php echo Loc::getmessage('CATALOG_'.$in_stock.'_STOCK') ?></span>
					<?if($arParams["USE_STORE"] == 'Y' && ModuleManager::isModuleInstalled("catalog"))
					{
						if($arParams['AJAX_QUICKVIEW'] && !$OFFERS || !$arParams['AJAX_QUICKVIEW'])
						{
							?><?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "catalog", array(
									"ELEMENT_ID" => $arResult['ID'],
									"STORE_PATH" => $arParams['STORE_PATH'],
									"CACHE_TYPE" => "A",
									"CACHE_TIME" => "36000",
									"MAIN_TITLE" => $arParams['MAIN_TITLE'],
									"USE_MIN_AMOUNT" =>  $arParams['USE_MIN_AMOUNT'],
									"MIN_AMOUNT" => $arParams['MIN_AMOUNT'],
									"STORES" => $arParams['STORES'],
									"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
									"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
									"USER_FIELDS" => $arParams['USER_FIELDS'],
									"FIELDS" => $arParams['FIELDS'],
									"AJAX_QUICKVIEW" => $arParams['AJAX_QUICKVIEW']
								),
								$component,
								array("HIDE_ICONS" => "Y")
							);?><?
						}
					}
					unset($in_stock);
				?>
	    	<?
  		if(!empty($arResult['DISPLAY_PROPERTIES']))
  		{
?>
				<div role="tabpanel" class="tab-pane" id="detail-tab-2" style="color:red">
  				<dl >
<?
						foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp)
						{
?>
						<dt><? echo $arOneProp['NAME']; ?>:</dt><dd><?
							echo (
								is_array($arOneProp['DISPLAY_VALUE'])
								? implode(' / ', $arOneProp['DISPLAY_VALUE'])
								: $arOneProp['DISPLAY_VALUE']
							); ?></dd><?
						}
						unset($arOneProp);
?>
					</dl>
				</div>
<?
  		}
?>

			</div>

<br>
<a target="_blank" href="/help/credit/">ДОСТУПНА РАССРОЧКА</a>
<br>
<br>
<p>Склад: Минск</p>
<p>телефоны:</p>
<p>+375(29)695-55-15</p>
<p>+375(29)551-50-50</p>
<p>Время работы:</p>
<p>Пн.-Пт. с 9.00 до 20.00, Сб.-Вс. с 11.00 до 18.00</p>









			<hr class="fab-rule">
			<?=$arSkuTemplate?>
<?
/////////////////////////////////////////////// TABLE SIZE ///////////////////////////////////////////////////
				if(isset($arResult['PROPERTIES']['TABLE_SIZE']) && $arResult['PROPERTIES']['TABLE_SIZE']['USER_TYPE'] == 'directory' && !empty($arResult['PROPERTIES']['TABLE_SIZE']['VALUE']))
				{
?>
					<div class="table-size"><a href="#" data-url="<?php echo $arResult['DETAIL_PAGE_URL']?>"><?php echo $arResult['PROPERTIES']['TABLE_SIZE']['NAME'] ?></a></div>
	<?
					$ajax_tablesize = false;
					if($arParams['AJAX'] && isset($_REQUEST['ajax_tablesize']) && !empty($_REQUEST['ajax_tablesize']))
					{
						$ajax_tablesize = true;
						$APPLICATION->RestartBuffer();
					}

					if($ajax_tablesize)
					{
						?><?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", "tablesize", array(
							"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
							"IBLOCK_ID" => $arParams['IBLOCK_ID'],
							"ELEMENT_ID" => $arResult['ID'],
							"ELEMENT_CODE" => "",
							"PROP_CODE" => array('TABLE_SIZE'),
							"CACHE_TYPE" => $arParams['CACHE_TYPE'],
							"CACHE_TIME" => $arParams['CACHE_TIME'],
							"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
							"WIDTH" => "",
							"HEIGHT" => ""
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?><?
					}
					if($ajax_tablesize)
						die;
				}

			if('Y' == $arParams['USE_PRODUCT_QUANTITY'])
       {
?>
				<div class="form-inline">
          <div class="quantity form-group">
          	<span class="quantity-name"><?=Loc::getmessage('CATALOG_QUANTITY2')?></span>
	          <div class="input-group">
	          	<div class="input-group-btn">
              	<button type="button" class="btn btn-default quantity-down">-</button>
              </div>
              <input
              type="text"
              class="form-control qty min text-center"
              id="<?=$arJSParams['VISUAL']['QUANTITY_MEASURE']?>"
              maxlength="10"
              name="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>"
              value="<?=$arResult['CATALOG_MEASURE_RATIO']; ?>"
              data-check="<?=($arResult['CHECK_QUANTITY'])? true: false; ?>"
              data-max="<?=($arResult['CHECK_QUANTITY'])? $arResult["MAX_QUANTITY"]: false; ?>"
              step="<?=$arResult['CATALOG_MEASURE_RATIO'];?>" />
              <div class="input-group-btn">
              	<button type="button" class="btn btn-default quantity-up">+</button>
              </div>
	          </div>
	          <span id="<?=$arJSParams['VISUAL']['NAME_MEASURE']?>" class="measure_name"><?=$arResult['CATALOG_MEASURE_NAME']; ?></span>
          </div>
         </div>
<?
      }
      $addCart = (in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']))? $arParams['MESS_BTN_BUY']: $arParams['MESS_BTN_ADD_TO_BASKET'];
      $notAvailableMessage = ($arParams['MESS_NOT_AVAILABLE'] != '' ? $arParams['MESS_NOT_AVAILABLE'] : Loc::getmessageJS('CT_BCE_CATALOG_NOT_AVAILABLE'));

			$arSettingsBasket = [
				'showClosePopup' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y' && !$arParams['AJAX_QUICKVIEW']),
				'loading' => Loc::getmessage('MESS_BTN_LOADING'),
				'basketUrl' => $arParams['BASKET_URL'],
				'detailPageUrl' => $arResult['DETAIL_PAGE_URL'],
				'inBasket' => $arParams['MESS_BTN_ADDED_TO_BASKET'],
				'outBasket' => $addCart,
				'basketAction' => (in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']))? 'BUY' : 'ADD'
			];
			?><div class="button-cart"><?
				if(!$OFFERS)
				{
					if($arResult['CAN_BUY']) {?>
							<a
							data-options="<?php echo CUtil::PhpToJSObject($arSettingsBasket, false, true); ?>"
							data-elementid="<?php echo $arResult['ID'] ?>"
							class="btn btn-primary un_buttoncart add2basket add2basket_<?php echo $arResult['ID'] ?>" 
							role="button" title="<?php echo $addCart ?>" href="#">
								<span class="text"><?php echo $addCart ?></span>
							</a>
					<?} else {?>
						<span class="notavailable"><?php echo $notAvailableMessage ?></span>

						<?if($showSubscribeBtn)
							{
								$APPLICATION->includeComponent('bitrix:catalog.product.subscribe','',
									array(
										'PRODUCT_ID' => $arResult['ID'],
										'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
										'BUTTON_CLASS' => 'btn btn-primary',
										'DEFAULT_DISPLAY' => !$arResult['CAN_BUY'],
									),
									$component, array('HIDE_ICONS' => 'Y')
								);
							}?>

					<?}
				} else if($OFFERS)
				{
	?>
						<a
						<?=($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'])? '' : 'style="display: none;"'?>
						data-options="<?php echo CUtil::PhpToJSObject($arSettingsBasket, false, true); ?>"
						data-elementid="<?php echo $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?>"
						data-parentelementid="<?php echo $arResult['ID'] ?>"
						id="<?php echo $arJSParams['VISUAL']['BUY_ID']?>" 
						class="btn btn-primary offers add2basket add2basket_<?php echo $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?> un_buttoncart" 
						role="button" title="<?php echo $addCart ?>" href="#">
							<span class="text"><?php echo $addCart ?></span>
						</a>
						<span class="notavailable"
						<?=($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'])? 'style="display: none;"' : ''?>
						id="<?php echo $arJSParams['VISUAL']['NOT_AVAILABLE_MESS']?>"><?php echo $notAvailableMessage ?></span>

						<?if($showSubscribeBtn)
						{
							$APPLICATION->includeComponent('bitrix:catalog.product.subscribe','',
								array(
									'PRODUCT_ID' => $arResult['ID'],
									'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
									'BUTTON_CLASS' => 'btn btn-primary',
									'DEFAULT_DISPLAY' => !$arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'],
								),
								$component, array('HIDE_ICONS' => 'Y')
							);
						}?>
	<?
				}
				if('Y' == $arParams['USE_FAVORITES'])
				{?>
					<div class="wishlist">
						<a data-toggle="tooltip" data-placement="top" title="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" data-in-favorites="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" data-in-delfavorites="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" class="btn add2liked add2liked_<?php echo $arResult['ID']?>" data-liked-id="<?php echo $arResult['ID']?>" href="javascript:void(0);">
						<span><?php echo Loc::getMessage('MSG_IN_FAVORITES')?></span>
						</a>
					</div>
				<?}
				if($arParams['DISPLAY_COMPARE'])
				{?>
					<div class="compare">
						<a data-toggle="tooltip" data-placement="top" href="<?php echo $arResult['COMPARE_URL']?>" class="btn add2compare add2compare_<?php echo $arResult['ID']?>" data-compareurl="<?php echo $arParams['COMPARE_PATH']?>" id="<?php echo $arJSParams['VISUAL']['COMPARE_ID']?>" data-compare="<?php echo $arResult['ID']?>" title="<?php echo $arParams['MESS_BTN_COMPARE']?>">
							<span><?php echo $arParams['MESS_BTN_COMPARE']?></span>
						</a>
					</div>
				<?}
			?></div><?
?>
			<?if($arParams['USE_ONECLICK'] == 'Y' && !$arParams['AJAX_QUICKVIEW'])
			{
				$APPLICATION->IncludeComponent(
					"unisoftmedia:recall", 
					"oneclick", 
					array(
						"USE_CAPTCHA" => $arParams['ONECLICK_USE_CAPTCHA'],
						"USE_ONECLICK" => $arParams['USE_ONECLICK'],
						"USE_MASK" => $arParams['ONECLICK_USE_MASK'],
						"MASK_PHONE" => $arParams['ONECLICK_MASK_PHONE'],
						"PRODUCT_NAME" => (
							isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
							? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
							: $arResult["NAME"]
						),
						"ELEMENT_ID" => $arResult['ID'],
						"OK_TEXT" => $arParams['ONECLICK_OK_TEXT'],
						"EMAIL_TO" => $arParams['ONECLICK_EMAIL_TO'],
						"REQUIRED_FIELDS" => $arParams['ONECLICK_REQUIRED_FIELDS'],
						"EVENT_MESSAGE_ID" => $arParams['ONECLICK_EVENT_MESSAGE_ID'],
						"COMPONENT_TEMPLATE" => "oneclick",
						"MESS_TITLE" => $arParams['ONECLICK_MESS_TITLE'],
						"POPUP_FORM" => "Y",
						"INCLUDE_FIELDS" => $arParams['ONECLICK_INCLUDE_FIELDS'],
						"USER_CONSENT" => $arParams['ONECLICK_USER_CONSENT'],
						"USER_CONSENT_ID" => $arParams['ONECLICK_USER_CONSENT_ID'],
						"USER_CONSENT_IS_CHECKED" => $arParams['ONECLICK_USER_CONSENT_IS_CHECKED'],
						"AUTO_SAVE" => $arParams['ONECLICK_USER_AUTO_SAVE'],
						"USER_CONSENT_IS_LOADED" => $arParams['ONECLICK_USER_IS_LOADED']
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
			}?>
		</div>
	</div>
<?
unset($minPrice);
if($arParams['AJAX_QUICKVIEW']){
	?></div><?
	if($OFFERS)
	{
		?><script type="text/javascript">
				var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
		</script><?
	}
	die;
}
	if($OFFERS)
	{
		if ($arResult['OFFER_GROUP'])
		{
			foreach ($arResult['OFFER_GROUP_VALUES'] as $offerID)
			{
?>
				<article class="constructor" id="<? echo $arJSParams['VISUAL']['OFFER_GROUP'].$offerID; ?>" style="display: none;">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
						"catalog",
						array(
							"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
							"ELEMENT_ID" => $offerID,
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</article>
<?
			}
		}
	}
	else
	{
		if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP'])
		{
?>
			<article class="constructor">
				?><?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
						"catalog",
						array(
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"ELEMENT_ID" => $arResult["ID"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
			<article>
<?
		}
	}

	if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
	{
		$APPLICATION->IncludeComponent("bitrix:sale.gift.product", "product", array(
				'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
				'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
				'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
				'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
				'SUBSCRIBE_URL_TEMPLATE' => $arResult['~SUBSCRIBE_URL_TEMPLATE'],
				'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],

				"USE_CAROUSEL" => 'Y',
				"RESPONSIVE_CAROUSEL_ITEMS_LG" => ($arParams['COLUMN'] == 'one_column')? "5" : "4",
				"RESPONSIVE_CAROUSEL_ITEMS_MD" => ($arParams['COLUMN'] == 'one_column')? "4" : "3",
				"RESPONSIVE_CAROUSEL_ITEMS_SM" => "3",
				"RESPONSIVE_CAROUSEL_ITEMS_XS" => "2",
				"RESPONSIVE_CAROUSEL_ITEMS_MOBILE" => "1",
				"RESPONSIVE_ITEMS_LG" => "4",
				"RESPONSIVE_ITEMS_MD" => "3",
				"RESPONSIVE_ITEMS_SM" => "3",
				"RESPONSIVE_ITEMS_XS" => "2",

				"SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
				"SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
				"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
				"LINE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
				"HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
				"BLOCK_TITLE" => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
				"TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
				"SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
				"SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
				"MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

				"SHOW_PRODUCTS_{$arParams['IBLOCK_ID']}" => "Y",
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
				"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
				"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
				"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
				"USE_PRODUCT_QUANTITY" => 'N',
				"OFFER_TREE_PROPS_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFER_TREE_PROPS'],
				"CART_PROPERTIES_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFERS_CART_PROPERTIES'],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"POTENTIAL_PRODUCT_TO_BUY" => array(
					'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
					'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
					'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
					'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
					'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

					'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
					'SECTION' => array(
						'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
						'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
						'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
						'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
					),
				)
			), $component, array("HIDE_ICONS" => "Y"));
	}
	if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
	{
		$APPLICATION->IncludeComponent(
				"bitrix:sale.gift.main.products",
				"product",
				array(
					"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
					"BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

					"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
					"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],

					"AJAX_MODE" => $arParams["AJAX_MODE"],
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],

					"ELEMENT_SORT_FIELD" => 'ID',
					"ELEMENT_SORT_ORDER" => 'DESC',
					//"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
					//"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
					"FILTER_NAME" => 'searchFilter',
					"SECTION_URL" => $arParams["SECTION_URL"],
					"DETAIL_URL" => $arParams["DETAIL_URL"],
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],

					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],

					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SET_TITLE" => $arParams["SET_TITLE"],
					"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

					"USE_CAROUSEL" => 'Y',
					"RESPONSIVE_CAROUSEL_ITEMS_LG" => "4",
					"RESPONSIVE_CAROUSEL_ITEMS_MD" => "3",
					"RESPONSIVE_CAROUSEL_ITEMS_SM" => "3",
					"RESPONSIVE_CAROUSEL_ITEMS_XS" => "2",
					"RESPONSIVE_CAROUSEL_ITEMS_MOBILE" => "1",
					"RESPONSIVE_ITEMS_LG" => "4",
					"RESPONSIVE_ITEMS_MD" => "3",
					"RESPONSIVE_ITEMS_SM" => "3",
					"RESPONSIVE_ITEMS_XS" => "2",
					"SLIDER_NAV" => 'Y',
					"SLIDER_SPEED" => 1000,
					"SLIDER_AUTOPLAY" => 'N',
					"SLIDER_SHOW_SPEED" => 8000,
					"SLIDER_AUTOPLAY_HOVER_PAUSE" => 'N',
					"MOUSE_DRAG" => 'Y',
					"SLIDER_LOOP" => 'N',
					'MAX_WIDTH' => '300',
					'MAX_HEIGHT' => '400',

					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),

					"ADD_PICT_PROP" => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),

					"LABEL_PROP" => (isset($arParams["LABEL_PROP"]) ? $arParams["LABEL_PROP"] : ""),
					"OFFER_ADD_PICT_PROP" => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
					"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : ""),
					"SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
					"SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
					"MESS_BTN_BUY" => (isset($arParams["MESS_BTN_BUY"]) ? $arParams["MESS_BTN_BUY"] : ""),
					"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["MESS_BTN_ADD_TO_BASKET"]) ? $arParams["MESS_BTN_ADD_TO_BASKET"] : ""),
					"MESS_BTN_DETAIL" => (isset($arParams["MESS_BTN_DETAIL"]) ? $arParams["MESS_BTN_DETAIL"] : ""),
					"MESS_NOT_AVAILABLE" => (isset($arParams["MESS_NOT_AVAILABLE"]) ? $arParams["MESS_NOT_AVAILABLE"] : ""),
					'ADD_TO_BASKET_ACTION' => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
					'SHOW_CLOSE_POPUP' => (isset($arParams["SHOW_CLOSE_POPUP"]) ? $arParams["SHOW_CLOSE_POPUP"] : ""),
					'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
					'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
				)
				+ array(
					'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']) ? $arResult['ID'] : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
					'SECTION_ID' => $arResult['SECTION']['ID'],
					'ELEMENT_ID' => $arResult['ID'],
				),
				$component,
				array("HIDE_ICONS" => "Y")
		);
	}

	$video = (isset($arResult['PROPERTIES']['VIDEO']['VALUE']) && is_array($arResult['PROPERTIES']['VIDEO']['VALUE']) && !empty($arResult['PROPERTIES']['VIDEO']['VALUE']));
	$documentations = (isset($arResult['PROPERTIES']['DOCUMENTATIONS']['VALUE']) && is_array($arResult['PROPERTIES']['DOCUMENTATIONS']['VALUE']) && !empty($arResult['PROPERTIES']['DOCUMENTATIONS']['VALUE']) && 'F' == $arResult['PROPERTIES']['DOCUMENTATIONS']['PROPERTY_TYPE']);
?>
	<section class="tabs-container detail-tabs-container">
		<!-- Nav tabs -->
	  <ul class="nav nav-tabs hidden-xs" role="tablist">
	    <li role="presentation" class="active"><a href="#detail-tab-1" role="tab" data-toggle="tab"><?php echo Loc::getmessage('FULL_DESCRIPTION') ?></a></li>
	    <?if(!empty($arResult['DISPLAY_PROPERTIES'])):?>
	    	<li role="presentation"><a href="#detail-tab-2" role="tab" data-toggle="tab"><?php echo Loc::getmessage('CHARACTERISTICS') ?></a></li>
	    <?endif?>
	    <?if($video):?>
	    	<li role="presentation"><a href="#detail-tab-3" role="tab" data-toggle="tab"><?php echo $arResult['PROPERTIES']['VIDEO']['NAME'] ?></a></li>
	    <?endif?>
	    <?if($documentations):?>
	    	<li role="presentation"><a href="#detail-tab-4" role="tab" data-toggle="tab"><?php echo $arResult['PROPERTIES']['DOCUMENTATIONS']['NAME'] ?></a></li>
	    <?endif?>
	    <?if('Y' == $arParams['USE_COMMENTS']):?>
	    	<li role="presentation"><a href="#detail-tab-5" role="tab" data-toggle="tab"><?php echo Loc::getmessage('TAB_COMMENTS') ?></a></li>
	    <?endif?>
	    <?if('F' == $arParams['USE_REVIEW']):?>
	    	<li role="presentation"><a href="#detail-tab-6" role="tab" data-toggle="tab"><?php echo Loc::getmessage('TAB_REVIEW') ?></a></li>
	    <?endif?>
	  </ul>
	  <ul class="nav nav-tabs hidden-sm hidden-md hidden-lg" role="tablist">
	    <li role="presentation" class="dropdown active">
		  		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo Loc::getmessage('FULL_DESCRIPTION') ?> <span class="caret"></span></a> 
		  		<ul class="dropdown-menu">
			  		<li class="active">
			  			<a class="tab-current" href="#detail-tab-1" role="tab" data-toggle="tab" aria-controls="detail-tab-1"><?php echo Loc::getmessage('FULL_DESCRIPTION') ?></a>
			  		</li> 
			  		<?if(!empty($arResult['DISPLAY_PROPERTIES'])):?>
				    	<li role="presentation"><a class="tab-current" href="#detail-tab-2" aria-controls="detail-tab-2" role="tab" data-toggle="tab"><?php echo Loc::getmessage('CHARACTERISTICS') ?></a></li>
				    <?endif?>
				    <?if($video):?>
				    	<li role="presentation"><a class="tab-current" href="#detail-tab-3" aria-controls="detail-tab-3" role="tab" data-toggle="tab"><?php echo $arResult['PROPERTIES']['VIDEO']['NAME'] ?></a></li>
				    <?endif?>
				    <?if($documentations):?>
				    	<li role="presentation"><a class="tab-current" href="#detail-tab-4" aria-controls="detail-tab-4" role="tab" data-toggle="tab"><?php echo $arResult['PROPERTIES']['DOCUMENTATIONS']['NAME'] ?></a></li>
				    <?endif?>
				    <?if('Y' == $arParams['USE_COMMENTS']):?>
				    	<li role="presentation"><a class="tab-current" href="#detail-tab-5" aria-controls="detail-tab-5" role="tab" data-toggle="tab"><?php echo Loc::getmessage('TAB_COMMENTS') ?></a></li>
				    <?endif?>
				    <?if('F' == $arParams['USE_REVIEW']):?>
				    	<li role="presentation"><a class="tab-current" href="#detail-tab-6" aria-controls="detail-tab-6" role="tab" data-toggle="tab"><?php echo Loc::getmessage('TAB_REVIEW') ?></a></li>
				    <?endif?>
		  		</ul> 
		  	</li>
	  </ul>

	  <!-- Tab panes -->
	  <div class="tab-content">
	    <div role="tabpanel" class="tab-pane active" id="detail-tab-1">
				<div class="description" itemprop="description">
					<?php echo $arResult['~DETAIL_TEXT'] ?>
				</div>
	    </div>
	    	<?
  		if(!empty($arResult['DISPLAY_PROPERTIES']))
  		{
?>
				<div role="tabpanel" class="tab-pane" id="detail-tab-2">
  				<dl class="dl-horizontal">
<?
						foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp)
						{
?>
						<dt><? echo $arOneProp['NAME']; ?></dt><dd><?
							echo (
								is_array($arOneProp['DISPLAY_VALUE'])
								? implode(' / ', $arOneProp['DISPLAY_VALUE'])
								: $arOneProp['DISPLAY_VALUE']
							); ?></dd><?
						}
						unset($arOneProp);
?>
					</dl>
				</div>
<?
  		}
?>
			<?if($video):?>
		    <div role="tabpanel" class="tab-pane" id="detail-tab-3">
					<section class="youtube-video">
						<div class="row">
							<?foreach($arResult['PROPERTIES']['VIDEO']['VALUE'] as $arVideo):?>
								<article class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<iframe data-src="<?=$arVideo?>" frameborder="0" allowfullscreen></iframe>
								</article>
							<?endforeach?>
						</div>
					</section>
		    </div>
	    <?endif?>
	    <?if($documentations):?>
	    	 <div role="tabpanel" class="tab-pane" id="detail-tab-4">
					<section class="documentations">
						<div class="row">
							<?foreach($arResult['PROPERTIES']['DOCUMENTATIONS']['VALUE'] as $arDocumentations):?>
								<article class="col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php echo strtolower($arDocumentations['TYPE']) ?>">
									<a class="file" href="<?php echo $arDocumentations['LINK_PATH'] ?>" target="_blank"><span></span></a>
									<div class="desc">
										<a href="<?php echo $arDocumentations['LINK_PATH'] ?>" target="_blank"><span class="text"><?php echo $arDocumentations['ORIGINAL_NAME'] ?></span></a>
										<br />
										<span class="size"><?php echo Loc::getmessage('DOCUMENTATIONS_SIZE') ?> <?php echo $arDocumentations['SIZE'] ?></span>
									</div>
								</article>
							<?endforeach?>
						</div>
					</section>
		    </div>
	    <?endif?>
	    <?if('Y' == $arParams['USE_COMMENTS']):?>
	    	 <div role="tabpanel" class="tab-pane" id="detail-tab-5">
					<section class="comments">
						<?$APPLICATION->IncludeComponent(
							"bitrix:catalog.comments",
							"comments",
							array(
								"ELEMENT_ID" => $arResult['ID'],
								"ELEMENT_CODE" => "",
								"IBLOCK_ID" => $arParams['IBLOCK_ID'],
								"SHOW_DEACTIVATED" => $arParams['SHOW_DEACTIVATED'],
								"URL_TO_COMMENT" => "",
								"WIDTH" => "",
								"COMMENTS_COUNT" => "10",
								"BLOG_USE" => $arParams['BLOG_USE'],
								"FB_USE" => $arParams['FB_USE'],
								"FB_APP_ID" => $arParams['FB_APP_ID'],
								"VK_USE" => $arParams['VK_USE'],
								"VK_API_ID" => $arParams['VK_API_ID'],
								"CACHE_TYPE" => $arParams['CACHE_TYPE'],
								"CACHE_TIME" => $arParams['CACHE_TIME'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								"BLOG_TITLE" => "",
								"BLOG_URL" => $arParams['BLOG_URL'],
								"PATH_TO_SMILE" => "",
								"EMAIL_NOTIFY" => $arParams['BLOG_EMAIL_NOTIFY'],
								"AJAX_POST" => "Y",
								"SHOW_SPAM" => "Y",
								"SHOW_RATING" => "N",
								"FB_TITLE" => "",
								"FB_USER_ADMIN_ID" => "",
								"FB_COLORSCHEME" => "light",
								"FB_ORDER_BY" => "reverse_time",
								"VK_TITLE" => ""
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?>
					</section>
		    </div>
	    <?endif?>
	    <?if('F' == $arParams['USE_REVIEW']):?>
	    	 <div role="tabpanel" class="tab-pane" id="detail-tab-6">
					<section class="review">
						<?$APPLICATION->IncludeComponent(
          		'bitrix:forum.topic.reviews',
          		'catalog',
          		Array(
          			"URL_TEMPLATES_DETAIL" => $arParams["REVIEWS_URL_TEMPLATES_DETAIL"],			
          			"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
          			"FORUM_ID" => $arParams["FORUM_ID"],
          			"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
          			"IBLOCK_ID" => $arParams['IBLOCK_ID'],
          			"ELEMENT_ID" => $arResult['ID'],
          			"CACHE_TYPE" => "N",//$arParams["CACHE_TYPE"],
          			"CACHE_TIME" => $arParams["CACHE_TIME"],
          			"MESSAGES_PER_PAGE" => 1,
          			"AJAX_POST" => 'Y',
          		),
          		$component,
          		array('HIDE_ICONS' => 'Y')
          	);?>
					</section>
		    </div>
	    <?endif?>
	  </div>
	</section>
</div>
<?
if($OFFERS)
{
	?><script type="text/javascript">
			var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
	</script><?
}