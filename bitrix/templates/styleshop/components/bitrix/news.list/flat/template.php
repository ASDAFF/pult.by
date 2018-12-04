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
?>
<?if(!empty($arResult['DESCRIPTION'])):?>
	<?php echo $arResult['DESCRIPTION'] ?>
	<hr />
<?endif?>
<div class="bx-newslist">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="row">
	<div class="<?php echo strtolower($arParams['MODE_VIEW']) ?>">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<article class="bx-newslist-container <?if($arParams['MODE_VIEW'] == 'GRID'):?>col-xs-<?php echo $arParams['RESPONSIVE_ITEMS_XS'] ?> col-sm-<?php echo $arParams['RESPONSIVE_ITEMS_SM'] ?> col-md-<?php echo $arParams['RESPONSIVE_ITEMS_MD'] ?> col-lg-<?php echo $arParams['RESPONSIVE_ITEMS_LG'] ?><?else:?>col-xs-12<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="bx-newslist-block row">
					<figure class="bx-newslist-img <?if($arParams['MODE_VIEW'] == 'GRID'):?>col-xs-12<?else:?>col-xs-12 col-sm-5 col-md-5 col-lg-4<?endif?>">
						<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
							<?
							$arPic = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"],
								array(
									"width" => $arParams['MAX_WIDTH'],
									"height" => $arParams['MAX_HEIGHT']
								),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);
							?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
									src="<?=$arPic["src"]?>"
									width="<?=$arPic["width"]?>"
									height="<?=$arPic["height"]?>"
									alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
									title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
									/></a>
						<?else:?>
							<?
							$arPic = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"],
								array(
									"width" => $arParams['MAX_WIDTH'],
									"height" => $arParams['MAX_HEIGHT']
								),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);
							?>
							<img
								src="<?=$arPic["src"]?>"
								width="<?=$arPic["width"]?>"
								height="<?=$arPic["height"]?>"
								alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
								title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
								/>
						<?endif;?>
					</figure>
				<div class="<?if($arParams['MODE_VIEW'] == 'GRID'):?>col-xs-12<?else:?>col-xs-12 col-sm-7 col-md-7 col-lg-8<?endif?>">
				<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
					<h3 class="bx-newslist-title">
						<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
							<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
						<?else:?>
							<?echo $arItem["NAME"]?>
						<?endif;?>
					</h3>
				<?endif;?>
				<?if(isset($arItem['DISPLAY_PROPERTIES']['ACTION_FROM']) && isset($arItem['DISPLAY_PROPERTIES']['ACTION_TO'])):?>
				<p class="bx-newslist-action-date"><span style="<?=(isset($arItem['DISPLAY_PROPERTIES']['ACTION_COLOR']) && !empty($arItem['DISPLAY_PROPERTIES']['ACTION_COLOR']['VALUE']))? 'background-color:'.$arItem['DISPLAY_PROPERTIES']['ACTION_COLOR']['VALUE']: ''?>"><?
				if(!empty($arItem['DISPLAY_PROPERTIES']['ACTION_FROM']['VALUE']))
					echo $arItem['DISPLAY_PROPERTIES']['ACTION_FROM']['NAME'].' '.$arItem['DISPLAY_PROPERTIES']['ACTION_FROM']['VALUE'].' ';
				if(!empty($arItem['DISPLAY_PROPERTIES']['ACTION_TO']['VALUE']))
					echo $arItem['DISPLAY_PROPERTIES']['ACTION_TO']['NAME'].' '.$arItem['DISPLAY_PROPERTIES']['ACTION_TO']['VALUE'];
				?></span></p>
				<?endif;?>
				<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
					<p class="bx-newslist-content">
					<?echo $arItem["PREVIEW_TEXT"];?>
					</p>
				<?endif;?>
				<?foreach($arItem["FIELDS"] as $code=>$value):?>
					<?if($code == "SHOW_COUNTER"):?>
						<div class="bx-newslist-view"><i class="fa fa-eye"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
							<?=intval($value);?>
						</div>
					<?elseif(
						$value
						&& (
							$code == "SHOW_COUNTER_START"
							|| $code == "DATE_ACTIVE_FROM"
							|| $code == "ACTIVE_FROM"
							|| $code == "DATE_ACTIVE_TO"
							|| $code == "ACTIVE_TO"
							|| $code == "DATE_CREATE"
							|| $code == "TIMESTAMP_X"
						)
					):?>
						<?
						$value = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($value, CSite::GetDateFormat()));
						?>
						<div class="bx-newslist-date"><i class="fa fa-calendar-o"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
							<?=$value;?>
						</div>
					<?elseif($code == "TAGS" && $value):?>
						<div class="bx-newslist-tags"><i class="fa fa-tag"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
							<?=$value;?>
						</div>
					<?elseif(
						$value
						&& (
							$code == "CREATED_USER_NAME"
							|| $code == "USER_NAME"
						)
					):?>
						<div class="bx-newslist-author"><i class="fa fa-user"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
							<?=$value;?>
						</div>
					<?elseif ($value != ""):?>
						<div class="bx-newslist-other"><i class="fa"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
							<?=$value;?>
						</div>
					<?endif;?>
				<?endforeach;?>
				<div class="row">
				<?if($arParams["USE_RATING"]=="Y"):?>
					<div class="col-xs-7 text-right">
						<?$APPLICATION->IncludeComponent(
							"bitrix:iblock.vote",
							"flat",
							Array(
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"ELEMENT_ID" => $arItem["ID"],
								"MAX_VOTE" => $arParams["MAX_VOTE"],
								"VOTE_NAMES" => $arParams["VOTE_NAMES"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"DISPLAY_AS_RATING" => $arParams["DISPLAY_AS_RATING"],
								"SHOW_RATING" => "N",
							),
							$component
						);?>
					</div>
				<?endif?>
				</div>
				<div class="row">
					<div class="col-xs-5">
					<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
						<div class="bx-newslist-more"><a class="btn btn-primary btn-xs" href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo GetMessage("CT_BNL_GOTO_DETAIL")?></a></div>
					<?endif;?>
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
								"PAGE_URL" => $arItem["~DETAIL_PAGE_URL"],
								"PAGE_TITLE" => $arItem["~NAME"],
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
			</article>
		<?endforeach;?>
	</div>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
