<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
global $DB;
$db_type=strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/classes/general/profile2.php"); 
?>
<form action="/bitrix/admin/mcart_xls_import.php" enctype="multipart/form-data" method="POST">
<?

$list_profile =  CMcartXlsProfile2::GetRows('main_profile', array('id', 'name'), array(), 0, 'name');

$arrProfile[-1] = GetMessage("NOT_LOAD_PROFILE");
	foreach ($list_profile as $prof)
		$arrProfile[$prof["id"]] = $prof["name"]."[".$prof["id"]."]";

//print "<pre>"; print_r($arrProfile); print "</pre>";	
?>
<p><input type="file" name='xls_file_name'></p>
</br>
<? if (count($arrProfile)>1):


?>
<h4><?=GetMessage("XLS_SELECT_PROFILE")?></h4>
			<select name = "xls_profile" >
				<?foreach ($arrProfile as $key=>$value):?>
				<option value="<?=$key?>"><?=$value?></option>
				<?endforeach?>
			</select>
			<input type="submit" name="del_prof" value="<?=GetMessage("MCART_DEL_PROFLE")?>">
<? endif; ?>			
<div style="display: block;height: 20px;"></div>
<input type="submit" name="next_step" value="<?=GetMessage("NEXT_STEP")?>">
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>