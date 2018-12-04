<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

?>
		<a class="dropdown-toggle"<?if($arParams["SHOW_PRODUCTS"] == 'Y'): ?> data-toggle="dropdown" role="button"<?endif?> href="<?=$arParams['PATH_TO_BASKET']?>"><span class="minicart-text"><?=Loc::getMessage('TSB1_CART2')?></span></a>
		<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
			<span class="minicart-num"><?=$arResult['NUM_PRODUCTS']?></span>
		<?endif?>
		<?if ($arParams['SHOW_TOTAL_PRICE'] == 'Y' && $arParams["SHOW_PRODUCTS"] != 'Y'):?>
		<br/>
			<?=Loc::getMessage('TSB1_TOTAL_PRICE')?>
			<?if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'):?>
			<strong><?=$arResult['TOTAL_PRICE']?></strong>
			<?endif?>
		<?endif?>
		<?if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
		<br>
		<a href="<?=$arParams['PATH_TO_PERSONAL']?>"><?=Loc::getMessage('TSB1_PERSONAL')?></a>
		<?endif?>