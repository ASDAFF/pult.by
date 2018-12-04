<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arResult["CATEGORIES"]))
	return;
?>
<div class="bx_searche">
	<div class="panel-body">
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?//echo $arCategory["TITLE"]?>
			<?if($category_id === "all"):?>
				<div class="bx_item_block" style="min-height:0">
					<div class="bx_img_element"></div>
					<div class="bx_item_element"><hr></div>
				</div>
				<div class="bx_item_block all_result">
					<div class="bx_img_element"></div>
					<div class="bx_item_element">
						<span class="all_result_title"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></span>
					</div>
				</div>
			<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
				$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];?>
				<div class="bx_item_block item">
					<div class="row">
						<?if (is_array($arElement["PICTURE"])):?>
						<div class="bx_img_element col-xs-3">
							<img class="img-thumbnail" src="<?echo $arElement["PICTURE"]["src"]?>" alt="<?echo $arItem["NAME"]?>" />
						</div>
						<?endif;?>
						<div class="bx_item_element col-xs-9">
							<h5><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></h5>
							<?
							foreach($arElement["PRICES"] as $code=>$arPrice)
							{
								if ($arPrice["MIN_PRICE"] != "Y")
									continue;

								if($arPrice["CAN_ACCESS"])
								{
									if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<div class="content_price old">
											<span class="old-price"><?=$arPrice["PRINT_VALUE"]?></span>
											<span class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
										</div>
									<?else:?>
										<div class="content_price"><span class="price"><?=$arPrice["PRINT_VALUE"]?></span></div>
									<?endif;
								}
								if ($arPrice["MIN_PRICE"] == "Y")
									break;
							}
							?>
						</div>
					</div>
				</div>
			<?else:?>
				<div class="bx_item_block others_result">
					<div class="bx_img_element"></div>
					<div class="bx_item_element">
						<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
					</div>
				</div>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
	</div>
</div>