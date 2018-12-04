<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use Bitrix\Main\Localization\Loc;

$frame = $this->createFrame()->begin("");

$injectId = $arParams['UNIQ_COMPONENT_ID'];

if (isset($arResult['REQUEST_ITEMS']))
{
	// code to receive recommendations from the cloud
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?>

	<span id="<?=$injectId?>"></span>

	<script type="text/javascript">
		BX.ready(function(){
			bx_rcm_get_from_cloud(
				'<?=CUtil::JSEscape($injectId)?>',
				<?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
				{
					'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
					'template': '<?=CUtil::JSEscape($signedTemplate)?>',
					'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
					'rcm': 'yes'
				}
			);
		});
	</script>
	<?
	$frame->end();
	return;

	// \ end of the code to receive recommendations from the cloud
}


if (!empty($arResult['ITEMS']))
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
		
		         $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" '.$descTooltip.' data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.htmlspecialcharsbx($arOneValue["NAME"]).'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="size cnt" title="'.htmlspecialcharsbx($arOneValue['NAME']).'">'.htmlspecialcharsbx($arOneValue['NAME']).'</span></li>';
		
		        }
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
		<?if($arParams['MESS_TITLE']) {?>
			<h2 class="product-title"><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></h2>
		<?}

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
			$sliderOptions['items'] = $arParams['CAROUSEL_ITEMS'];

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
			<div class="unproduct <?if($arParams['USE_CAROUSEL'] == 'Y'):?>owl-carousel Owlcarousel Owlcarouselbigdata<?endif?>" <?if($arParams['USE_CAROUSEL'] == 'Y'):?>data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>"<?endif?>>
				<?foreach ($arResult['ITEMS'] as $key => $arItem)
				{
					$strMainID = $this->GetEditAreaId($arItem['ID'] . $key);

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
						'PRODUCT' => array(
							'ID' => $arItem['ID'],
							'NAME' => $arrMainID['NAME'],
						),
						'VISUAL' => array(
							'ID' => $arrMainID['ID'],
							'NAME' => $arrMainID['NAME'],
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
						'PRODUCTDAY_PROP' => $arItem['PROPERTIES']['PRODUCTDAY']['VALUE']
					);

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

					$strTitle = (
					isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && '' != isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
						? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
						: $arItem['NAME']
					);
					?><article class="unproduct-item js-item<?if($arParams['USE_CAROUSEL'] != 'Y'):?> col-xs-<?php echo $arParams['RESPONSIVE_ITEMS_XS'] ?> col-sm-<?php echo $arParams['RESPONSIVE_ITEMS_SM'] ?> col-md-<?php echo $arParams['RESPONSIVE_ITEMS_MD'] ?> col-lg-<?php echo $arParams['RESPONSIVE_ITEMS_LG'] ?><?endif?>" id="<?php echo $strMainID ?>"><?
						?><div class="unproduct-container"><?
							/********************************************** IMAGES CONTAINER ************************************************/
							if($arParams['SHOW_IMAGE'] == 'Y')
							{
								?><figure class="unproduct-image-container">
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
											<img class="picture__first" id="<?php echo $arJSParams['VISUAL']['PICT_ID']?>" src="<?php echo $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $strTitle?>" />
										<?}
										if('Y' == $arParams['HOVER_IMAGE'])
										{?>
											<img class="picture__second" id="<?php echo $arJSParams['VISUAL']['SECOND_PICT_ID']?>" src="<?php echo (!empty($arItem['PREVIEW_PICTURE_SECOND']))? $arItem['PREVIEW_PICTURE_SECOND']['SRC'] : $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $strTitle?>" />
										<?}?>
									</a>
									<?/********************** IMAGE end ************************/
									/************ COMPARE, QUICK LOOK, FAVORITES **********/
									if($OFFERS && !empty($arItem['OFFERS_PROP']))
									{?>
										<div class="mask hidden-xs">
											<?/********************************* SKU *************************************/
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
								<?}
								/********************************************** IMAGES CONTAINER end ************************************************/?>
							<div class="unproduct-bottom-container">
								<?if ($arParams['SHOW_NAME'] == 'Y')
								{?>
									<div class="name_unproduct">
										<a title="<?php echo $arItem['NAME']?>" href="<?php echo $arItem['DETAIL_PAGE_URL']?>"><?php echo $arItem['NAME']?></a>
									</div>
								<?}
?>
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
									<div class="button-container hidden-xs"><?
									/************************ ADD TO CART *************************/
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
											<?} else {?>
												<a class="btn un_buttoncart more" role="button" title="<?php echo $arParams['MESS_BTN_DETAIL']?>" href="<?php echo $arItem["DETAIL_PAGE_URL"]?>">
													<span class="text"><?php echo $arParams['MESS_BTN_DETAIL']?></span>
												</a>
											<?}
										} else if($OFFERS)
										{
												?><a
												id="<?php echo $arJSParams['VISUAL']['BUY_ID']?>"
												<?=($arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'])? '' : 'style="display: none;"' ?>
												class="btn offers add2basket add2basket_<?php echo $arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['ID'] ?> un_buttoncart"
												data-options="<?php echo CUtil::PhpToJSObject($arSettingsBasket, false, true); ?>"
												data-parentelementid="<?php echo $arItem['ID'] ?>"
												data-elementid="<?php echo $arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['ID'] ?>"
												role="button" title="<?php echo $addCart ?>" href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>"><?
													?><span class="text"><?php echo $addCart ?></span><?
												?></a>

											<a class="btn un_buttoncart more" id="<?php echo $arJSParams['VISUAL']['NOT_AVAILABLE_MESS']?>" style="display: none;" role="button" title="<?php echo $arParams['MESS_BTN_DETAIL']?>" href="<?php echo $arItem["DETAIL_PAGE_URL"]?>">
												<span class="text"><?php echo $arParams['MESS_BTN_DETAIL']?></span>
											</a><?
										}

									if($arParams['DISPLAY_COMPARE'])
									{?>
										<div class="compare"><a data-toggle="tooltip" data-placement="top" href="<?php echo $arItem['COMPARE_URL'] ?>" class="btn add2compare add2compare_<?php echo $arItem['ID']?>" data-compareurl="<?php echo $arParams['COMPARE_PATH'] ?>" title="<?php echo Loc::getMessage('MSG_IN_COMPARE')?>" id="<?php echo $arJSParams['VISUAL']['COMPARE_ID']?>" data-compare="<?php echo $arItem['ID']?>">
											<span><?php echo Loc::getMessage('MSG_IN_COMPARE')?></span>
										</a></div>
									<?}
									/************************ /ADD TO CART *************************/?>
									</div>
								</div>
							</div>
						</div>
						<?if($OFFERS && !empty($arItem['OFFERS_PROP'])):?>
							<script type="text/javascript">
								var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
							</script>
						<?endif?>
					</article>
				<?}?>
			</div>
		</div>
	</div>
<?}

$frame->end();