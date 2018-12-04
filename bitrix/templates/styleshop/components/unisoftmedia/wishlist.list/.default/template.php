<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

use Bitrix\Main\Localization\Loc;

$wishlistId = "wishlist".$component->randString();
$arParams['wishlistId'] = $wishlistId;
?>
<ul class="nav nav-pills">
	<li class="favorites wishlist"<?if($arParams["SHOW_PRODUCTS"] == 'Y'): ?> data-hover="dropdown"<?endif?> id="<?=$wishlistId?>">
		<script>
			var <?=$wishlistId?> = new SmallWishlist;
		</script>
			<?
			$frame = $this->createFrame($wishlistId, false)->begin();

				require(realpath(dirname(__FILE__)).'/ajax_template.php');

			$frame->beginStub();

				require(realpath(dirname(__FILE__)).'/top_template.php');

			$frame->end();
			?>

	</li>
<script>
	<?=$wishlistId?>.siteId       = '<?=SITE_ID?>';
	<?=$wishlistId?>.wishlistId   = '<?=$wishlistId?>';
	<?=$wishlistId?>.ajaxPath     = '<?=$componentPath?>/ajax.php';
	<?=$wishlistId?>.templateName = '<?=$templateName?>';
	<?=$wishlistId?>.arParams     =  <?=CUtil::PhpToJSObject ($arParams)?>; // TODO \Bitrix\Main\Web\Json::encode
	<?=$wishlistId?>.activate();
</script>
</ul>