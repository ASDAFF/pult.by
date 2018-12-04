<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

?>
<a class="favorite dropdown-toggle"<?if($arParams["SHOW_PRODUCTS"] == 'Y'): ?> data-toggle="dropdown" role="button"<?endif?> href="<?php echo $arParams['PATH_TO_WISHLIST'] ?>">
	<span><?php echo Loc::getMessage('favorites') ?></span>
</a>