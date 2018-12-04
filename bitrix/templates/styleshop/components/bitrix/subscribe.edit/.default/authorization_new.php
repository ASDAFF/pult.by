<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult["ALLOW_ANONYMOUS"]=="Y" && $_REQUEST["authorize"]<>"YES" && $_REQUEST["register"]<>"YES"):?>
	<div class="row">
		<div class="col-xs-12">
			<h3><?echo GetMessage("subscr_title_auth2")?></h3>
		</div>
		<div class="col-sm-6">
			<p><?echo GetMessage("adm_auth1")?> <a href="<?echo SITE_DIR.'auth/?backurl='.$arResult["FORM_ACTION"]?>"><?echo GetMessage("adm_auth2")?></a>.</p>
			<?if($arResult["ALLOW_REGISTER"]=="Y"):?>
				<p><?echo GetMessage("adm_reg1")?> <a href="<?echo SITE_DIR.'auth?register=yes&backurl='.$arResult["FORM_ACTION"]?>"><?echo GetMessage("adm_reg2")?></a>.</p>
			<?endif;?>
		</div>
		<div class="col-sm-6">
			<p class="small"><?echo GetMessage("adm_reg_text")?></p>
		</div>
	</div>
<?elseif($arResult["ALLOW_ANONYMOUS"]=="N" || $_REQUEST["authorize"]=="YES" || $_REQUEST["register"]=="YES"):?>
	<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
	<?echo bitrix_sessid_post();?>
	<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<input type="hidden" name="RUB_ID[]" value="<?=$itemValue["ID"]?>">
	<?endforeach;?>
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<?if($_REQUEST["register"] == "YES"):?>
		<input type="hidden" name="register" value="YES" />
	<?endif;?>
	<?if($_REQUEST["authorize"]=="YES"):?>
		<input type="hidden" name="authorize" value="YES" />
	<?endif;?>
		<div class="row">
			<div class="col-xs-12">
				<h3><?echo GetMessage("adm_auth_exist")?></h3>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label><?echo GetMessage("adm_auth_login")?><span class="starrequired">*</span></label>
					<input class="form-control" type="text" name="LOGIN" value="<?echo $arResult["REQUEST"]["LOGIN"]?>" size="20" />
				</div>
				<div class="form-group">
					<label><?echo GetMessage("adm_auth_pass")?><span class="starrequired">*</span></label>
					<input class="form-control" type="password" name="PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["PASSWORD"]?>" />
				</div>
			</div>
			<div class="col-sm-6">
				<p class="small">
					<?if($arResult["ALLOW_ANONYMOUS"]=="Y"):?>
						<?echo GetMessage("subscr_auth_note")?>
					<?else:?>
						<?echo GetMessage("adm_must_auth")?>
					<?endif;?>
				</p>
			</div>
			<div class="col-xs-12">
				<button class="btn btn-primary" type="submit" name="Save" value="<?echo GetMessage("adm_auth_butt")?>"><?echo GetMessage("adm_auth_butt")?></button>
			</div>
		</div>
	</form>
	<?if($arResult["ALLOW_REGISTER"]=="Y"):?>
		<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
			<?echo bitrix_sessid_post();?>
			<div class="row">
				<div class="col-xs-12">
					<h3><?echo GetMessage("adm_reg_new")?></h3>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?echo GetMessage("adm_reg_login")?><span class="starrequired">*</span></label>
						<input class="form-control" type="text" name="NEW_LOGIN" value="<?echo $arResult["REQUEST"]["NEW_LOGIN"]?>" size="20" />
					</div>
					<div class="form-group">
						<label><?echo GetMessage("adm_reg_pass")?><span class="starrequired">*</span></label>
						<input class="form-control" type="password" name="NEW_PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["NEW_PASSWORD"]?>" />
					</div>
					<div class="form-group">
						<label><?echo GetMessage("adm_reg_pass_conf")?><span class="starrequired">*</span></label>
						<input class="form-control" type="password" name="CONFIRM_PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["CONFIRM_PASSWORD"]?>" />
					</div>
					<div class="form-group">
						<label><?echo GetMessage("subscr_email")?><span class="starrequired">*</span></label>
						<input class="form-control" type="text" name="EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"];?>" size="30" maxlength="255" />
					</div>

					<?/* CAPTCHA */
					if (COption::GetOptionString("main", "captcha_registration", "N") == "Y"):
						$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
					?>
						<div class="form-group">
							<h4><?=GetMessage("subscr_CAPTCHA_REGF_TITLE")?><h4>
							<input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx($capCode) ?>" />
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($capCode) ?>" width="180" height="40" alt="CAPTCHA" />
						</div>
						<div class="form-group">
							<label><?=GetMessage("subscr_CAPTCHA_REGF_PROMT")?><span class="starrequired">*</span></label>
							<input class="form-control" type="text" name="captcha_word" size="30" maxlength="50" value="" />
						</div>
					<?endif;?>
				</div>
				<div class="col-sm-6">
					<p class="small">
						<?if($arResult["ALLOW_ANONYMOUS"]=="Y"):?>
							<?echo GetMessage("subscr_auth_note")?>
						<?else:?>
							<?echo GetMessage("adm_must_auth")?>
						<?endif;?>
					</p>
				</div>
				<div class="col-xs-12">
					<button class="btn btn-primary" type="submit" name="Save" value="<?echo GetMessage("adm_reg_butt")?>"><?echo GetMessage("adm_reg_butt")?></button>
				</div>
			</div>
		</form>
	<?endif?>
<?endif; //$arResult["ALLOW_ANONYMOUS"]=="Y" && $authorize<>"YES"?>
