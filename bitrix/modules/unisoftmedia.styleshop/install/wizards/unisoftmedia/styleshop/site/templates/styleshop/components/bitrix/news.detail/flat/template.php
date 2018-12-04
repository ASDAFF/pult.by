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
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
$this->addExternalCss("/bitrix/css/main/font-awesome.css");
$this->addExternalCss($this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css');
CUtil::InitJSCore(array('fx'));
?>
<div class="bx-newsdetail">
	<div class="bx-newsdetail-block" id="<?echo $this->GetEditAreaId($arResult['ID'])?>">

	<?if($arParams["DISPLAY_PICTURE"]!="N"):?>
		<?if (is_array($arResult["DETAIL_PICTURE"])):?>
			<p class="bx-newsdetail-img">
				<img
					src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
					width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
					height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
					title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
					/>
			</p>
		<?endif;?>
	<?endif?>

	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3 class="bx-newsdetail-title"><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<?if(isset($arResult['DISPLAY_PROPERTIES']['ACTION_FROM']) && isset($arResult['DISPLAY_PROPERTIES']['ACTION_TO'])):?>
		<p class="bx-newslist-action-date"><span style="<?=(isset($arResult['DISPLAY_PROPERTIES']['ACTION_COLOR']) && !empty($arResult['DISPLAY_PROPERTIES']['ACTION_COLOR']['VALUE']))? 'background-color:'.$arResult['DISPLAY_PROPERTIES']['ACTION_COLOR']['VALUE']: ''?>"><?
		if(!empty($arResult['DISPLAY_PROPERTIES']['ACTION_FROM']['VALUE']))
			echo $arResult['DISPLAY_PROPERTIES']['ACTION_FROM']['NAME'].' '.$arResult['DISPLAY_PROPERTIES']['ACTION_FROM']['VALUE'].' ';
		if(!empty($arResult['DISPLAY_PROPERTIES']['ACTION_TO']['VALUE']))
			echo $arResult['DISPLAY_PROPERTIES']['ACTION_TO']['NAME'].' '.$arResult['DISPLAY_PROPERTIES']['ACTION_TO']['VALUE'];
		?></span></p>
		<?endif;?>
	<div class="bx-newsdetail-content">
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	</div>

	<?foreach($arResult["FIELDS"] as $code=>$value):?>
		<?if($code == "SHOW_COUNTER"):?>
			<div class="bx-newsdetail-view"><i class="fa fa-eye"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=intval($value);?>
			</div>
		<?elseif($code == "SHOW_COUNTER_START" && $value):?>
			<?
			$value = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($value, CSite::GetDateFormat()));
			?>
			<p class="bx-newsdetail-date"><i class="fa fa-calendar-o"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</p>
		<?elseif($code == "TAGS" && $value):?>
			<div class="bx-newsdetail-tags"><i class="fa fa-tag"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?elseif($code == "CREATED_USER_NAME"):?>
			<div class="bx-newsdetail-author"><i class="fa fa-user"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?elseif ($value != ""):?>
			<div class="bx-newsdetail-other"><i class="fa"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?endif;?>
	<?endforeach;?>

	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<p class="bx-newsdetail-date"><i class="fa fa-calendar-o"></i> <?echo $arResult["DISPLAY_ACTIVE_FROM"]?></p>
	<?endif?>
	<?if($arParams["USE_RATING"]=="Y"):?>
		<div class="bx-newsdetail-separator">|</div>
		<div class="bx-newsdetail-rating">
			<?$APPLICATION->IncludeComponent(
				"bitrix:iblock.vote",
				"flat",
				Array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ELEMENT_ID" => $arResult["ID"],
					"MAX_VOTE" => $arParams["MAX_VOTE"],
					"VOTE_NAMES" => $arParams["VOTE_NAMES"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"DISPLAY_AS_RATING" => $arParams["DISPLAY_AS_RATING"],
					"SHOW_RATING" => "Y",
				),
				$component
			);?>
		</div>
	<?endif?>

	<div class="row">
		<div class="col-xs-5">
		</div>
	<?
	if ($arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="col-xs-7 text-right">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", $arParams["SHARE_TEMPLATE"], array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
	</div>
	</div>
</div>
<script type="text/javascript">
	BX.ready(function() {
		var slider = new JCNewsSlider('<?=CUtil::JSEscape($this->GetEditAreaId($arResult['ID']));?>', {
			imagesContainerClassName: 'bx-newsdetail-slider-container',
			leftArrowClassName: 'bx-newsdetail-slider-arrow-container-left',
			rightArrowClassName: 'bx-newsdetail-slider-arrow-container-right',
			controlContainerClassName: 'bx-newsdetail-slider-control'
		});
	});
</script>
