<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Помощь");
?>

<div class="bx_page">
	<ul>
		<li>
			<a href="<?=SITE_DIR?>help/delivery/">Доставка по России и Зарубежью</a>
		</li>
		<li>
			<a href="<?=SITE_DIR?>help/payment/">Оплата</a>
		</li>
		<li>
			<a href="<?=SITE_DIR?>help/guaranty/">Гарантия</a>
		</li>
		<li>
			<a href="<?=SITE_DIR?>help/credit/">Кредит</a>
		</li>
	</ul>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>