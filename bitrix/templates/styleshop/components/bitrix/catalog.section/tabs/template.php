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
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (!empty($arResult['ITEMS']))
{
	$arSkuTemplate = array();
	if (!empty($arResult['SKU_PROPS'])) {

		$skuTooltip = ($arParams['USE_SKU_TOOLTIP'] == 'Y');

		foreach($arResult['SKU_PROPS'] as $arProp)
		{
			$propId = $arProp['ID'];

      if(is_array($arProp['VALUES']) && !empty($arProp['VALUES'])) {
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
	
	         $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" '.$descTooltip.' data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.htmlspecialcharsbx($arOneValue["NAME"]).'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="size cnt" title="'.htmlspecialcharsbx($arOneValue['NAME']).'">'.htmlspecialcharsbx($arOneValue['NAME']).'</span></li>';
	
	        }
      	}
      }
    }
	}

	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => Loc::getMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
	<div class="catalog-products-container">
		<?if($arParams['MESS_TITLE']) {
			?>
			<h2 class="product-title"><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></h2>
			<?
		}

		if($arParams['USE_CAROUSEL'] == 'Y')
		{
			$sliderOptions = [];
			$sliderOptions['nav'] = ($arParams['SLIDER_NAV'] == 'Y');
			$sliderOptions['smartSpeed'] = $arParams['SLIDER_SPEED'];
			$sliderOptions['autoplay'] = ($arParams['SLIDER_AUTOPLAY'] == 'Y');
			$sliderOptions['autoplayTimeout'] = $arParams['SLIDER_SHOW_SPEED'];
			$sliderOptions['autoplayHoverPause'] = ($arParams['SLIDER_AUTOPLAY_HOVER_PAUSE'] == 'Y');
			if($arParams['MOUSE_DRAG'] == 'N')
				$sliderOptions['mouseDrag'] = false;
			$sliderOptions['loop'] = ($arParams['SLIDER_LOOP'] == 'Y');
			$sliderOptions['dots'] = false;

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
		}
?>
		<div class="row">
			<div class="unproduct <?if($arParams['USE_CAROUSEL'] == 'Y'):?>owl-carousel Owlcarousel<?endif?>" <?if($arParams['USE_CAROUSEL'] == 'Y'):?>data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>"<?endif?>>
				<?foreach ($arResult['ITEMS'] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
					$strMainID = $this->GetEditAreaId($arItem['ID']);

					$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

					$OFFERS = (isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))? true : false;

					if($arItem['CATALOG_SUBSCRIBE'] == 'Y')
						$showSubscribeBtn = true;
					else
						$showSubscribeBtn = false;

					$arrMainID = [
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
				   ];

				   $arJSParams = [
						'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
						'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
						'SHOW_ADD_BASKET_BTN' => false,
						'SHOW_BUY_BTN' => true,
						'SHOW_ABSENT' => true,
						'USE_SUBSCRIBE' => $showSubscribeBtn,
						'SHOW_SKU_PROPS' => $arItem['OFFERS_PROPS_DISPLAY'],
						'SECOND_PICT' => $arItem['SECOND_PICT'],
						'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
						'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
						'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
						'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
						'DEFAULT_PICTURE' => [
							'PICTURE' => $arItem['PRODUCT_PREVIEW'],
							'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
						],
						'PRODUCT' => [
							'ID' => $arItem['ID'],
							'NAME' => $arrMainID['NAME'],
						],
						'VISUAL' => [
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
					    'SUBSCRIBE_ID' => $arrMainID['SUBSCRIBE_LINK'],
					    'NOT_AVAILABLE_MESS' => $arrMainID['NOT_AVAILABLE_MESS']
						],
						'OFFERS' => $arItem['JS_OFFERS'],
						'OFFER_SELECTED' => $arItem['OFFERS_SELECTED'],
						'QUICKBUY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
						'PRODUCTDAY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
						'QUICKBUY_PROP' => $arItem['PROPERTIES']['QUICKBUY']['VALUE'],
						'PRODUCTDAY_PROP' => $arItem['PROPERTIES']['PRODUCTDAY']['VALUE'],                    
					];

					$stickerNew   = '';
					$stickerHit   = '';
					if('Y' == $arItem['PROPERTIES']['NEWPRODUCT']['VALUE']) {
						$stickerNew = ' newproduct';
					}
					if('Y' == $arItem['PROPERTIES']['HIT']['VALUE']) {
						$stickerHit = ' hitproduct';
					}

					$minPrice = false;
					if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
						$minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
?>
					<article class="unproduct-item js-item<?if($arParams['USE_CAROUSEL'] != 'Y'):?> col-xs-<?php echo $arParams['RESPONSIVE_ITEMS_XS'] ?> col-sm-<?php echo $arParams['RESPONSIVE_ITEMS_SM'] ?> col-md-<?php echo $arParams['RESPONSIVE_ITEMS_MD'] ?> col-lg-<?php echo $arParams['RESPONSIVE_ITEMS_LG'] ?><?endif?>" id="<?php echo $strMainID ?>">
						<div class="unproduct-container">
							<?/********************************************** IMAGES CONTAINER ************************************************/?>
							<figure class="unproduct-image-container">
							<?/********************** sticker ************************/?>
								<div class="sticker">
									<span class="stickernew"<?php echo ('' == $stickerNew)? ' style="display: none;"' : '' ?>>
										<span class="text"><?php echo $arItem['PROPERTIES']['NEWPRODUCT']['NAME']?></span>
									</span>
									<span class="stickerhit"<?php echo ('' == $stickerHit)? ' style="display: none;"' : '' ?>>
										<span class="text"><?php echo $arItem['PROPERTIES']['HIT']['NAME']?></span>
									</span>
									<span id="<?php echo $arJSParams['VISUAL']['DSC_PERC']?>" data-name="<?php echo Loc::getMessage('STICKER_SALES') ?>" class="stickersales"<?php echo (0 < $minPrice['DISCOUNT_DIFF_PERCENT'])? '' : ' style="display: none;"' ?>>
										<span class="text">
										<?if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']) {
											?>-<?=$minPrice['DISCOUNT_DIFF_PERCENT'] ?>%<?
										} else {
											echo Loc::getMessage('STICKER_SALES') ?><?
										}?>
										</span>
									</span>
								</div>
								<?/********************** sticker end ************************/
								/********************** IMAGE ************************/?>
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
								/************ COMPARE, QUICK LOOK, FAVORITES **********/
								if($OFFERS && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'] && !empty($arItem['OFFERS_PROP']))
								{?>
									<div class="mask hidden-xs">
										<?/********************************* SKU *************************************/
											$arSkuProps = array();
											if (!empty($arResult['SKU_PROPS'])) {
												?>
										      <div class="block-sku" id="<?php echo $arJSParams['VISUAL']['TREE_ID'] ?>"><?
														foreach($arResult['SKU_PROPS'] as $arProp)
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
									          }?>
										      </div>
										    <?
											}
											$arJSParams['TREE_PROPS'] = $arSkuProps;
									/********************************* SKU end *************************************/
										if($arParams['USE_QUICKVIEW'] == 'Y')
										{?>
											<a href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>" class="quick-view"><span><?php echo Loc::getMessage('MSG_QUICK_VIEW')?></span></a>
										<?}?>
									</div>
								<?}
								if($arParams['USE_QUICKVIEW'] == 'Y' && empty($arItem['OFFERS_PROP']))
								{?>
									<a href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>" class="quick-view hidden-xs"><span><?php echo Loc::getMessage('MSG_QUICK_VIEW')?></span></a>
								<?}
								/************ COMPARE, QUICK LOOK, FAVORITES end **********/?>
							</figure>
							<?/********************************************** IMAGES CONTAINER end ************************************************/?>
							<div class="unproduct-bottom-container">
								<div class="name_unproduct">
									<a title="<?php echo $arItem['NAME']?>" href="<?php echo $arItem['DETAIL_PAGE_URL']?>"><?php echo $arItem['NAME']?></a>
								</div>
								<div class="block_list">
									<div class="content_price">
										<?if ('Y' == $arParams['SHOW_OLD_PRICE'])
										{?>
											<span id="<?php echo $arJSParams['VISUAL']['OLD_PRICE_ID']?>" class="old-price"<?php echo ($minPrice['DISCOUNT_VALUE'] < $minPrice['VALUE'])? '' : ' style="display: none;"' ?>><?php echo $minPrice['PRINT_VALUE']; ?></span>
										<?}?>
											<span id="<?php echo $arJSParams['VISUAL']['PRICE_ID'] ?>" class="price"><?php echo $minPrice['PRINT_DISCOUNT_VALUE'] ?></span>
									</div>
<?
									unset($minPrice);
?>
									<div class="button-container hidden-xs">
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
										if('Y' == $arParams['USE_FAVORITES'])
										{?>
											<div class="wishlist"><a data-toggle="tooltip" data-placement="top" title="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" data-in-favorites="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" data-in-delfavorites="<?php echo Loc::getMessage('MSG_IN_FAVORITES')?>" class="btn add2liked add2liked_<?php echo $arItem['ID']?>" data-liked-id="<?php echo $arItem['ID']?>" href="javascript:void(0);">
												<span><?php echo Loc::getMessage('MSG_IN_FAVORITES')?></span>
											</a></div>
										<?}
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
											<?} else if($arParams['PRODUCT_DISPLAY_MODE'] != 'Y') {?>
												<a class="btn un_buttoncart more" role="button" title="<?php echo $arParams['MESS_BTN_DETAIL']?>" href="<?php echo $arItem["DETAIL_PAGE_URL"]?>">
													<span class="text"><?php echo $arParams['MESS_BTN_DETAIL']?></span>
												</a>
											<?} else {?>
												
												<?if(!$showSubscribeBtn):?>
													<span class="notavailable"><?php echo $notAvailableMessage ?></span>
												<?endif;?>

												<?if($showSubscribeBtn)
													{
														$APPLICATION->includeComponent('bitrix:catalog.product.subscribe','',
															array(
																'PRODUCT_ID' => $arItem['ID'],
																'BUTTON_ID' => $arrMainID['SUBSCRIBE_LINK'],
																'BUTTON_CLASS' => 'btn btn-primary',
																'DEFAULT_DISPLAY' => !$arItem['CAN_BUY'],
															),
															$component, array('HIDE_ICONS' => 'Y')
														);
													}?>
												
											<?}
										} else if($OFFERS)
										{
											if($arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
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

												<?if(!$showSubscribeBtn):?>
													<span class="notavailable" <?=($arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'])? 'style="display: none;"' : '' ?> id="<?php echo $arJSParams['VISUAL']['NOT_AVAILABLE_MESS']?>"><?php echo $notAvailableMessage ?></span>
												<?endif;?>

												<?if($showSubscribeBtn)
												{
													$APPLICATION->includeComponent('bitrix:catalog.product.subscribe','',
														array(
															'PRODUCT_ID' => $arItem['ID'],
															'BUTTON_ID' => $arrMainID['SUBSCRIBE_LINK'],
															'BUTTON_CLASS' => 'btn btn-primary',
															'DEFAULT_DISPLAY' => !$arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'],
														),
														$component, array('HIDE_ICONS' => 'Y')
													);
												}

											} else {
												?><a class="btn un_buttoncart more" role="button" title="<?php echo $arParams['MESS_BTN_DETAIL']?>" href="<?php echo $arItem["DETAIL_PAGE_URL"]?>">
													<span class="text"><?php echo $arParams['MESS_BTN_DETAIL']?></span>
												</a><?
											}
									}
									if($arParams['DISPLAY_COMPARE'])
									{?>
										<div class="compare"><a data-toggle="tooltip" data-placement="top" href="<?php echo $arItem['COMPARE_URL']?>" class="btn add2compare add2compare_<?php echo $arItem['ID']?>" data-compareurl="<?php echo $arParams['COMPARE_PATH']?>" title="<?php echo $arParams['MESS_BTN_COMPARE']?>" id="<?php echo $arJSParams['VISUAL']['COMPARE_ID']?>" data-compare="<?php echo $arItem['ID']?>">
											<span><?php echo $arParams['MESS_BTN_COMPARE']?></span>
										</a></div>
									<?}
									/************************ ADD TO CART end *************************/?>
									</div>
								</div>
							</div>
						</div>
						<?if($OFFERS && !empty($arItem['OFFERS_PROP']) && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
							?><script type="text/javascript">
								var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
							</script><?
						}?>
					</article>
				<?}?>
			</div>
		</div>
	</div>
<?}