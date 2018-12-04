<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$this->IncludeLangFile('template.php');
$bxajaxid = $component->getBxAjaxId();
$recallId = "recall".$component->getIdForm();
?>
	<?if(!empty($arResult["ERROR_MESSAGE"]))
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
			ShowError($v);
	}
	if(strlen($arResult["OK_MESSAGE"]) > 0):?>
		<p><font class="notetext"><?=$arResult["OK_MESSAGE"]?></font></p>
	<?endif;?>
	<?if(strlen($arResult["OK_MESSAGE"]) <= 0 || $arParams['POPUP_FORM'] != 'Y'):?>
		<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
			<?=bitrix_sessid_post()?>
			<?if($arParams['POPUP_FORM'] == 'Y'):?>
				<input type="hidden" name="form_popup" value="<?=$bxajaxid?>" />
			<?endif?>
			<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>" />
			<input type="hidden" name="action" value="recall" />
			<?if(in_array("NAME", $arParams['INCLUDE_FIELDS'])):?>
				<div class="form-group">
					<label>
						<?=Loc::getMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
					</label>
					<input class="form-control" type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
				</div>
			<?endif?>
			<?if(in_array("PHONE", $arParams['INCLUDE_FIELDS'])):?>
				<div class="form-group">
					<label>
						<?=Loc::getMessage("MFT_PHONE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
					</label>
					<input <?if($arParams['USE_MASK'] == 'Y'):?>data-mask="true" data-mask-value="<?php echo $arParams['MASK_PHONE'] ?>"<?endif?> class="form-control" type="text" name="user_phone" value="<?=$arResult["AUTHOR_PHONE"]?>">
				</div>
			<?endif?>
			<?if(in_array("EMAIL", $arParams['INCLUDE_FIELDS'])):?>
				<div class="form-group">
					<label>
						<?=Loc::getMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
					</label>
					<input class="form-control" type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
				</div>
			<?endif?>
			<?if(in_array("MESSAGE", $arParams['INCLUDE_FIELDS'])):?>
				<div class="form-group">
					<label>
						<?=Loc::getMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?> <span class="starrequired">*</span><?endif?>
					</label>
					<textarea class="form-control" name="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
				</div>
			<?endif?>
			<?if($arParams["USE_CAPTCHA"] == "Y"):?>
			<div class="form-group">
				<label><?=Loc::getMessage("MFT_CAPTCHA")?></label>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
				<div class="mf-text"><?=Loc::getMessage("MFT_CAPTCHA_CODE")?> <span class="starrequired">*</span></div>
				<input type="text" class="form-control" name="captcha_word" size="30" maxlength="50" value="">
			</div>
			<?endif;?>
			<?if ($arParams['USER_CONSENT'] === 'Y')
        {
            ?><div class="form-group">
							<?$APPLICATION->IncludeComponent(
								'bitrix:main.userconsent.request',
								'recall',
								array(
									'ID' => $arParams['USER_CONSENT_ID'],
									'IS_CHECKED' => $arParams['USER_CONSENT_IS_CHECKED'],
									'IS_LOADED' => $arParams['USER_CONSENT_IS_LOADED'],
									'AUTO_SAVE' => 'N',
									'SUBMIT_EVENT_NAME' => 'un-recall-send',
									'REPLACE' => array(
										'button_caption' => Loc::getMessage("MFT_SUBMIT"),
										'fields' => $arResult['USER_CONSENT_PROPERTY_DATA']
									)
								)
							);?>
						</div><?
        }?>
			<button class="btn btn-primary" type="submit" name="submit" <?if($arParams['POPUP_FORM'] == 'Y' || $arParams['MODE_AJAX'] == 'Y'):?>onclick="Recall.submit('<?php echo $recallId ?>', '<?php echo $arParams['USER_CONSENT'] ?>'); return false;"<?endif?> value="<?=Loc::getMessage("MFT_SUBMIT")?>"><?=Loc::getMessage("MFT_SUBMIT")?></button>
		</form>
	<?endif?>