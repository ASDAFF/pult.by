<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
use Bitrix\Sale\DiscountCouponsManager;

if (!empty($arResult["ERROR_MESSAGE"]))
	ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;

if ($normalCount > 0):
?>
<div id="basket_items_list">
	<div class="bx_ordercart_order_table_container">
		<table class="table" id="basket_items">
			<thead>
				<tr>
					<?
					foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
						$arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
						if ($arHeader["name"] == '')
							$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);
						$arHeaders[] = $arHeader["id"];

						// remember which values should be shown not in the separate columns, but inside other columns
						if (in_array($arHeader["id"], array("TYPE")))
						{
							$bPriceType = true;
							continue;
						}
						elseif ($arHeader["id"] == "PROPS")
						{
							$bPropsColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELAY")
						{
							$bDelayColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELETE")
						{
							$bDeleteColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "WEIGHT")
						{
							$bWeightColumn = true;
						}

						if ($arHeader["id"] == "NAME"):
						?>
							<td class="item" colspan="2" id="col_<?=$arHeader["id"];?>">
						<?
						elseif ($arHeader["id"] == "PRICE"):
						?>
							<td class="price" id="col_<?=$arHeader["id"];?>">
						<?
						else:
						?>
							<td class="custom" id="col_<?=$arHeader["id"];?>">
						<?
						endif;
						?>
							<?=$arHeader["name"]; ?>
							</td>
					<?
					endforeach;

					if ($bDeleteColumn || $bDelayColumn):
					?>
						<td class="custom"></td>
					<?
					endif;
					?>
				</tr>
			</thead>

			<tbody>
				<?
				foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):

					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
				?>
					<tr id="<?=$arItem["ID"]?>">
						<?
						foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):

							if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE"))) // some values are not shown in the columns in this template
								continue;

							if ($arHeader["name"] == '')
								$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);

							if ($arHeader["id"] == "NAME"):

								$sku = '';
								ob_start();

								if(!empty($arItem["PROPS"])):
									?><ul class="bx_ordercart_itemart list-inline clearfix">
											<?
											if ($bPropsColumn):
												foreach ($arItem["PROPS"] as $val):

													if (is_array($arItem["SKU_DATA"]))
													{
														$bSkip = false;
														foreach ($arItem["SKU_DATA"] as $propId => $arProp)
														{
															if ($arProp["CODE"] == $val["CODE"])
															{
																$bSkip = true;
																break;
															}
														}
														if ($bSkip)
															continue;
													}
													?>
													<li><span class="bx_ordercart_itemart-name"><?php echo $val["NAME"] ?></span></li>
													<li><span class="bx_ordercart_itemart-value"><?php echo $val["VALUE"] ?></span></li>
													<?
												endforeach;
											endif;
											?>
										</ul>
										<?
									endif;
									
										if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):
											?><div class="block-sku"><?
											foreach ($arItem["SKU_DATA"] as $propId => $arProp):

												// if property contains images or values
												$isImgProperty = false;
												if (!empty($arProp["VALUES"]) && is_array($arProp["VALUES"]))
												{
													foreach ($arProp["VALUES"] as $id => $arVal)
													{
														if (!empty($arVal["PICT"]) && is_array($arVal["PICT"])
															&& !empty($arVal["PICT"]['SRC']))
														{
															$isImgProperty = true;
															break;
														}
													}
												}

												if ($isImgProperty): // iblock element relation property
												?>
													<div class="block-sku-detail-sku">

														<span class="block-sku-detail-sku-name">
															<?=htmlspecialcharsex($arProp["NAME"])?>:
														</span>

														<div class="block-sku-detail-sku-container">

															<div class="bx_scu">
																<ul class="block-sku-detail-sku-container-list" id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>">
																	<?
																	foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																		$selected = "";
																		foreach ($arItem["PROPS"] as $arItemProp):
																			if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																			{
																				if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
																					$selected = " active";
																			}
																		endforeach;
																	?>
																		<li
																			class="sku_prop<?=$selected?>"
																			data-sku-selector="Y"
																			data-value-id="<?=$arSkuValue["XML_ID"]?>"
																			data-sku-name="<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>"
																			data-element="<?=$arItem["ID"]?>"
																			data-property="<?=$arProp["CODE"]?>">
																			<span class="color cnt"><span class="cnt_item" style="background-image:url(<?=$arSkuValue["PICT"]["SRC"];?>)"></span></span>
																		</li>
																	<?
																	endforeach;
																	?>
																</ul>
															</div>
														</div>

													</div>
												<?
												else:
												?>
													<div class="block-sku-detail-sku">

														<span class="block-sku-detail-sku-name">
															<?=htmlspecialcharsex($arProp["NAME"])?>:
														</span>

														<div class="block-sku-detail-sku-container">
															<div class="bx_size">
																<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
																	class="block-sku-detail-sku-container-list"
																	>
																	<?
																	foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																		$selected = "";
																		foreach ($arItem["PROPS"] as $arItemProp):
																			if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																			{
																				if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																					$selected = " active";
																			}
																		endforeach;
																	?>
																		<li
																			class="sku_prop<?=$selected?>"
																			data-sku-selector="Y"
																			data-value-id="<?=($arSkuValue['XML_ID'] ? $arSkuValue['XML_ID'] : htmlspecialcharsbx($arSkuValue['NAME'])); ?>"
																			data-sku-name="<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>"
																			data-element="<?=$arItem["ID"]?>"
																			data-property="<?=$arProp["CODE"]?>"
																			>
																			<span class="size cnt"><?=$arSkuValue["NAME"]?></span>
																		</li>
																	<?
																	endforeach;
																	?>
																</ul>
															</div>
														</div>

													</div>
												<?
												endif;
											endforeach;
											?></div><?
										endif;

								$sku = ob_get_contents();
								ob_end_clean();

							?>
								<td class="itemphoto">
									<div class="bx_ordercart_photo_container">
										<?
										if (strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0):
											$url = $arItem["PREVIEW_PICTURE_SRC"];
										elseif (strlen($arItem["DETAIL_PICTURE_SRC"]) > 0):
											$url = $arItem["DETAIL_PICTURE_SRC"];
										else:
											$url = $templateFolder."/images/no_photo.png";
										endif;
										?>

										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
											<img class="img-thumbnail" src="<?=$url?>" alt="" />
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									</div>
									<?
									if (!empty($arItem["BRAND"])):
									?>
									<div class="bx_ordercart_brand">
										<img alt="" src="<?=$arItem["BRAND"]?>" />
									</div>
									<?
									endif;
									?>
								</td>
								<td class="item">
									<h4 class="bx_ordercart_itemtitle">
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
											<?=$arItem["NAME"]?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									</h4>
									<?php echo $sku ?>
								</td>
							<?
							elseif ($arHeader["id"] == "QUANTITY"):
							?>
							<?php $mobileMessage['QUANTITY'] = GetMessage("SALE_".$arHeader["id"]."2") ?>
								<td class="custom" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]."2"); ?>">
									<span><?=$arHeader["name"]; ?>:</span>
									<div class="centered">
										<div class="form-inline">
						          <div class="quantity form-group">
							          <div class="input-group">
							          <?

					              if (!isset($arItem["MEASURE_RATIO"]))
												{
													$arItem["MEASURE_RATIO"] = 1;
												}

							          $ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 0;
												$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
												$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
												$useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");

							          	if (floatval($arItem["MEASURE_RATIO"]) != 0):
							          ?>
							          	<div class="input-group-btn">
						              	<button type="button" class="btn btn-sm btn-default minus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'down', <?=$useFloatQuantityJS?>);">-</button>
						              </div>
						              <?
						              endif;
						              ?>
						              <input
						              	class="form-control qty min text-center"
														type="text"
														size="3"
														id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
														name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
														maxlength="10"
														min="<?=$ratio?>"
														<?=$max?>
														step="<?=$ratio?>"
														value="<?=$arItem["QUANTITY"]?>"
														onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)">
													<?
							          		if (floatval($arItem["MEASURE_RATIO"]) != 0):
								          ?>
						              <div class="input-group-btn">
						              	<button type="button" class="btn btn-sm btn-default plus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'up', <?=$useFloatQuantityJS?>);">+</button>
						              </div>
							          </div>
							          <?
						              endif;
						            ?>
							          <?

							          if (isset($arItem["MEASURE_TEXT"]))
												{
													?>
														<span class="measure_name"><?=$arItem["MEASURE_TEXT"]?></span>
													<?
												}
												?>
						          </div>
						         </div>
									</div>
									<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
								</td>
							<?
							elseif ($arHeader["id"] == "PRICE"):
							?>
								<?php $mobileMessage['PRICE'] = GetMessage("SALE_".$arHeader["id"]) ?>
								<td class="content_price<?if(0 < floatval($arItem["DISCOUNT_PRICE"])):?> old<?endif?>" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]); ?>">
										<div class="price" id="current_price_<?=$arItem["ID"]?>">
											<?=$arItem["PRICE_FORMATED"]?>
										</div>
										<div class="old-price" id="old_price_<?=$arItem["ID"]?>">
											<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
												<?=$arItem["FULL_PRICE_FORMATED"]?>
											<?endif;?>
										</div>

									<?if ($bPriceType && strlen($arItem["NOTES"]) > 0):?>
										<div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
										<div class="type_price_value"><?=$arItem["NOTES"]?></div>
									<?endif;?>
								</td>
							<?
							elseif ($arHeader["id"] == "DISCOUNT"):
							?>
								<?php $mobileMessage['DISCOUNT'] = GetMessage("SALE_".$arHeader["id"]) ?>
								<td class="custom discount" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]); ?>">
									<span><?=$arHeader["name"]; ?>:</span>
									<div id="discount_value_<?=$arItem["ID"]?>"><?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?></div>
								</td>
							<?
							elseif ($arHeader["id"] == "WEIGHT"):
							?>
								<td class="custom">
									<span><?=$arHeader["name"]; ?>:</span>
									<?=$arItem["WEIGHT_FORMATED"]?>
								</td>
							<?
							else:
							?>
								<?php $mobileMessage[$arHeader["id"]] = $arHeader["name"] ?>
								<td class="custom sum content_price" data-label="<?=$arHeader["name"]; ?>">
									<span><?=$arHeader["name"]; ?>:</span>
									<?
									if ($arHeader["id"] == "SUM"):
									?>
										<div class="price" id="sum_<?=$arItem["ID"]?>">
									<?
									endif;

									echo $arItem[$arHeader["id"]];

									if ($arHeader["id"] == "SUM"):
									?>
										</div>
									<?
									endif;
									?>
								</td>
							<?
							endif;
						endforeach;

						if ($bDelayColumn || $bDeleteColumn):
						?>
							<td class="control">
								<?
								if ($bDeleteColumn):
								?>
									<div><a class="delete" title="<?=GetMessage("SALE_DELETE")?>" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"></a></div>
								<?
								endif;
								if ($bDelayColumn):
								?>
									<div><a class="delay" title="<?=GetMessage("SALE_DELAY")?>" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>"></a></div>
								<?
								endif;
								?>
							</td>
						<?
						endif;
						?>
					</tr>
					<?
					endif;
				endforeach;
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="column_headers" value="<?=htmlspecialcharsbx(implode($arHeaders, ","))?>" />
	<input type="hidden" id="offers_props" value="<?=htmlspecialcharsbx(implode($arParams["OFFERS_PROPS"], ","))?>" />
	<input type="hidden" id="action_var" value="<?=htmlspecialcharsbx($arParams["ACTION_VARIABLE"])?>" />
	<input type="hidden" id="quantity_float" value="<?=($arParams["QUANTITY_FLOAT"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="auto_calculation" value="<?=($arParams["AUTO_CALCULATION"] == "N") ? "N" : "Y"?>" />

	<div class="bx_ordercart_order_pay row">

		<div class="bx_ordercart_order_pay_left col-xs-12 col-sm-5 col-md-4 col-lg-3" id="coupons_block">
		<?
		if ($arParams["HIDE_COUPON"] != "Y")
		{
		?>
			<div class="bx_ordercart_coupon">
				<label for="coupon"><?=GetMessage("STB_COUPON_PROMT")?></label>
				<div class="form-group">
					<div class="input-group"><input class="form-control" type="text" id="coupon" name="COUPON" value="" onchange="enterCoupon();">
						<div class="input-group-btn">
							<a class="bx_bt_button bx_big btn btn-default" href="javascript:void(0)" onclick="enterCoupon();" title="<?=GetMessage('SALE_COUPON_APPLY_TITLE'); ?>"><?=GetMessage('SALE_COUPON_APPLY'); ?></a>
						</div>
					</div>
				</div>
			</div><?
				if (!empty($arResult['COUPON_LIST']))
				{
					foreach ($arResult['COUPON_LIST'] as $oneCoupon)
					{
						$couponClass = 'disabled';
						switch ($oneCoupon['STATUS'])
						{
							case DiscountCouponsManager::STATUS_NOT_FOUND:
							case DiscountCouponsManager::STATUS_FREEZE:
								$couponClass = 'bad';
								break;
							case DiscountCouponsManager::STATUS_APPLYED:
								$couponClass = 'good';
								break;
						}
						?><div class="bx_ordercart_coupon"><input disabled readonly type="text" name="OLD_COUPON[]" value="<?=htmlspecialcharsbx($oneCoupon['COUPON']);?>" class="<? echo $couponClass; ?>"><span class="<? echo $couponClass; ?>" data-coupon="<? echo htmlspecialcharsbx($oneCoupon['COUPON']); ?>"></span><div class="bx_ordercart_coupon_notes"><?
						if (isset($oneCoupon['CHECK_CODE_TEXT']))
						{
							echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
						}
						?></div></div><?
					}
					unset($couponClass, $oneCoupon);
				}
		}
		else
		{
			?>&nbsp;<?
		}
?>
		</div>
		<div class="bx_ordercart_order_pay_right col-xs-12 col-sm-7 col-md-8 col-lg-9">
			<table class="bx_ordercart_order_sum">
				<?if ($bWeightColumn && floatval($arResult['allWeight']) > 0):?>
					<tr>
						<td class="custom_t1"><?=GetMessage("SALE_TOTAL_WEIGHT")?></td>
						<td class="custom_t2" id="allWeight_FORMATED"><?=$arResult["allWeight_FORMATED"]?>
						</td>
					</tr>
				<?endif;?>
				<?if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y"):?>
					<tr>
						<td><?echo GetMessage('SALE_VAT_EXCLUDED')?></td>
						<td id="allSum_wVAT_FORMATED"><?=$arResult["allSum_wVAT_FORMATED"]?></td>
					</tr>
					<?if (floatval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
						<tr>
							<td class="custom_t1"></td>
							<td class="custom_t2" style="text-decoration:line-through; color:#828282;" id="PRICE_WITHOUT_DISCOUNT">
								<?=$arResult["PRICE_WITHOUT_DISCOUNT"]?>
							</td>
						</tr>
					<?endif;?>
					<?
					if (floatval($arResult['allVATSum']) > 0):
						?>
						<tr>
							<td><?echo GetMessage('SALE_VAT')?></td>
							<td id="allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></td>
						</tr>
						<?
					endif;
					?>
				<?endif;?>
					<tr>
						<td class="fwb"><?=GetMessage("SALE_TOTAL")?></td>
						<td class="fwb" id="allSum_FORMATED"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></td>
					</tr>


			</table>
			<div style="clear:both;"></div>
		</div>
		<div style="clear:both;"></div>
		<div class="bx_ordercart_order_pay_center col-xs-12">

			<?if ($arParams["USE_PREPAYMENT"] == "Y" && strlen($arResult["PREPAY_BUTTON"]) > 0):?>
				<?=$arResult["PREPAY_BUTTON"]?>
				<span><?=GetMessage("SALE_OR")?></span>
			<?endif;?>
			<?
			if ($arParams["AUTO_CALCULATION"] != "Y")
			{
				?>
				<a href="javascript:void(0)" onclick="updateBasket();" class="btn btn-lg btn-default checkout refresh"><?=GetMessage("SALE_REFRESH")?></a>
				<?
			}
			?>
			<a href="javascript:void(0)" onclick="checkOut();" class="btn btn-lg btn-primary checkout"><?=GetMessage("SALE_ORDER")?></a>
		</div>
	</div>
</div>
<?
else:
?>
<div id="basket_items_list" style="margin-top: 10px;">
	<p><font class="errortext"><?=GetMessage("SALE_NO_ITEMS");?></font></p>
</div>
<?
endif;
?>
<script>
	var basketMessage = <?=CUtil::PhpToJSObject($mobileMessage, false, true)?>
</script>