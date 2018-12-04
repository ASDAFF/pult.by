<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$wishlistId = $arParams['wishlistId'];

$this->IncludeLangFile('template.php');

require(realpath(dirname(__FILE__)).'/top_template.php');

if($arResult['NUM_PRODUCTS'] > 0 ):?>
	<script>
		$in_favorites = <?=CUtil::PhpToJSObject($arResult['arID'], false, true)?>;
	</script>
<?endif?>

<?if($arParams["SHOW_PRODUCTS"] == 'Y' && $arResult['NUM_PRODUCTS'] > 0)
{ ?>
	<div class="wishlist-item-list dropdown-menu animated fadeIn">
		<div class="panel wishlist-item-list-container">
			<div class="panel-body">
				<div class="jScrollPane jScrollPaneMiniwishlist">
					<?foreach ($arResult["ITEMS"] as $items): ?>
						<div class="wishlist-item-list-item">
							<div class="row">
								<div class="col-xs-3">
									<div class="wishlist-item-list-item-img">
										<?php 
											if($items['PICTURE_SRC']) {?>
												<a href="<?=$items["DETAIL_PAGE_URL"]?>"><img class="img-thumbnail" src="<?=$items["PICTURE_SRC"]?>" alt="<?=htmlspecialcharsex($items["NAME"])?>"></a>
											<?}
										?>
									</div>
								</div>
								<div class="col-xs-9">
									<div class="wishlist-item-list-item-name">
										<a href="<?=$items["DETAIL_PAGE_URL"]?>"><?=htmlspecialcharsex($items["NAME"])?></a>
									</div>
									<span class="wishlist-remove" role="button" onclick="<?=$wishlistId?>.removeItemFromWishlist(<?=$items['ID']?>, this); return false;" title="<?=Loc::getMessage("TSB1_DELETE")?>"></span>
								</div>
							</div>
						</div>
					<?endforeach?>
				</div>
			</div>
			<div class="panel-footer">
				<div class="btn-group btn-group-justified">
					<div class="btn-group">
						<a href="<?=$arParams["PATH_TO_WISHLIST"]?>" class="btn btn-primary"><?=Loc::getMessage("TSB1_WISHLIST")?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?} else if($arParams["SHOW_PRODUCTS"] == 'Y' && $arResult['NUM_PRODUCTS'] <= 0)
{?>
	<div class="wishlist-item-list dropdown-menu animated fadeIn">
		<div class="panel wishlist-item-list-container">
			<div class="panel-body _empty">
				<p><?php echo Loc::getMessage('TSB1_WISHLIST_EMPTY') ?></p>
			</div>
		</div>
	</div>
<?} ?>

<span class="favorites__count wishlist-count hidden-xs">
	<span><?php echo $arResult['NUM_PRODUCTS'] ?></span>
</span>