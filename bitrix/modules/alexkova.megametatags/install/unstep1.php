<form action="<?echo $APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
    <?echo CAdminMessage::ShowMessage(GetMessage("TAGS_IB_UNINST_WARN"))?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="alexkova.megametatags">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
        <p><input type="checkbox" name="del_IB" id="del_IB"><label for="del_IB"><?echo GetMessage("DELETE_TABLES_TAGS")?></label></p>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_HISTORY_BACK")?>">
<form>