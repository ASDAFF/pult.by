<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>

<div class="row">
	<div class="col-sm-8 col-md-7 col-lg-6">
	<?if(!empty($arResult["ERROR_MESSAGE"]))
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
			ShowError($v);
	}
	if(strlen($arResult["OK_MESSAGE"]) > 0)
	{
		?><p><font class="notetext"><?=$arResult["OK_MESSAGE"]?></font></p><?
	}
	?>

	<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
	<?=bitrix_sessid_post()?>
		<div class="form-group">
			<label>
				<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
			</label>
			<input class="form-control" type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
		</div>
		<div class="form-group">
			<label>
				<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
			</label>
			<input class="form-control" type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
		</div>

		<div class="form-group">
			<label>
				<?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
			</label>
			<textarea class="form-control" name="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
		</div>

		<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="form-group">
			<label><?=GetMessage("MFT_CAPTCHA")?></label>
			<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
			<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?> <span class="starrequired">*</span></div>
			<input type="text" name="captcha_word" size="30" maxlength="50" value="">
		</div>
		<?endif;?>
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<button class="btn btn-primary" type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>"><?=GetMessage("MFT_SUBMIT")?></button>
	</form>
	</div>
</div>