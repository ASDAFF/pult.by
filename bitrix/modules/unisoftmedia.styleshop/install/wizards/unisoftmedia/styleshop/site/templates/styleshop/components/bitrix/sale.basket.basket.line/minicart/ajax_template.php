<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$this->IncludeLangFile('template.php');

$cartId = $arParams['cartId'];

require(realpath(dirname(__FILE__)).'/top_template.php');

if($arResult['NUM_PRODUCTS'] > 0)
{
	?><script>
	    $in_basket = <?=CUtil::PhpToJSObject($arResult["IN_BASKT"], false, true)?>;
	</script><?
}

if ($arParams["SHOW_PRODUCTS"] == 'Y' && $arResult['NUM_PRODUCTS'] > 0)
{
	?>
	<div class="bx-basket-item-list dropdown-menu dropdown-menu-right animated fadeIn">

		<div id="<?=$cartId?>products" class="panel bx-basket-item-list-container">
			<?foreach ($arResult["CATEGORIES"] as $category => $items):
			if (empty($items))
				continue;
			?>
			<div class="panel-heading bx-basket-item-list-item-status"><span class="bx-basket-item-list-item-status-text"><?=Loc::getMessage("TSB1_$category")?></span></div>
			<div class="panel-body">
				<div class="jScrollPane jScrollPaneMinicart">
					<?foreach ($items as $v):?>
					<div class="bx-basket-item-list-item">
						<div class="row">
							<div class="col-xs-3">
								<div class="bx-basket-item-list-item-img">
									<?if ($arParams["SHOW_IMAGE"] == "Y" && $v["PICTURE_SRC"]):?>
									<?if($v["DETAIL_PAGE_URL"]):?>
									<a href="<?=$v["DETAIL_PAGE_URL"]?>"><img class="img-thumbnail" src="<?=$v["PICTURE_SRC"]?>" alt="<?=htmlspecialcharsex($v["NAME"])?>"></a>
									<?else:?>
									<img class="img-thumbnail" src="<?=$v["PICTURE_SRC"]?>" alt="<?=$v["NAME"]?>" />
									<?endif?>
									<?endif?>
								</div>
							</div>
							<div class="col-xs-9">
								<div class="bx-basket-item-list-item-name">
									<?if ($v["DETAIL_PAGE_URL"]):?>
									<a href="<?=$v["DETAIL_PAGE_URL"]?>"><?=htmlspecialcharsex($v["NAME"])?></a>
									<?else:?>
									<?=$v["NAME"]?>
									<?endif?>
									<?if ($v["CAN_BUY"] == 'Y'):?>
									<div class="minicart-instock"><?=Loc::getMessage('IN_STOCK')?></div>
									<?endif?>
								</div>
								<?if (true):/*$category != "SUBSCRIBE") TODO */?>
								<div class="bx-basket-item-list-item-price-block">
									<?if ($arParams["SHOW_PRICE"] == "Y"):?>
									<div class="bx-basket-item-list-item-price"><strong><?=$v["QUANTITY"]?></strong>&nbsp;<?=$v["MEASURE_NAME"]?>&nbsp;x&nbsp;<strong><?=$v["PRICE_FMT"]?></strong></div>
									<?if ($v["FULL_PRICE"] != $v["PRICE_FMT"]):?>
									<div class="bx-basket-item-list-item-price-old"><?=$v["FULL_PRICE"]?></div>
									<?endif?>
									<?endif?>
								</div>
								<?php if(isset($v['PROPS']) && !empty($v['PROPS'])): ?>
								<ul class="bx-basket-item-list-item-prop list-unstyled">
									<?php foreach($v['PROPS'] as $prop): ?>
										<li>
											<span class="bx-basket-item-list-item-prop-name"><?php echo htmlspecialcharsex($prop['NAME']) ?></span><?
											?><span class="bx-basket-item-list-item-prop-value"><?php echo $prop['VALUE'] ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?endif?>
							<?endif?>
							<span href="#" class="bx-basket-remove" role="button" onclick="<?=$cartId?>.removeItemFromCart(<?=$v['ID']?>); return false;" title="<?=Loc::getMessage("TSB1_DELETE")?>"></span>
						</div>
					</div>
				</div>
				<?endforeach?>
			</div>
		</div>
		<?endforeach?>
		<div class="panel-footer">
			<?php 
			if($arParams["SHOW_PRODUCTS"] == 'Y') {
				if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y') { ?>
					<div class="bx-basket-total">
						<span class="bx-basket-total-name"><?php echo Loc::getMessage('TSB1_TOTAL') ?></span>
						<span class="bx-basket-total-value"><?php echo $arResult['TOTAL_PRICE']; ?></span>
					</div>
					<? }
				}
				?>
				<?if($arParams["PATH_TO_BASKET"] && $arResult["CATEGORIES"]["READY"]):?>
				<div class="btn-group btn-group-justified">
					<div class="btn-group">
						<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="btn btn-primary"><?=Loc::getMessage("TSB1_BASKET")?></a>
					</div>
				</div>
				<?endif?>
			</div>
		</div>
	</div>
	<?
} else if($arParams["SHOW_PRODUCTS"] == 'Y' && $arResult['NUM_PRODUCTS'] <= 0)
{?>
	<div class="bx-basket-item-list dropdown-menu dropdown-menu-right animated fadeIn">
		<div id="<?=$cartId?>products" class="panel bx-basket-item-list-container">
			<div class="panel-body _empty">
				<p><?php echo Loc::getMessage('TSB1_MINICART_EMPTY') ?></p>
			</div>
		</div>
	</div>
<?}