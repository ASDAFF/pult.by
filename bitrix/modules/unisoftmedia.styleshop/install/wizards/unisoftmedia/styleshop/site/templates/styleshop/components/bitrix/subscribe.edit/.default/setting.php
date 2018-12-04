<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

use Bitrix\Main\Localization\Loc;

//***********************************
//setting section
//***********************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
<?echo bitrix_sessid_post();?>
	<div class="row">
		<div class="col-xs-12">
			<h3><?echo Loc::getMessage("subscr_title_settings")?></h3>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
			<label><?echo Loc::getMessage("subscr_email")?><span class="starrequired">*</span></label>
			<input class="form-control" type="text" name="EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"];?>" size="30" maxlength="255" />
		</div>
		<div class="form-group"><h4><?echo Loc::getMessage("subscr_rub")?><span class="starrequired">*</span></h4>
			<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><?=$itemValue["NAME"]?>
					</label>
				</div>
			<?endforeach;?>
		</div>
		<div class="form-group">
			<h4><?echo Loc::getMessage("subscr_fmt")?></h4>
			<div class="radio">
				<label><input type="radio" name="FORMAT" value="text"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "text") echo " checked"?> /><?echo Loc::getMessage("subscr_text")?></label>&nbsp;/&nbsp;<label><input type="radio" name="FORMAT" value="html"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo " checked"?> />HTML</label>
			</div>
		</div>
		</div>
		<div class="col-sm-6">
			<p class="small"><?echo Loc::getMessage("subscr_settings_note1")?></p>
			<p class="small"><?echo Loc::getMessage("subscr_settings_note2")?></p>
		</div>
		<div class="col-xs-12">
			<button class="btn btn-primary" type="submit" name="Save" value="<?echo ($arResult["ID"] > 0? Loc::getMessage("subscr_upd"):Loc::getMessage("subscr_add"))?>"><?echo ($arResult["ID"] > 0? Loc::getMessage("subscr_upd"):Loc::getMessage("subscr_add"))?></button>
			<button class="btn btn-default" type="reset" value="<?echo Loc::getMessage("subscr_reset")?>" name="reset"><?echo Loc::getMessage("subscr_reset")?></button>
		</div>
	</div>
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<?if($_REQUEST["register"] == "YES"):?>
		<input type="hidden" name="register" value="YES" />
	<?endif;?>
	<?if($_REQUEST["authorize"]=="YES"):?>
		<input type="hidden" name="authorize" value="YES" />
	<?endif;?>
</form>
