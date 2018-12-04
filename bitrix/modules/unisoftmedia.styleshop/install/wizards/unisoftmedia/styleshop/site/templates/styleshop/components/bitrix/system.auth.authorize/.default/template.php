<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */

use Bitrix\Main\Localization\Loc;
?>
<div class="bx-authform">

	<div class="row">
		<div class="col-sm-6">
			<h3><?php echo Loc::getMessage('FOR_REGISTERED') ?></h3>
			<p><?php echo Loc::getMessage('AUTH_PLEASE_AUTH') ?></p>
			<?if(!empty($arParams["~AUTH_RESULT"])):
				$text = str_replace(array("<br>", "<br />"), "\n", (isset($arParams["~AUTH_RESULT"]["MESSAGE"]))? $arParams["~AUTH_RESULT"]["MESSAGE"]:$arParams["~AUTH_RESULT"]);
			?>
				<div class="alert alert-danger"><?=nl2br(htmlspecialcharsbx($text))?></div>
			<?endif?>
			<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="AUTH" />
				<?if (strlen($arResult["BACKURL"]) > 0):?>
						<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
				<?endif?>
				<?foreach ($arResult["POST"] as $key => $value):?>
						<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
				<?endforeach?>
				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_LOGIN")?></label>
						<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
				</div>
				<div class="form-group">
					<label><?=Loc::getMessage("AUTH_PASSWORD")?></label>
					<?if($arResult["SECURE_AUTH"]):?>
									<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none"><div class="bx-authform-psw-protected-desc"><span></span><?echo Loc::getMessage("AUTH_SECURE_NOTE")?></div></div>

					<script type="text/javascript">
					document.getElementById('bx_auth_secure').style.display = '';
					</script>
					<?endif?>
					<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" />
				</div>

				<?if($arResult["CAPTCHA_CODE"]):?>
						<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />

						<div class="form-group">
							<label class="bx-authform-label-container"><?echo Loc::getMessage("AUTH_CAPTCHA_PROMT")?></label>
							<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></div>
							<input class="form-control" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
						</div>
				<?endif;?>

				<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
								<?=Loc::getMessage("AUTH_REMEMBER_ME")?>
							</label>
							<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
								<a class="pull-right" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
							<?endif?>
						</div>
					</div>
				<?endif?>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="Login" value="<?=Loc::getMessage("AUTH_AUTHORIZE")?>"><?=Loc::getMessage("AUTH_AUTHORIZE")?></button>
				</div>
			</form>
			<?if($arResult["AUTH_SERVICES"]):?>
			<?
			$APPLICATION->IncludeComponent("bitrix:socserv.auth.form",
				"flat",
				array(
					"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
					"AUTH_URL" => $arResult["AUTH_URL"],
					"POST" => $arResult["POST"],
				),
				$component,
				array("HIDE_ICONS"=>"Y")
			);
			?>

				<hr class="bxe-light">
			<?endif?>
		</div>
		<?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?>
			<div class="col-sm-6">
				<h3><?php echo Loc::getMessage('NEW_USER') ?></h3>
				<p><?php echo Loc::getMessage('AUTH_FIRST_ONE') ?></p>
				<a class="btn btn-primary" href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?php echo Loc::getMessage('AUTH_REGISTER') ?></a>
			</div>
		<?endif?>
	</div>
</div>
<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>

<?/*<div class="bx-authform col-lg-7">

<?
if(!empty($arParams["~AUTH_RESULT"])):
	$text = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
?>
	<div class="alert alert-danger"><?=nl2br(htmlspecialcharsbx($text))?></div>
<?endif?>

<?
if($arResult['ERROR_MESSAGE'] <> ''):
	$text = str_replace(array("<br>", "<br />"), "\n", $arResult['ERROR_MESSAGE']);
?>
	<div class="alert alert-danger"><?=nl2br(htmlspecialcharsbx($text))?></div>
<?endif?>

	<h3 class="bx-title"><?=GetMessage("AUTH_PLEASE_AUTH")?></h3>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form",
	"flat",
	array(
		"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
		"AUTH_URL" => $arResult["AUTH_URL"],
		"POST" => $arResult["POST"],
	),
	$component,
	array("HIDE_ICONS"=>"Y")
);
?>

	<hr class="bxe-light">
<?endif?>

	<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>

		<div class="form-group">
			<label><?=GetMessage("AUTH_LOGIN")?></label>
				<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
		</div>
		<div class="form-group">
			<label><?=GetMessage("AUTH_PASSWORD")?></label>
<?if($arResult["SECURE_AUTH"]):?>
				<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none"><div class="bx-authform-psw-protected-desc"><span></span><?echo GetMessage("AUTH_SECURE_NOTE")?></div></div>

<script type="text/javascript">
document.getElementById('bx_auth_secure').style.display = '';
</script>
<?endif?>
				<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" />
		</div>

<?if($arResult["CAPTCHA_CODE"]):?>
		<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />

		<div class="bx-authform-formgroup-container dbg_captha">
			<div class="bx-authform-label-container">
				<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>
			</div>
			<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></div>
			<div class="bx-authform-input-container">
				<input class="form-control" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
			</div>
		</div>
<?endif;?>

<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
		<div class="bx-authform-formgroup-container">
			<div class="checkbox">
				<label class="bx-filter-param-label">
					<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
					<span class="bx-filter-param-text"><?=GetMessage("AUTH_REMEMBER_ME")?></span>
				</label>
			</div>
		</div>
<?endif?>
		<div class="bx-authform-formgroup-container">
			<button type="submit" class="btn btn-primary" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>"><?=GetMessage("AUTH_AUTHORIZE")?></button>
		</div>
	</form>

<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
	<hr class="bxe-light">

	<noindex>
		<div class="bx-authform-link-container">
			<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></b></a>
		</div>
	</noindex>
<?endif?>

<?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?>
	<noindex>
		<div class="bx-authform-link-container">
			<?=GetMessage("AUTH_FIRST_ONE")?><br />
			<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_REGISTER")?></b></a>
		</div>
	</noindex>
<?endif?>

</div>

<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>*/?>

