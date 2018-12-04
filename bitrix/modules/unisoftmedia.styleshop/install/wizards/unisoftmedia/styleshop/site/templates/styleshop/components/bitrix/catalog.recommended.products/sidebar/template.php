<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && !empty($arResult['ITEMS']))
{
	$arSkuTemplate = array();
	if (!empty($arResult['SKU_PROPS'])) {

		$skuTooltip = ($arParams['USE_SKU_TOOLTIP'] == 'Y');

		foreach($arResult['SKU_PROPS'] as $skuProps)
		{
			foreach($skuProps as $arProp)
			{
				$propId = $arProp['ID'];

	      if(is_array($arProp['VALUES']) && !empty($arProp['VALUES'])){
	        foreach($arProp['VALUES'] as $key => $arOneValue)
	        {
	          if('PICT' == $arProp['SHOW_MODE']) {

		          $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.htmlspecialcharsbx($arOneValue["NAME"]).'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="color cnt" title="'.htmlspecialcharsbx($arOneValue["NAME"]).'"><span class="cnt_item" style="background-image:url('.$arOneValue["PICT"]["SRC"].');"></span></span></li>';
		
		        } else if('TEXT' == $arProp['SHOW_MODE']) {

		        	$descTooltip = '';
		        	if(isset($arOneValue['DESCRIPTION']) && $skuTooltip)
		        	{
		        		$descTooltip .= 'data-container="body" ';
	              $descTooltip .= 'data-toggle="popover" ';
	              $descTooltip .= 'data-placement="top" ';
	              $descTooltip .= 'data-html="true" ';
	              $descTooltip .= 'data-content="'.$arOneValue["DESCRIPTION"].'"';
		        	}
		
		         $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" '.$descTooltip.'  data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.$arOneValue["NAME"].'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="size cnt" title="'.htmlspecialcharsbx($arOneValue['NAME']).'">'.htmlspecialcharsbx($arOneValue['NAME']).'</span></li>';
		
		        }
	      	}
	      }
	    }
	  }
	}
?>
    <div class="catalog-sidebar block hidden-xs hidden-sm"><?
		if($arParams['MESS_TITLE']) {
			?><h2 class="product-title"><?php echo $arParams['MESS_TITLE'] ?></h2><?
		}

		$sliderOptions = [];
		if($arParams['SLIDER_NAV'] == 'Y')
			$sliderOptions['nav'] =  true;
		$sliderOptions['smartSpeed'] = $arParams['SLIDER_SPEED'];
		if($arParams['SLIDER_AUTOPLAY'] == 'Y')
			$sliderOptions['autoplay'] = true;
		$sliderOptions['autoplayTimeout'] = $arParams['SLIDER_SHOW_SPEED'];
		if($arParams['SLIDER_AUTOPLAY_HOVER_PAUSE'] == 'Y')
			$sliderOptions['autoplayHoverPause'] = true;
		if($arParams['MOUSE_DRAG'] == 'N')
			$sliderOptions['mouseDrag'] = false;
		if($arParams['SLIDER_LOOP'] == 'Y')
					$sliderOptions['loop'] = true;
		$sliderOptions['dots'] = false;
		$sliderOptions['items'] = 1;

        ?><div class="catalog-sidebar-container"><?
		$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
		$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
		$arElementDeleteParams = array("CONFIRM" => Loc::getMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

		$previousLevel = 0;
		$carousel = intval($arParams['CAROUSEL_ITEMS']);
		?><ul class="unproduct owl-carousel Owlcarousel" data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>"><?
            foreach($arResult['ITEMS'] as $arItem)
            {
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
				$strMainID = $this->GetEditAreaId($arItem['ID']);

				$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

				$OFFERS = (isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))? true : false;

				$arrMainID = array(
					'ID' => $strMainID,
					'NAME' => $strMainID.'_name',
					'PICT' => $strMainID.'_pict',
					'SECOND_PICT' => $strMainID.'_secondpict',
					'MAIN_PROPS' => $strMainID.'_main_props',
			
					'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
					'AVAILABLE' => $strMainID.'_available',
					'NAME_MEASURE' => $strMainID.'_name_measure',
					'BUY_LINK' => $strMainID.'_buy_link',
					'COMPARE' => $strMainID.'_compare',
					'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
			
					'PRICE' => $strMainID.'_price',
					'OLD_PRICE' => $strMainID.'_old_price',
					'DSC_PERC' => $strMainID.'_dsc_perc',
					'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
			
					'PROP_DIV' => $strMainID.'_sku_tree',
					'PROP' => $strMainID.'_prop_',
					'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
					'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail'
			   );

			   $arJSParams = array(
					'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
					'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
					'SHOW_ADD_BASKET_BTN' => false,
					'SHOW_BUY_BTN' => true,
					'SHOW_ABSENT' => true,
					'SHOW_SKU_PROPS' => $arItem['OFFERS_PROPS_DISPLAY'],
					'SECOND_PICT' => $arItem['SECOND_PICT'],
					'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
					'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
					'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
					'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
					'DEFAULT_PICTURE' => array(
						'PICTURE' => $arItem['PRODUCT_PREVIEW'],
						'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
					),
					'PRODUCT' => [
						'ID' => $arItem['ID'],
						'NAME' => $arrMainID['NAME'],
					],
					'VISUAL' => array(
						'ID' => $arrMainID['ID'],
						'NAME' => $arrMainID['NAME'],
						'AVAILABLE_ID' => $arrMainID['AVAILABLE'],
						'PICT_ID' => $arrMainID['PICT'],
						'SECOND_PICT_ID' => $arrMainID['SECOND_PICT'],
						'QUANTITY_MEASURE' => $arrMainID['QUANTITY_MEASURE'],
						'NAME_MEASURE' => $arrMainID['NAME_MEASURE'],
						'PRICE_ID' => $arrMainID['PRICE'],
						'OLD_PRICE_ID' => $arrMainID['OLD_PRICE'],
						'TREE_ID' => $arrMainID['PROP_DIV'],
						'TREE_ITEM_ID' => $arrMainID['PROP'],
						'BUY_ID' => $arrMainID['BUY_LINK'],
						'COMPARE_ID' => $arrMainID['COMPARE'],
						'ADD_BASKET_ID' => $arrMainID['ADD_BASKET_ID'],
						'DSC_PERC' => $arrMainID['DSC_PERC'],
						'SECOND_DSC_PERC' => $arrMainID['SECOND_DSC_PERC'],
						'DISPLAY_PROP_DIV' => $arrMainID['DISPLAY_PROP_DIV'],
						'IN_STOCK' => $arParams['MESS_IN_AVAILABLE'],
				    'NOT_STOCK' => $arParams['MESS_NOT_AVAILABLE'],
				    'NOT_AVAILABLE_MESS' => $arrMainID['NOT_AVAILABLE_MESS']
					),
					'OFFERS' => $arItem['JS_OFFERS'],
					'OFFER_SELECTED' => $arItem['OFFERS_SELECTED'],
					'QUICKBUY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
					'PRODUCTDAY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
					'QUICKBUY_PROP' => $arItem['PROPERTIES']['QUICKBUY']['VALUE'],
					'PRODUCTDAY_PROP' => $arItem['PROPERTIES']['PRODUCTDAY']['VALUE'],                    
				);

				if($previousLevel == 0) {
					?><li><?
				}
				$minPrice = false;
				if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
					$minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
?>
        <article class="catalog-sidebar-item js-item" id="<?php echo $strMainID ?>">
        	<div class="row">
					<?if($arParams['SHOW_IMAGE'] == 'Y')
					{?>
						<div class="col-md-4 col-lg-5">
						<?/********************************************** IMAGES CONTAINER ************************************************/?>
							<figure class="unproduct-image-container">
								<?/********************** IMAGE ************************/?>
								<a class="unproduct-image-link" href="<?php echo $arItem['DETAIL_PAGE_URL']?>">
									<?if(is_array($arItem['PREVIEW_PICTURE']))
									{?>
										<img class="picture__first" id="<?php echo $arJSParams['VISUAL']['PICT_ID']?>" src="<?php echo $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $arItem['NAME']?>" />
									<?}
									if('Y' == $arParams['HOVER_IMAGE'])
									{?>
										<img class="picture__second" id="<?php echo $arJSParams['VISUAL']['SECOND_PICT_ID']?>" src="<?php echo (!empty($arItem['PREVIEW_PICTURE_SECOND']))? $arItem['PREVIEW_PICTURE_SECOND']['SRC'] : $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $arItem['NAME']?>" />
									<?}?>
								</a>
								<?/********************** IMAGE end ************************/
								/************ QUICK LOOK **********/
								if($arParams['USE_QUICKVIEW'] == 'Y' && 'Y' != $arParams['PRODUCT_DISPLAY_MODE'])
								{?>
									<a title="<?php echo Loc::getMessage('MSG_QUICK_VIEW')?>" href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>" class="quick-view"></a>
								<?}
								/************ QUICK LOOK end **********/?>
							</figure>
							<?/********************************************** IMAGES CONTAINER end ************************************************/?>
						</div><?
					}
					?><div class="<?php  echo ($arParams['SHOW_IMAGE'] == 'Y')? 'col-md-8 col-lg-7' : 'col-xs-12 col-sm-12 col-md-12 col-lg-12' ?>"><?
						?><div class="catalog-sidebar-item-product-content"><?
							if ($arParams['SHOW_NAME'] == 'Y')
							{?>
								<div class="name_unproduct">
									<a href="<?php echo $arItem['DETAIL_PAGE_URL']?>" title="<?php echo $arItem['NAME']?>"><?php echo $arItem['NAME']?></a>
								</div>
							<?}?>
							<div class="block_list">
								<div class="content_price"><?
									if ('Y' == $arParams['SHOW_OLD_PRICE'])
									{
										?> <span id="<?php echo $arJSParams['VISUAL']['OLD_PRICE_ID']?>" class="old-price"<?php echo ($minPrice['DISCOUNT_VALUE'] < $minPrice['VALUE'])? '' : ' style="display: none;"' ?>><?php echo $minPrice['PRINT_VALUE']; ?></span><?
									}
										?><span id="<?php echo $arJSParams['VISUAL']['PRICE_ID'] ?>" class="price"><?php echo $minPrice['PRINT_DISCOUNT_VALUE'] ?></span><?
								?></div>
<?
								unset($minPrice);
								/********************************* SKU *************************************/
								$arSkuProps = array();
								if (!empty($arResult['SKU_PROPS'])) {
									?>
							      <div class="block-sku" id="<?php echo $arJSParams['VISUAL']['TREE_ID'] ?>"><?
											foreach($arResult['SKU_PROPS'] as $skuProps)
											{
												foreach($skuProps as $arProp)
												{
													if (!isset($arItem['SKU_TREE_VALUES'][$arProp['ID']]))
														continue;

													$arSkuProps[] = array(
														'ID' => $arProp['ID'],
														'SHOW_MODE' => $arProp['SHOW_MODE'],
														'TYPE' => $arProp['PROPERTY_TYPE'],
														'VALUES_COUNT' => $arProp['VALUES_COUNT']
													);
							            if(is_array($arProp['VALUES']) && !empty($arProp['VALUES'])){?>
						                <div class="block-sku-detail-sku" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_cont">
						                    <span class="block-sku-detail-sku-name" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_selected"><?php echo htmlspecialcharsex($arProp['NAME']); ?>:
						                        <span class="selection"></span>
						                    </span>
						                    <div class="block-sku-detail-sku-container">
						                        <ul class="block-sku-detail-sku-container-list" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_list">
						                        	<?foreach($arSkuTemplate[$arProp['ID']] as $value => $valueItem)
						                        	{
						                        		if (!isset($arItem['SKU_TREE_VALUES'][$arProp['ID']][$value]))
																					continue;
																				echo $valueItem;
						                        	}
						                        		unset($value, $valueItem);
						                        	?>
						                        </ul>
						                    </div>
						                </div>
						            <?}
							          } 
							        }?>
							      </div>
							    <?
								}
								$arJSParams['TREE_PROPS'] = $arSkuProps;
								/********************************* SKU end *************************************/?>
								<div class="button-container">
								<?/************************ ADD TO CART *************************/
									$addCart = ($arParams['ADD_TO_BASKET_ACTION'] == 'BUY')? $arParams['MESS_BTN_BUY']: $arParams['MESS_BTN_ADD_TO_BASKET'];
									$notAvailableMessage = ($arParams['MESS_NOT_AVAILABLE'] != '' ? $arParams['MESS_NOT_AVAILABLE'] : Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE'));
									$arSettingsBasket = [
										'showClosePopup' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
										'loading' => Loc::getMessage('MESS_BTN_LOADING'),
										'basketUrl' => $arParams['BASKET_URL'],
										'detailPageUrl' => $arItem['DETAIL_PAGE_URL'],
										'inBasket' => $arParams['MESS_BTN_ADDED_TO_BASKET'],
										'outBasket' => $addCart,
										'basketAction' => ($arParams['ADD_TO_BASKET_ACTION'] == 'BUY')? 'BUY' : 'ADD'
									];
									if(!$OFFERS)
									{
										if($arItem['CAN_BUY']) {?>
											<a
											class="btn un_buttoncart add2basket add2basket_<?php echo $arItem['ID'] ?>"
											data-options="<?php echo CUtil::PhpToJSObject($arSettingsBasket, false, true); ?>"
											data-elementid="<?php echo $arItem['ID'] ?>"
											role="button" title="<?php echo $addCart ?>" href="<?php echo $arItem['DETAIL_PAGE_URL']?>">
												<span class="text"><?php echo $addCart ?></span>
											</a>
										<?} else {?>
											<span class="notavailable"><?php echo $notAvailableMessage ?></span>
										<?}
									} else if($OFFERS)
									{
?>
										<a
										id="<?php echo $arJSParams['VISUAL']['BUY_ID']?>"
										<?=($arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'])? '' : 'style="display: none;"' ?>
										class="btn offers add2basket add2basket_<?php echo $arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['ID'] ?> un_buttoncart"
										data-options="<?php echo CUtil::PhpToJSObject($arSettingsBasket, false, true); ?>"
										data-parentelementid="<?php echo $arItem['ID'] ?>"
										data-elementid="<?php echo $arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['ID'] ?>"
										role="button" title="<?php echo $addCart ?>" href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>">
											<span class="text"><?php echo $addCart ?></span>
										</a>

										<span class="notavailable" <?=($arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'])? 'style="display: none;"' : '' ?> id="<?php echo $arJSParams['VISUAL']['NOT_AVAILABLE_MESS']?>"><?php echo $notAvailableMessage ?></span><?
									}
									/************************ ADD TO CART end *************************/?>
								</div>
							</div>
						</div>
					</div>
					<?if(!empty($arItem['OFFERS_PROP']) && $OFFERS) {
						?><script type="text/javascript">
								var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
						</script>
					<?}?> 
					</div>
        </article>
         <?
							++$previousLevel;
							if($previousLevel >= $carousel){
								echo '</li>';
								$previousLevel = 0;
							}
            }
?>
        </ul>
		</div>
    </div>
<?}?>