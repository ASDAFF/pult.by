<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show confirmation form
//*************************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="ID" value="<?echo $arResult["ID"];?>" />
	<div class="row">
		<div class="col-xs-12">
			<h3><?echo GetMessage("subscr_title_confirm")?></h3>
		</div>
		<div class="col-sm-6">
			<div class="form-group"><label><?echo GetMessage("subscr_conf_code")?></label><span class="starrequired">*</span>
				<input class="form-control" type="text" name="CONFIRM_CODE" value="<?echo $arResult["REQUEST"]["CONFIRM_CODE"];?>" size="20" />
			</div>
			<?echo GetMessage("subscr_conf_date")?>
			<p><?echo $arResult["SUBSCRIPTION"]["DATE_CONFIRM"];?></p>
		</div>
		<div class="col-sm-6">
			<p class="small"><?echo GetMessage("subscr_conf_note1")?> <a title="<?echo GetMessage("adm_send_code")?>" href="<?echo $arResult["FORM_ACTION"]?>?ID=<?echo $arResult["ID"]?>&amp;action=sendcode&amp;<?echo bitrix_sessid_get()?>"><?echo GetMessage("subscr_conf_note2")?></a>.</p>
		</div>
		<div class="col-sm-12">
			<button class="btn btn-primary" type="submit" name="confirm" value="<?echo GetMessage("subscr_conf_button")?>"><?echo GetMessage("subscr_conf_button")?></button>
		</div>
	</div>
</form>