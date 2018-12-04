<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$bPriceType  = false;
$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
?>
<div id="basket_items_delayed" class="bx_ordercart_order_table_container" style="display:none">
	<table class="table" id="delayed_items">
		<thead>
			<tr>
				<?
				foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
					$arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
					if ($arHeader["name"] == '')
						$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);
					if (in_array($arHeader["id"], array("TYPE"))) // some header columns are shown differently
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
			foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):

				if ($arItem["DELAY"] == "Y" && $arItem["CAN_BUY"] == "Y"):
			?>
				<tr id="<?=$arItem["ID"]?>">
					<?
					foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):

						if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE"))) // some values are not shown in columns in this template
							continue;

						if ($arHeader["id"] == "NAME"):
						
							$sku = '';
							$arColor = [];
							ob_start();

							if(!empty($arItem["PROPS"])):
								?><ul class="bx_ordercart_itemart list-inline clearfix mb">
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
							<td class="custom" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]); ?>">
								<span><?=$arHeader["name"]; ?>:</span>
								<div style="text-align: center;">
									<?echo $arItem["QUANTITY"];
										if (isset($arItem["MEASURE_TEXT"]))
											echo "&nbsp;".$arItem["MEASURE_TEXT"];
									?>
								</div>
							</td>
						<?
						elseif ($arHeader["id"] == "PRICE"):
						?>
							<td class="content_price<?if(0 < floatval($arItem["DISCOUNT_PRICE"])):?> old<?endif?>" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]); ?>">
								<?if (doubleval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
									<div class="price"><?=$arItem["PRICE_FORMATED"]?></div>
									<div class="old-price"><?=$arItem["FULL_PRICE_FORMATED"]?></div>
								<?else:?>
									<div class="price"><?=$arItem["PRICE_FORMATED"];?></div>
								<?endif?>

								<?if ($bPriceType && strlen($arItem["NOTES"]) > 0):?>
									<div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
									<div class="type_price_value"><?=$arItem["NOTES"]?></div>
								<?endif;?>
							</td>
						<?
						elseif ($arHeader["id"] == "DISCOUNT"):
						?>
							<td class="custom" data-label="<?php echo GetMessage("SALE_".$arHeader["id"]); ?>">
								<span><?=$arHeader["name"]; ?>:</span>
								<?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
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
							<td class="custom sum" data-label="<?=GetMessage("SALE_".$arHeader["id"]); ?>">
								<span><?=GetMessage("SALE_".$arHeader["id"]); ?>:</span>
								<?=$arItem[$arHeader["id"]]?>
							</td>
						<?
						endif;
					endforeach;

					if ($bDelayColumn || $bDeleteColumn):
					?>
						<td class="control">
							<div><a class="add" title="<?=GetMessage("SALE_ADD_TO_BASKET")?>" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["add"])?>"></a></div>
							<?
							if ($bDeleteColumn):
							?>
								<div><a class="delete" title="<?=GetMessage("SALE_DELETE")?>" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"></a></div>
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
<?