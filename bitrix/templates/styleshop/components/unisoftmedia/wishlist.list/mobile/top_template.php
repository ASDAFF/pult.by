<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

?>
<a class="favorite dropdown-toggle"<?if($arParams["SHOW_PRODUCTS"] == 'Y'): ?> data-toggle="dropdown" role="button"<?endif?> href="<?php echo $arParams['PATH_TO_WISHLIST'] ?>">
	<div>
		<span class="favorites__count wishlist-count"<?if($arResult['NUM_PRODUCTS'] <= 0):?> style="display: none;"<?endif?>>
			<span><?php echo $arResult['NUM_PRODUCTS'] ?></span>
		</span>
	</div>
	<span><?php echo Loc::getMessage('favorites') ?></span>
</a>