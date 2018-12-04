<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

//one css for all system.auth.* forms
?>
<div class="bx-authform">
	<div class="row">
		<div class="col-sm-8 col-md-6">
		<?
		if(!empty($arParams["~AUTH_RESULT"])):
			$text = str_replace(array("<br>", "<br />"), "\n", (isset($arParams["~AUTH_RESULT"]["MESSAGE"]))? $arParams["~AUTH_RESULT"]["MESSAGE"]:$arParams["~AUTH_RESULT"] );
		?>
			<div class="alert <?=($arParams["~AUTH_RESULT"]["TYPE"] == "OK"? "alert-success":"alert-danger")?>"><?=nl2br(htmlspecialcharsbx($text))?></div>
		<?endif?>

		<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
			<div class="alert alert-success"><?echo Loc::getMessage("AUTH_EMAIL_SENT")?></div>
		<?else:?>

		<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
			<div class="alert alert-warning"><?echo Loc::getMessage("AUTH_EMAIL_WILL_BE_SENT")?></div>
		<?endif?>

		<noindex>
			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
		<?if($arResult["BACKURL"] <> ''):?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="REGISTRATION" />

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_NAME")?></label>
					<input class="form-control" type="text" name="USER_NAME" maxlength="255" value="<?=$arResult["USER_NAME"]?>" />
				</div>

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_LAST_NAME")?></label>
					<input class="form-control" type="text" name="USER_LAST_NAME" maxlength="255" value="<?=$arResult["USER_LAST_NAME"]?>" />
				</div>

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_LOGIN_MIN")?><span class="starrequired">*</span></label>
					<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["USER_LOGIN"]?>" />
				</div>

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_PASSWORD_REQ")?><span class="starrequired">*</span></label>
		<?if($arResult["SECURE_AUTH"]):?>
						<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none"><div class="bx-authform-psw-protected-desc"><span></span><?echo Loc::getMessage("AUTH_SECURE_NOTE")?></div></div>

		<script type="text/javascript">
		document.getElementById('bx_auth_secure').style.display = '';
		</script>
		<?endif?>
						<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult["USER_PASSWORD"]?>" autocomplete="off" />
				</div>

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_CONFIRM")?><span class="starrequired">*</span></label>
		<?if($arResult["SECURE_AUTH"]):?>
						<div class="bx-authform-psw-protected" id="bx_auth_secure_conf" style="display:none"><div class="bx-authform-psw-protected-desc"><span></span><?echo Loc::getMessage("AUTH_SECURE_NOTE")?></div></div>

		<script type="text/javascript">
		document.getElementById('bx_auth_secure_conf').style.display = '';
		</script>
		<?endif?>
						<input class="form-control" type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" autocomplete="off" />
				</div>

				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_EMAIL")?><?if($arResult["EMAIL_REQUIRED"]):?><span class="starrequired">*</span><?endif?></label>
					<input class="form-control" type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" />
				</div>

		<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
			<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>

				<div class="form-group">
					<label><?=$arUserField["EDIT_FORM_LABEL"]?><?if ($arUserField["MANDATORY"]=="Y"):?><span class="starrequired">*</span><?endif?></label>
		<?
		$APPLICATION->IncludeComponent(
			"bitrix:system.field.edit",
			$arUserField["USER_TYPE"]["USER_TYPE_ID"],
			array(
				"bVarsFromForm" => $arResult["bVarsFromForm"],
				"arUserField" => $arUserField,
				"form_name" => "bform"
			),
			null,
			array("HIDE_ICONS"=>"Y")
		);
		?>
				</div>

			<?endforeach;?>
		<?endif;?>
		<?if ($arResult["USE_CAPTCHA"] == "Y"):?>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />

				<div class="form-group">
					<label>
						<?=Loc::getMessage("CAPTCHA_REGF_PROMT")?><span class="starrequired">*</span>
					</label>
					<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></div>
					<input class="form-control" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off"/>
				</div>

		<?endif?>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="Register" value="<?=Loc::getMessage("AUTH_REGISTER")?>"><?=Loc::getMessage("AUTH_REGISTER")?></button>
				</div>

				<hr class="bxe-light">

				<div class="bx-authform-description-container">
					<?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?>
				</div>

				<div class="bx-authform-description-container">
					<span class="starrequired">*</span><?=Loc::getMessage("AUTH_REQ")?>
				</div>

				<div class="form-group">
					<a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_AUTH")?></a>
				</div>

			</form>
		</noindex>

		<script type="text/javascript">
		document.bform.USER_NAME.focus();
		</script>

		<?endif?>
		</div>
	</div>
</div>