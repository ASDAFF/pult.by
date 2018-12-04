<?
$moduleID = 'alexkova.megametatags';

if(!$USER->IsAdmin())
	return;

//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleID.'/config.php');
IncludeModuleLangFile(__FILE__);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("TAGS_OPTIONS"), "TITLE" => GetMessage("TAGS_OPTIONS_TITLE"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$h1Code = CBIblockMegaMetaTags::$proph1Code;
$titleCode = CBIblockMegaMetaTags::$propTitleCode;
$keywordsCode = CBIblockMegaMetaTags::$propKeyWordsCode;
$descriptionCode = CBIblockMegaMetaTags::$propDescriptionCode;

if ($REQUEST_METHOD=="GET" && $_REQUEST['RestoreDefaults'] == "Y" && check_bitrix_sessid()){
	COption::SetOptionString($moduleID, "SHOW_DEBUGGING_INFO", 1);
	COption::SetOptionString($moduleID, "PROPERTY_".$h1Code, 'h1');
	//COption::SetOptionString($moduleID, "PROPERTY_".$titleCode, 'title');
	COption::SetOptionString($moduleID, "PROPERTY_".$keywordsCode, 'keywords');
	COption::SetOptionString($moduleID, "PROPERTY_".$descriptionCode, 'description');
}
if($REQUEST_METHOD=="POST" && strlen($Apply)>0 && check_bitrix_sessid())
{
	if (strlen($SHOW_DEBUGGING_INFO) > 0){
		$DEBUG_INFO_4DB = 1;
	}		
	else{
		$DEBUG_INFO_4DB = NULL;
	}

	COption::SetOptionString($moduleID, "SHOW_DEBUGGING_INFO", $DEBUG_INFO_4DB);
	COption::SetOptionString($moduleID, "PROPERTY_".$h1Code, $TAGS_PROPERTY_H1);
	//COption::SetOptionString($moduleID, "PROPERTY_".$titleCode, $TAGS_PROPERTY_TITLE);
	COption::SetOptionString($moduleID, "PROPERTY_".$keywordsCode, $TAGS_PROPERTY_KEYWORDS);
	COption::SetOptionString($moduleID, "PROPERTY_".$descriptionCode, $TAGS_PROPERTY_DESCRIPTION);
	
	if($Apply == '' && $_REQUEST["back_url_settings"] <> '')
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
$str_SHOW_DEBUGGING_INFO = COption::GetOptionString($moduleID, "SHOW_DEBUGGING_INFO");

$str_prop_h1 = htmlspecialchars(COption::GetOptionString($moduleID, "PROPERTY_".$h1Code));
$str_prop_title = htmlspecialchars(COption::GetOptionString($moduleID, "PROPERTY_".$titleCode));
$str_prop_keywords = htmlspecialchars(COption::GetOptionString($moduleID, "PROPERTY_".$keywordsCode));
$str_prop_description = htmlspecialchars(COption::GetOptionString($moduleID, "PROPERTY_".$descriptionCode));
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
<?$tabControl->Begin();?>
<?$tabControl->BeginNextTab();?>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("DEBUGGING_INFO_TITLE")?></b></td>
	</tr>
	<tr>
		 <td width="40%"><?=GetMessage("SHOW_DEBUGGING_INFO")?>:</td>
		 <td width="60%">
			<input name="SHOW_DEBUGGING_INFO" type="checkbox" id="SHOW_DEBUGGING_INFO" <?=($str_SHOW_DEBUGGING_INFO) ? 'checked' : ''?>/>
		 </td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("PROPERTY_TYPE_TITLE")?></b></td>
	</tr>
	<tr>
		 <td width="40%"><?=GetMessage("TAGS_PROPERTY_".$h1Code)?>:</td>
		 <td width="60%">
			<input name="TAGS_PROPERTY_<?=$h1Code?>" type="text" value="<?=$str_prop_h1?>"/>
		 </td>
	</tr>
	<?/*<tr>
		 <td width="40%"><?=GetMessage("TAGS_PROPERTY_".$titleCode)?>:</td>
		 <td width="60%">
			<input name="TAGS_PROPERTY_<?=$titleCode?>" type="text" value="<?=$str_prop_title?>"/>
		 </td>
	</tr>*/?>
	<tr>
		 <td width="40%"><?=GetMessage("TAGS_PROPERTY_".$descriptionCode)?>:</td>
		 <td width="60%">
			<input name="TAGS_PROPERTY_<?=$descriptionCode?>" type="text" value="<?=$str_prop_description?>"/>
		 </td>
	</tr>
	<tr>
		 <td width="40%"><?=GetMessage("TAGS_PROPERTY_".$keywordsCode)?>:</td>
		 <td width="60%">
			<input name="TAGS_PROPERTY_<?=$keywordsCode?>" type="text" value="<?=$str_prop_keywords?>"/>
		 </td>
	</tr>
<?$tabControl->BeginNextTab();?>
<?$tabControl->Buttons();?>
<script type="text/javascript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?echo bitrix_sessid_get()?>";
}
</script>
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>" class="adm-btn-save">
	<input type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>