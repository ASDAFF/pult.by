<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
	if(!empty($arResult["STORES"])):?>
	<div class="dropdown-menu animated fadeIn">
		<div class="panel">
			<?if($arParams["MAIN_TITLE"] != ''):?>
				<h5 class="panel-heading"><?=$arParams["MAIN_TITLE"]?></h5>
			<?endif;?>
			<div class="panel-body">
				<?foreach($arResult["STORES"] as $pid => $arProperty):?>
					<div style="display: <? echo ($arParams['SHOW_EMPTY_STORE'] == 'N' && isset($arProperty['REAL_AMOUNT']) && $arProperty['REAL_AMOUNT'] <= 0 ? 'none' : ''); ?>;">
						<?if (isset($arProperty["TITLE"])):?>
							<a href="<?=$arProperty["URL"]?>"> <?=$arProperty["TITLE"]?></a><br />
						<?endif;?>
						<?if (isset($arProperty["IMAGE_ID"]) && !empty($arProperty["IMAGE_ID"])):?>
							<span class="schedule"><?=GetMessage('S_IMAGE')?> <?=CFile::ShowImage($arProperty["IMAGE_ID"], 200, 200, "border=0", "", true);?></span><br />
						<?endif;?>
						<?if (isset($arProperty["PHONE"])):?>
							<span class="tel"><?=GetMessage('S_PHONE')?> <?=$arProperty["PHONE"]?></span><br />
						<?endif;?>
						<?if (isset($arProperty["SCHEDULE"])):?>
							<span class="schedule"><?=GetMessage('S_SCHEDULE')?> <?=$arProperty["SCHEDULE"]?></span><br />
						<?endif;?>
						<?if (isset($arProperty["EMAIL"])):?>
							<span><?=GetMessage('S_EMAIL')?> <?=$arProperty["EMAIL"]?></span><br />
						<?endif;?>
						<?if (isset($arProperty["DESCRIPTION"])):?>
							<span><?=GetMessage('S_DESCRIPTION')?> <?=$arProperty["DESCRIPTION"]?></span><br />
						<?endif;?>
						<?if (isset($arProperty["COORDINATES"])):?>
							<span><?=GetMessage('S_COORDINATES')?> <?=$arProperty["COORDINATES"]["GPS_N"]?>, <?=$arProperty["COORDINATES"]["GPS_S"]?></span><br />
						<?endif;?>
						<?if ($arParams['SHOW_GENERAL_STORE_INFORMATION'] == "Y") :?>
							<?=GetMessage('BALANCE')?>:
						<?else:?>
							<?=GetMessage('S_AMOUNT')?>
						<?endif;?>
						<span class="balance" id="<?=$arResult['JS']['ID']?>_<?=$arProperty['ID']?>"><?=$arProperty["AMOUNT"]?></span><br />
						<?
						if (!empty($arProperty['USER_FIELDS']) && is_array($arProperty['USER_FIELDS']))
						{
							foreach ($arProperty['USER_FIELDS'] as $userField)
							{
								if (isset($userField['CONTENT']))
								{
									?><span><?=$userField['TITLE']?>: <?=$userField['CONTENT']?></span><br /><?
								}
							}
						}
						?>
					</div>
				<?endforeach;?>
				</div>
			</div>
		</div>
	<?endif;?>
<?if (isset($arResult["IS_SKU"]) && $arResult["IS_SKU"] == 1 && !$arParams['AJAX_QUICKVIEW']):?>
	<script type="text/javascript">
		var obStoreAmount = new JCCatalogStoreSKU(<? echo CUtil::PhpToJSObject($arResult['JS'], false, true, true); ?>);
	</script>
	<?
endif;?>