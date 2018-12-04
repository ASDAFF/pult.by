<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-4">
		<div class="sale-personal-account-wallet-container panel panel-default">
			<div class="sale-personal-account-wallet-title panel-heading">
				<?=Bitrix\Main\Localization\Loc::getMessage('SPA_BILL_AT')?>
				<?=$arResult["DATE"];?>
			</div>
			<div class="sale-personal-account-wallet-list-container panel-body">
				<div class="sale-personal-account-wallet-list">
					<?
					foreach($arResult["ACCOUNT_LIST"] as $accountValue)
					{
						?>
						<div class="sale-personal-account-wallet-list-item">
							<span class="sale-personal-account-wallet-sum h2 pull-right"><?=$accountValue['SUM']?></span>
							<span class="sale-personal-account-wallet-currency pull-left">
								<div class="sale-personal-account-wallet-currency-item"><?=$accountValue['CURRENCY']?></div>
								<div class="sale-personal-account-wallet-currency-item"><?=$accountValue["CURRENCY_FULL_NAME"]?></div>
							</span>
						</div>
						<?
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>