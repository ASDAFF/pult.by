<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
?>

<div class="bx-authform">
	<div class="row">
		<div class="col-sm-8 col-md-6">

		<?
		if(!empty($arParams["~AUTH_RESULT"])):
			$text = str_replace(array("<br>", "<br />"), "\n", (isset($arParams["~AUTH_RESULT"]["MESSAGE"]))? $arParams["~AUTH_RESULT"]["MESSAGE"]:$arParams["~AUTH_RESULT"]);
		?>
			<div class="alert <?=($arParams["~AUTH_RESULT"]["TYPE"] == "OK"? "alert-success":"alert-danger")?>"><?=nl2br(htmlspecialcharsbx($text))?></div>
		<?endif?>

			<h3 class="bx-title"><?=Loc::getMessage("AUTH_GET_CHECK_STRING")?></h3>

			<p class="bx-authform-content-container"><?=Loc::getMessage("AUTH_FORGOT_PASSWORD_1")?></p>

			<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<?if($arResult["BACKURL"] <> ''):?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="SEND_PWD">

				<div class="form-group">
					<label><?echo Loc::getMessage("AUTH_LOGIN_EMAIL")?></label>
					<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
					<input type="hidden" name="USER_EMAIL" />
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="send_account_info" value="<?=Loc::getMessage("AUTH_SEND")?>"><?=Loc::getMessage("AUTH_SEND")?></button>
				</div>

				<div class="form-group">
					<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><?=Loc::getMessage("AUTH_AUTH")?></a>
				</div>

			</form>

		</div>
	</div>
</div>

<script type="text/javascript">
document.bform.onsubmit = function(){document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;};
document.bform.USER_LOGIN.focus();
</script>
