<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show current authorization section
//*************************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
<?echo bitrix_sessid_post();?>
	<div class="row">
		<div class="col-xs-12">
			<h4><?echo GetMessage("subscr_title_auth")?></h4>
		</div>
		<div class="col-sm-6">
			<p><?echo GetMessage("adm_auth_user")?> <?echo htmlspecialcharsbx($USER->GetFormattedName(false));?> [<?echo htmlspecialcharsbx($USER->GetLogin())?>].</p>
		</div>
		<div class="col-sm-6">
			<p><?if($arResult["ID"]==0):?>
			<?echo GetMessage("subscr_auth_logout1")?> <a href="<?echo $arResult["FORM_ACTION"]?>?logout=YES&amp;sf_EMAIL=<?echo $arResult["REQUEST"]["EMAIL"]?><?echo $arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?echo GetMessage("adm_auth_logout")?></a><?echo GetMessage("subscr_auth_logout2")?><br />
		<?else:?>
			<?echo GetMessage("subscr_auth_logout3")?> <a href="<?echo $arResult["FORM_ACTION"]?>?logout=YES&amp;sf_EMAIL=<?echo $arResult["REQUEST"]["EMAIL"]?><?echo $arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?echo GetMessage("adm_auth_logout")?></a><?echo GetMessage("subscr_auth_logout4")?><br />
		<?endif;?></p>
		</div>
	</div>
</form>
