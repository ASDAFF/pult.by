<?

global $MESS;
IncludeModuleLangFile(__FILE__);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "mcart.xls";
CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

$ROWS_COUNT = COption::GetOptionInt("mcart.xls", "ROWS_COUNT", 50);


if($MOD_RIGHT>="Y" || $USER->IsAdmin()):

		

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && check_bitrix_sessid())
	{
	$ROWS_COUNT = $_POST['rows_count'];
	COption::SetOptionInt("mcart.xls", "ROWS_COUNT", $ROWS_COUNT);
	
	}
	
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_TAB_RIGHTS")),
	array("DIV" => "edit2", "TAB" => GetMessage("CS_SETTINGS"), "TITLE" => GetMessage("CS_DETAIL")),
	
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?>

<style>
#tblTYPES tr td 			{vertical-align: top;}
#tblTYPES .wd-quick-edit 	{display: none; width: 500px;}
#tblTYPES .wd-quick-view	{padding: 3px; border: 1px solid transparent; width:800px;}
#tblTYPES .wd-input-hover 	{background-color:#F8F8F8; border: 1px solid #bbbbbb; cursor: pointer;}
textarea { word-wrap: break-word; }
</style>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>" name="webdav_settings">
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

<?$tabControl->BeginNextTab();?>


<tr>
	<td ><?echo GetMessage('MCART_XLS_ROWS_COUNT')?></td>
	<td><input type="text" name="rows_count" id="ROWS_COUNT" value=<?=$ROWS_COUNT?>></td>
</tr>


<?$tabControl->Buttons();?>

<input type="submit" name="Update" <?if ($MOD_RIGHT<"W") echo "disabled" ?> value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="hidden" name="Update" value="Y">

<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>
