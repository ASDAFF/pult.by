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
<div class="js-basket basket-popup clearfix">
	<div class="basket-popup-list">
		<table class="table">
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
							<td class="item" colspan="2">
						<?
						elseif ($arHeader["id"] == "PRICE"):
						?>
							<td class="price">
						<?
						else:
						?>
							<td class="custom">
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
				$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
				foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):


					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
				?>
					<tr class="item" id="<?=$arItem["ID"]?>">
						<?
						foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):

							if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE"))) // some values are not shown in the columns in this template
								continue;

							if ($arHeader["name"] == '')
								$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);

							if ($arHeader["id"] == "NAME"):

								$sku = '';
								$arColor = [];
								ob_start();

								?><ul class="ordercart-itemart list-inline">
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
											<li><span class="ordercart-itemart-name"><?php echo $val["NAME"] ?></span></li>
											<li><span class="ordercart-itemart-value"><?php echo $val["VALUE"] ?></span></li>
											<?
											endforeach;
										endif;
										?>
									</ul>
									<?
									if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):
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
												<div class="bx_item_detail_scu_small_noadaptive">

													<div class="bx_scu_scroller_container">

														<div class="bx_scu">
															<ul
																class="sku_prop_list list-inline">
																<li><?=$arProp["NAME"]?></li>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = false;
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
																				$selected = true;
																		}
																	endforeach;
																	if($selected):
																?>
																	<li
																		data-value-id="<?=$arSkuValue["XML_ID"]?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>">
																		<span class="cnt_item"><?php echo $arSkuValue["NAME"]?></span>
																		<?$arColor[] = $arSkuValue["PICT"]["SRC"]?>
																			<span class="cnt_item" style="background-image:url(<?php echo $arSkuValue["PICT"]["SRC"];?>)">
																		</span>
																	</li>
																<?endif?>
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
												<div class="bx_item_detail_size_small_noadaptive">

													<div class="bx_size_scroller_container">
														<div class="bx_size">
															<ul
																class="sku_prop_list list-inline">
																<li><?=$arProp["NAME"]?></li>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = false;
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																				$selected = true;
																		}
																	endforeach;
																	if($selected):
																?>
																	<li
																		data-value-id="<?=($arProp['TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory' ? $arSkuValue['XML_ID'] : $arSkuValue['NAME']); ?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																		>
																		<span class="cnt"><?=$arSkuValue["NAME"]?></span>
																	</li>
																	<?endif?>
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
									endif;

								$sku = ob_get_contents();
								ob_end_clean();

								?><td class="itemphoto">
										<div class="itemphoto_container">
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
													<img class="img-thumbnail" src="<?=$url?>" alt="<?=$arItem["NAME"]?>" />
													<?
													if(isset($arColor) && !empty($arColor))
													{
														foreach($arColor as $color)
														{
															?><span class="cnt_item" style="background-image:url(<?php echo $color ?>)"></span><?
														}
													}
														?>
												<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
										</div>
									</td>
									<td class="item">
										<h4 class="itemtitle">
											<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
												<?=$arItem["NAME"]?>
											<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
										</h4>
										<?php echo $sku ?>
									</td><?
								
							elseif ($arHeader["id"] == "QUANTITY"):
							?>
								<td class="custom">
									<div class="text-center">
										<?
											$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 0;
											$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
											$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
											$useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");
											if (!isset($arItem["MEASURE_RATIO"]))
											{
												$arItem["MEASURE_RATIO"] = 1;
											}
										?>
										<div class="form-inline">
						          <div class="quantity form-group">
							          <div class="input-group">
							          	<div class="input-group-btn">
						              	<button data-element="<?=$arItem["ID"]?>" type="button" class="btn btn-sm btn-default quantity-down js-quantity-update">-</button>
						              </div>
						              <input
						              	class="form-control qty min text-center"
														type="text"
														size="3"
														id="QUANTITY_<?=$arItem['ID']?>"
														name="QUANTITY_<?=$arItem['ID']?>"
														maxlength="10"
														min="<?=$ratio?>"
														<?=$max?>
														step="<?=$ratio?>"
														value="<?=$arItem["QUANTITY"]?>">
						              <div class="input-group-btn">
						              	<button data-element="<?=$arItem["ID"]?>" type="button" class="btn btn-sm btn-default quantity-up js-quantity-update">+</button>
						              </div>
							          </div>
							          <?
							          if (isset($arItem["MEASURE_TEXT"]))
												{
													?>
														<span class="measure_name" style="display: none;"><?=$arItem["MEASURE_TEXT"]?></span>
													<?
												}
												?>
						          </div>
						         </div>
									</div>
								</td>
							<?
							elseif ($arHeader["id"] == "PRICE"):
							?>
								<td class="content_price<?if(0 < floatval($arItem["DISCOUNT_PRICE"])):?> old<?endif?>">
										<div class="price" id="current_price_<?=$arItem["ID"]?>">
											<?=$arItem["PRICE_FORMATED"]?>
										</div>
										<div class="old-price" id="old_price_<?=$arItem["ID"]?>">
											<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
												<?=$arItem["FULL_PRICE_FORMATED"]?>
											<?endif;?>
										</div>
										<?
											if(0 < floatval($arItem["DISCOUNT_PRICE"]))
											{
												?><div class="discount">
												<?=GetMessage("CATALOG_SET_DISCOUNT_DIFF", array("#PRICE#" => CCurrencyLang::CurrencyFormat($arItem["DISCOUNT_PRICE"], $arItem['CURRENCY']))) ?>
												</div><?
											}
										?>
										<?/*<div class="discount" id="discount_value_<?=$arItem["ID"]?>"
										<?=((floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0))? '' : 'style="display: none;"' ?>>
										<?= GetMessage("CATALOG_SET_DISCOUNT_DIFF", array("#PRICE#" => $arItem["DISCOUNT_PRICE_PERCENT_FORMATED"])) ?>
										</div>*/?>

									<?if ($bPriceType && strlen($arItem["NOTES"]) > 0):?>
										<div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
										<div class="type_price_value"><?=$arItem["NOTES"]?></div>
									<?endif;?>
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
								<td class="custom">
									<?
									if ($arHeader["id"] == "SUM"):
									?>
										<div>
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
									<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"><?=GetMessage("SALE_DELETE")?></a><br />
								<?
								endif;
								if ($bDelayColumn):
								?>
									<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>"><?=GetMessage("SALE_DELAY")?></a>
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

	<div class="bx_ordercart_order_pay">
		<div class="bx_ordercart_order_pay_right clearfix">
			<table class="bx_ordercart_order_sum">
				<?if ($bWeightColumn && floatval($arResult['allWeight']) > 0):?>
					<tr>
						<td class="custom_t1"><?=GetMessage("SALE_TOTAL_WEIGHT")?></td>
						<td class="custom_t2 allWeight_FORMATED"><?=$arResult["allWeight_FORMATED"]?>
						</td>
					</tr>
				<?endif;?>
				<?if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y"):?>
					<tr>
						<td><?echo GetMessage('SALE_VAT_EXCLUDED_COUNT')?></td>
						<td class="allCount"><?=(count($arResult["GRID"]["ROWS"]))?></td>
					</tr>
					<?if (floatval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
						<tr>
							<td class="custom_t1"></td>
							<td class="custom_t2 PRICE_WITHOUT_DISCOUNT" style="text-decoration:line-through; color:#828282;">
								<?=$arResult["PRICE_WITHOUT_DISCOUNT"]?>
							</td>
						</tr>
					<?endif;?>
					<?
					if (floatval($arResult['allVATSum']) > 0):
						?>
						<tr>
							<td><?echo GetMessage('SALE_VAT')?></td>
							<td class="allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></td>
						</tr>
						<?
					endif;
					?>
				<?endif;?>
					<tr>
						<td class="fwb"><?=GetMessage("SALE_TOTAL")?></td>
						<td class="fwb allSum_FORMATED"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></td>
					</tr>
			</table>
		</div>
		<div class="bx_ordercart_order_pay_center">

			<?if ($arParams["USE_PREPAYMENT"] == "Y" && strlen($arResult["PREPAY_BUTTON"]) > 0):?>
				<?=$arResult["PREPAY_BUTTON"]?>
				<span><?=GetMessage("SALE_OR")?></span>
			<?endif;?>
			<a href="#" onclick="jQuery.fancybox.close(); return false;" class="btn btn-default"><?=GetMessage("SALE_MORE")?></a>
			<a href="<?php echo $arParams['BASKET_URL'] ?>" class="btn btn-primary checkout"><?=GetMessage("SALE_BASKET")?></a>
		</div>
	</div>
</div>
<?
else:
?>
<div id="basket_items">
	<table>
		<tbody>
			<tr>
				<td style="text-align:center">
					<div><?=GetMessage("SALE_NO_ITEMS");?></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?
endif;
?>