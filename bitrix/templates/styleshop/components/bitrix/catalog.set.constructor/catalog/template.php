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
$this->setFrameMode(true);

$templateData = array(
	'CURRENCIES' => CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true)
);
$curJsId = $this->randString();
?>
<div id="bx-set-const-<?=$curJsId?>" class="bx-modal-container container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="bx-modal-small-title h2"><?=GetMessage("CATALOG_SET_BUY_SET")?></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3">
			<div class="bx-original-item-container">
				<?if ($arResult["ELEMENT"]["DETAIL_PICTURE"]["src"]):?>
					<img class="img-thumbnail" src="<?=$arResult["ELEMENT"]["DETAIL_PICTURE"]["src"]?>" style="width: 70px;height: auto;" alt="">
				<?else:?>
					<img class="img-thumbnail" src="<?=$this->GetFolder().'/images/no_foto.png'?>" style="width: 70px;height: auto;" alt="">
				<?endif?>

				<div>
					<?=$arResult["ELEMENT"]["NAME"]?> <br>
					<span class="bx-added-item-new-price"><strong><?=$arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"]?></strong> * <?=$arResult["ELEMENT"]["BASKET_QUANTITY"]; ?> <?=$arResult["ELEMENT"]["MEASURE"]["SYMBOL_RUS"]; ?></span>
					<?if (!($arResult["ELEMENT"]["PRICE_VALUE"] == $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"])):?><span class="bx-catalog-set-item-price-old"><strong><?=$arResult["ELEMENT"]["PRICE_PRINT_VALUE"]?></strong></span><?endif?>

				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="bx-added-item-table-container">
				<table class="bx-added-item-table table">
					<tbody data-role="set-items">
					<?foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem):?>
						<tr
							data-id="<?=$arItem["ID"]?>"
							data-img="<?=$arItem["DETAIL_PICTURE"]["src"]?>"
							data-url="<?=$arItem["DETAIL_PAGE_URL"]?>"
							data-name="<?=$arItem["NAME"]?>"
							data-price="<?=$arItem["PRICE_DISCOUNT_VALUE"]?>"
							data-print-price="<?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?>"
							data-old-price="<?=$arItem["PRICE_VALUE"]?>"
							data-print-old-price="<?=$arItem["PRICE_PRINT_VALUE"]?>"
							data-diff-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"
							data-measure="<?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?>"
							data-quantity="<?=$arItem["BASKET_QUANTITY"]; ?>"
						>
							<td class="bx-added-item-table-cell-img">
								<?if ($arItem["DETAIL_PICTURE"]["src"]):?>
									<img src="<?=$arItem["DETAIL_PICTURE"]["src"]?>" class="img-thumbnail" alt="">
								<?else:?>
									<img src="<?=$this->GetFolder().'/images/no_foto.png'?>" class="img-thumbnail" alt="">
								<?endif?>
							</td>
							<td class="bx-added-item-table-cell-itemname">
								<a class="tdn" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
							</td>
							<td class="bx-added-item-table-cell-price">
								<span class="bx-added-item-new-price"><?= $arItem["PRICE_PRINT_DISCOUNT_VALUE"]?> * <?=$arItem["BASKET_QUANTITY"]; ?> <?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?></span>
								<?if ($arItem["PRICE_VALUE"] != $arItem["PRICE_DISCOUNT_VALUE"]):?>
									<br><span class="bx-added-item-old-price"><?=$arItem["PRICE_PRINT_VALUE"]?></span>
								<?endif?>
							</td>
							<td class="bx-added-item-table-cell-del"><div class="bx-added-item-delete" data-role="set-delete-btn"></div></td>
						</tr>
					<?endforeach?>
					</tbody>
				</table><div style="display: none;" data-set-message="empty-set"></div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="bx-constructor-container-result content_price<?=($arResult["SET_ITEMS"]["OLD_PRICE"])? ' old' : ''?>">
				<span class="bx-item-set-current-price price" data-role="set-price"><?=$arResult["SET_ITEMS"]["PRICE"]?></span>
				<span class="bx-added-item-old-price old-price"<?=($arResult["SET_ITEMS"]["OLD_PRICE"])? '' : ' style="display: none;"'?> data-role="set-old-price"><?
					if ($arResult["SET_ITEMS"]["OLD_PRICE"])
					{
						?><?= $arResult["SET_ITEMS"]["OLD_PRICE"] ?><?
					}
				?></span> <span class="bx-item-set-economy-price" data-role="set-diff-price"><?
					if ($arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"])
					{
						?><?=GetMessage("CATALOG_SET_DISCOUNT_DIFF", array("#PRICE#" => $arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"])) ?><?
					}
				?></span>
				<div>
					<a href="javascript:void(0)" data-role="set-buy-btn" data-added="<?=GetMessage('MESS_BTN_ADDED')?>" data-loading="<?=GetMessage('MESS_BTN_LOADING')?>" class="btn btn-primary btn-add btn-sm"><?=GetMessage("CATALOG_SET_BUY")?></a>
				</div>
			</div>
		</div>
	</div>

	<div class="bx-catalog-set-topsale-slider-box">
		<div class="bx-catalog-set-topsale-slider-container">
			<div class="bx-catalog-set-topsale-slids bx-catalog-set-topsale-slids-<?=$curJsId?>" data-role="set-other-items">
				<?
				$first = true;
				foreach($arResult["SET_ITEMS"]["OTHER"] as $key => $arItem):?>
				<div class="bx-catalog-set-item-container bx-catalog-set-item-container-<?=$curJsId?>"
					data-id="<?=$arItem["ID"]?>"
					data-img="<?=$arItem["DETAIL_PICTURE"]["src"]?>"
					data-url="<?=$arItem["DETAIL_PAGE_URL"]?>"
					data-name="<?=$arItem["NAME"]?>"
					data-price="<?=$arItem["PRICE_DISCOUNT_VALUE"]?>"
					data-print-price="<?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?>"
					data-old-price="<?=$arItem["PRICE_VALUE"]?>"
					data-print-old-price="<?=$arItem["PRICE_PRINT_VALUE"]?>"
					data-diff-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"
					data-measure="<?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?>"
					data-quantity="<?=$arItem["BASKET_QUANTITY"]; ?>"<?
				if (!$arItem['CAN_BUY'] && $first)
				{
					echo 'data-not-avail="yes"';
					$first = false;
				}
				?>
				>
					<div class="bx-catalog-set-item">
						<div class="bx-catalog-set-item-img">
							<div class="bx-catalog-set-item-img-container">
							<?if ($arItem["DETAIL_PICTURE"]["src"]):?>
								<img src="<?=$arItem["DETAIL_PICTURE"]["src"]?>" class="img-thumbnail" alt=""/>
							<?else:?>
								<img src="<?=$this->GetFolder().'/images/no_foto.png'?>" class="img-thumbnail"/>
							<?endif?>
							</div>
						</div>
						<div class="bx-catalog-set-item-title">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						</div>
						<div class="bx-catalog-set-item-price">
							<div class="bx-catalog-set-item-price-new"><?= $arItem["PRICE_PRINT_DISCOUNT_VALUE"]?> * <?=$arItem["BASKET_QUANTITY"]; ?> <?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?></div>
							<?if ($arItem["PRICE_VALUE"] != $arItem["PRICE_DISCOUNT_VALUE"]):?>
								<div class="bx-catalog-set-item-price-old"><?=$arItem["PRICE_PRINT_VALUE"]?></div>
							<?endif?>
						</div>
						<div class="bx-catalog-set-item-add-btn">
							<?
							if ($arItem['CAN_BUY'])
							{
								?><a href="javascript:void(0)" data-role="set-add-btn" class="btn btn-primary btn-add btn-sm"><?= GetMessage("CATALOG_SET_BUTTON_ADD") ?></a><?
							}
							else
							{
								?><span class="bx-catalog-set-item-notavailable"><? echo GetMessage('CATALOG_SET_MESS_NOT_AVAILABLE'); ?></span><?
							}
							?>
						</div>
					</div>
				</div>
				<?endforeach?>
			</div>
		</div>
	</div>
</div>
<?
$arJsParams = array(
	"numSliderItems" => count($arResult["SET_ITEMS"]["OTHER"]),
	"numSetItems" => count($arResult["SET_ITEMS"]["DEFAULT"]),
	"jsId" => $curJsId,
	"parentContId" => "bx-set-const-".$curJsId,
	"ajaxPath" => $this->GetFolder().'/ajax.php',
	"currency" => $arResult["ELEMENT"]["PRICE_CURRENCY"],
	"mainElementPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"],
	"mainElementOldPrice" => $arResult["ELEMENT"]["PRICE_VALUE"],
	"mainElementDiffPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
	"mainElementBasketQuantity" => $arResult["ELEMENT"]["BASKET_QUANTITY"],
	"lid" => SITE_ID,
	"iblockId" => $arParams["IBLOCK_ID"],
	"basketUrl" => $arParams["BASKET_URL"],
	"setIds" => $arResult["DEFAULT_SET_IDS"],
	"offersCartProps" => $arParams["OFFERS_CART_PROPERTIES"],
	"itemsRatio" => $arResult["BASKET_QUANTITY"],
	"noFotoSrc" => $this->GetFolder().'/images/no_foto.png',
	"messages" => array(
		"EMPTY_SET" => GetMessage('CT_BCE_CATALOG_MESS_EMPTY_SET'),
		"ADD_BUTTON" => GetMessage("CATALOG_SET_BUTTON_ADD"),
		"CATALOG_SET_DISCOUNT_DIFF" => GetMessage("CATALOG_SET_DISCOUNT_DIFF")
	)
);
?>
<script type="text/javascript">
	BX.ready(function(){
		new BX.Catalog.SetConstructor(<?=CUtil::PhpToJSObject($arJsParams, false, true, true)?>);
	});
</script>