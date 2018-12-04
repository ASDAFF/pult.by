<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
?>
<? $APPLICATION->AddHeadScript("http://code.jquery.com/jquery-2.1.0.min.js");?>
<?
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));
CJSCore::Init("jquery");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
global $DB;
$db_type=strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/classes/general/profile2.php"); 

?>


<script type="text/javascript">

function ShowInput()
	{
		$('.last_row_num').css("display","block");
	}
function ShowInputIblock()
{
	$('.visible_iblock').css("display","block");
}
function HideInput()
	{
		$('.last_row_num').css("display","none");
	}
function ShowSKU(chk)
{
if (chk.checked)
	$('.iblock_sku_id').css("display","block");
else
	$('.iblock_sku_id').css("display","none");

}
</script>
<?
$errMess = "";
$inputFileName =  $_FILES['xls_file_name']['tmp_name'];
if ($_REQUEST["del_prof"]):
?>	<form action="<?=$APPLICATION->GetCurPage()?>"  method="POST">
	<?echo GetMessage("MCART_DO_YOU_REALY_DEL_QUESTION")." id = ".$_REQUEST["xls_profile"]?>
	<?
		//LocalRedirect('mcart_xls_start.php');
	?>
	<input type="hidden" name="xls_profile" value="<?=$_REQUEST["xls_profile"]?>">
	<input type="submit" name="del_prof_real" value="<?=GetMessage("MCART_XLS_DEL_PROFILE_Y")?>">
	</form>
<? elseif ($_REQUEST["del_prof_real"]):
?>
	<?
	$prof_id = $_REQUEST["xls_profile"];
	CMcartXlsProfile2::DelRows("main_profile", array("id"=>$prof_id));
	CMcartXlsProfile2::DelRows("mcart_profile_property", array("profile_id"=>$prof_id));

	?>
	<?=GetMessage("MCART_PROFLE_DELETED")?>&nbsp;
	<a href="mcart_xls_start.php">OK</a>
	<?
		//LocalRedirect('mcart_xls_start.php');
	?>
	
	</form>	
<?else:?>	
	<?
	$_SESSION['ARR_REAL_PROFILE'] = array();
	if ($PROFILE_ID=$_REQUEST['xls_profile'])
	{
		if (intval($PROFILE_ID)>0)
		{
			$_SESSION['PROFILE_ID'] = $PROFILE_ID;
			$profile_data = CMcartXlsProfile2::GetRows('main_profile', array('worksheet', 'iblock_id', 'row_first', 'row_title', 'column_firsl', 'column_last', 'row_end_label', 'need_offer', 'section_id', 'section_id_new'), 
					array('id'=>$PROFILE_ID), 1);
			if (count($profile_data)>0)		
				$ARR_REAL_PROFILE = $profile_data[0]; 
			//print "<pre>"; print_r($profile_data[0]); print "</pre>";		
		}
	}

	if (empty($inputFileName))	
		$errMess =$errMess."</br>".GetMessage("XLS_NULL_FILE");
	if (empty($errMess))	
	{
	$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/';

	if (!is_dir($uploaddir))
		mkdir($uploaddir);
	$uploadfile = $uploaddir . basename($_FILES['xls_file_name']['name']);
	if (move_uploaded_file($_FILES['xls_file_name']['tmp_name'], $uploadfile))
		$inputFileName = $uploadfile;

	
	include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mcart.xls/classes/general/PHPExcel/IOFactory.php';
	$arrWorkSheets = array();	 
		 try {
		 
		 CModule::IncludeModule('mcart.xls');
		 $langCls = new CMcartXlsStrRef();
		 
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				
				COption::SetOptionString("mcart.xls", "format", $inputFileType);
				
				if ($inputFileType=='CSV')
				{
					/*
					if (ini_get('mbstring.func_overload') & 2){
						die(GetMessage("MCART_WRONG_FILE_FORMAT")."</br><a href = '/bitrix/admin/mcart_xls_start.php'>".GetMessage("STEP_BACK")."</a>");
					}
					*/
					
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheet_names = $objReader->listWorksheetInfo($inputFileName);					
					foreach ($worksheet_load as $ws_id=>$ws_name)
						$arrWorkSheets[$ws_id] =  $langCls->ConvertArrayCharset($ws_name, BP_EI_DIRECTION_IMPORT);
				}
				
				if ($inputFileType=='Excel2007'){
					
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheet_names = $objReader->listWorksheetNames($inputFileName);
					foreach ($worksheet_names as $ws_id=>$ws_name)
						$arrWorkSheets[$ws_id] =  $langCls->ConvertArrayCharset($ws_name, BP_EI_DIRECTION_IMPORT);
				}
				
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
	?>
	<form action="/bitrix/admin/mcart_xls_import_step_1.php"  method="POST">
	<?
	if($inputFileType=='Excel2007')		//Excel2007
	{
		$arrTypes = array();
		$db_iblock_type = CIBlockType::GetList(/*array('id'=>'asc')*/);
			while($ar_iblock_type = $db_iblock_type->Fetch())
			{
			   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
			   {
				  $arrTypes[$ar_iblock_type['ID']] = htmlspecialcharsEx($arIBType["NAME"]);
			   }   
			   else
				$arrTypes[$ar_iblock_type['ID']] = $ar_iblock_type['ID'];
			}
		
		$arIBlocks=Array();
		$arIBlocks["0"] = "";
		$db_iblock = CIBlock::GetList(Array("iblock_type"=>"asc"), Array());
		while($arRes = $db_iblock->GetNext())
			$arIBlocks[] = array('name'=>$arRes["NAME"], 'id'=>$arRes["ID"], 'type'=>$arRes['IBLOCK_TYPE_ID']);
		
		$arIBlocksSKU=Array();
		$arIBlocks["0"] = array('id'=>0, 'name'=>'');
		$db_iblock = CIBlock::GetList(Array("NAME"=>"ASC"), Array("TYPE"=>"offers"));
		while($arRes = $db_iblock->GetNext())
			$arIBlocksSKU[$arRes["ID"]] = $arRes["NAME"];
		?>
		<h4><?=GetMessage("XLS_SELECT_CURRENT_SHEET")?></h4>
					<select name = "xls_shett_index" >
						<?foreach ($arrWorkSheets as $key=>$value):?>
						<option value="<?=$key?>" <?if (isset($ARR_REAL_PROFILE['worksheet'])&&($ARR_REAL_PROFILE['worksheet']==$key)) echo " selected"?>><?=$value?></option>
						<?endforeach?>
					</select>
		</br>
					<h4><?=GetMessage("XLS_SELECT_IBLOCK")?></h4>
					<select name = "xls_iblock_id" >
						<?foreach ($arIBlocks as $key=>$value):?>
						<?if (($key>0)&&($value['type']!=$typestypes)) :
						$typestypes = $value['type'];
						?>
							<option value="type" disabled ><b><?=$arrTypes[$typestypes]?></b></option>
						<?  endif;?>
						<option value="<?=$value['id']?>" <?if (isset($ARR_REAL_PROFILE['iblock_id'])&&($ARR_REAL_PROFILE['iblock_id']==$value['id'])) echo " selected"?>><?="..".$value['name']?></option>
						<?endforeach?>
					</select>
		</br>
		<h4><?=GetMessage("MCART_TITLE_XLS_ROW")?></h4>
		<input type="text" name="title_xls_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['row_title'])) echo $ARR_REAL_PROFILE['row_title']?>"/>
		</br>
		<h4><?=GetMessage("MCART_FIRST_ROW")?></h4>
		<input type="text" name="first_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['row_first'])) echo $ARR_REAL_PROFILE['row_first']?>"/>
		</br>
		<h4><?=GetMessage("MCART_ROWS_END")?></h4>
			<input type="radio" name="rows_end_label" class="nonumber" value="auto" onclick="HideInput()" checked><?=GetMessage('XLS_MCART_AUTO')?></br>
			<input type="radio" name="rows_end_label" class="nonumber" value="keyword"  onclick="HideInput()" disabled><?=GetMessage('XLS_MCART_WORD1')?></br>
			<input type="radio" name="rows_end_label" class="lastrownumber" value="lastrownumber" onclick="ShowInput()" ><?=GetMessage('XLS_MCART_ROW_END_NUMBER')?>
				<input type="text" class="last_row_num" name="last_row_num" style="display: none" size="2"/></br>
			<input type="radio" name="rows_end_label" class="nonumber" value="emptyinrow"  onclick="HideInput()" disabled><?=GetMessage('XLS_MCART_ROW_EEMPTY')?></br>
		</br>
		<input type="hidden" name = "xls_file_name" value='<?=$inputFileName?>'>
		<h4><?=GetMessage("MCART_COLUMN_DIAPAZONE")?></h4>
		<input type="text" name="column_a" size="3" value="<?if (isset($ARR_REAL_PROFILE['column_firsl'])) echo $ARR_REAL_PROFILE['column_firsl']?>"/>
		-
		<input type="text" name="column_b" size="3" value="<?if (isset($ARR_REAL_PROFILE['column_last'])) echo $ARR_REAL_PROFILE['column_last']?>"/>		
		</br>

		<?
		$MCART_IS_SKU = false;
			// определение базовой цены	
			if ((CModule::IncludeModule('catalog'))&&(CModule::IncludeModule('sale'))):
			$MCART_IS_SKU = true;
		?>	
		
		<h4><input type="checkbox" class="add_sku"  name="add_sku" value="Y" <? if ($ARR_REAL_PROFILE['need_offer']):?> checked <? endif;?>onclick="ShowSKU(this)">
		<?=GetMessage("XLS_SELECT_SKU_IBLOCK")?></h4>
		</br>
		<div class = "iblock_sku_id"  style="display:none" >
		<?=GetMessage("IBLOCK_SKU_TITLE")?>
		</br>
		<select name = "iblock_sku_id" class="sku_detail">
			<?foreach ($arIBlocks as $key=>$value):?>
			<option value="<?=$key?>"><?=$value?></option>
			<?endforeach?>
		</select>
		</br>
		<?=GetMessage("CML2_LINK_ASC")?><input type="text" name="cml2_link_code" value="<?if (isset($ARR_REAL_PROFILE['cml2_link_code'])) echo $ARR_REAL_PROFILE['cml2_link_code']?>" >
		</br>
		</br>
		</div>
		</br>
		<?endif;?>
	<?}?>
	
	<?if($inputFileType=='CSV')			//CSV
	{
		$arrTypes = array();
		$db_iblock_type = CIBlockType::GetList(/*array('id'=>'asc')*/);
			while($ar_iblock_type = $db_iblock_type->Fetch())
			{
			   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
			   {
				  $arrTypes[$ar_iblock_type['ID']] = htmlspecialcharsEx($arIBType["NAME"]);
			   }   
			   else
				$arrTypes[$ar_iblock_type['ID']] = $ar_iblock_type['ID'];
			}
		
		$arIBlocks=Array();
		$arIBlocks["0"] = "";
		$db_iblock = CIBlock::GetList(Array("iblock_type"=>"asc"), Array());
		while($arRes = $db_iblock->GetNext())
			$arIBlocks[] = array('name'=>$arRes["NAME"], 'id'=>$arRes["ID"], 'type'=>$arRes['IBLOCK_TYPE_ID']);
		
		$arIBlocksSKU=Array();
		$arIBlocks["0"] = array('id'=>0, 'name'=>'');
		$db_iblock = CIBlock::GetList(Array("NAME"=>"ASC"), Array("TYPE"=>"offers"));
		while($arRes = $db_iblock->GetNext())
			$arIBlocksSKU[$arRes["ID"]] = $arRes["NAME"];
	?>
		<h4><?=GetMessage("XLS_SELECT_INPUT")?></h4>
					<select name = "delimiter" >
						<option value=";"><b>;</b></option>
						<option value="."><b>.</b></option>
						<option value=","><b>,</b></option>
					</select>
		</br>
		<h4><?=GetMessage("XLS_SELECT_CRM")?></h4>
		<?if(CModule::IncludeModule('crm')){?>
			<input type="radio" name="xls_crm" class="nonumber" value="auto" onclick="HideInput()" checked><?=GetMessage("XLS_SELECT_CRM_CRM")?></br>
		<?}?>
		<?if(CModule::IncludeModule('iblock')){?>
			<input type="radio" name="xls_crm" class="lastrownumber" value="lastrownumber" onclick="ShowInput()" ><?=GetMessage("XLS_SELECT_CRM_IBLOCK")?></br>
					<div class="last_row_num" name="last_row_num" style="display: none">
					</br>
						<select name = "xls_iblock_id" >
							<?foreach ($arIBlocks as $key=>$value):?>
							<?if (($key>0)&&($value['type']!=$typestypes)) :
							$typestypes = $value['type'];
							?>
								<option value="type" disabled ><b><?=$arrTypes[$typestypes]?></b></option>
							<?  endif;?>
							<option value="<?=$value['id']?>" <?if (isset($ARR_REAL_PROFILE['iblock_id'])&&($ARR_REAL_PROFILE['iblock_id']==$value['id'])) echo " selected"?>><?="..".$value['name']?></option>
							<?endforeach?>
						</select>
					</div>
		<?}?>
		<h4><?=GetMessage("MCART_TITLE_XLS_ROW")?></h4>
		<input type="text" name="title_xls_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['row_title'])) echo $ARR_REAL_PROFILE['row_title']?>"/>
		</br>
		<h4><?=GetMessage("MCART_FIRST_ROW")?></h4>
		<input type="text" name="first_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['row_first'])) echo $ARR_REAL_PROFILE['row_first']?>"/>
		</br>
		<h4><?=GetMessage("MCART_ROWS_END")?></h4>
			<?=GetMessage('XLS_MCART_ROW_END_NUMBER')?></br>
				</br><input type="text"  name="last_row_num" size="2"/>
				
		<input type="hidden" name = "xls_file_name" value='<?=$inputFileName?>'>
		<h4><?=GetMessage("MCART_COLUMN_DIAPAZONE")?></h4>
		<input type="text" name="column_a" size="3" value="<?if (isset($ARR_REAL_PROFILE['column_firsl'])) echo $ARR_REAL_PROFILE['column_firsl']?>"/>
		-
		<input type="text" name="column_b" size="3" value="<?if (isset($ARR_REAL_PROFILE['column_last'])) echo $ARR_REAL_PROFILE['column_last']?>"/>		
		<div style="display: block;height: 20px;"></div>
	<?}?>
	
	<a href = "/bitrix/admin/mcart_xls_start.php"><?=GetMessage("STEP_BACK")?></a>
	<?if(CModule::IncludeModule('iblock') || CModule::IncludeModule('crm')){?>
	<input type="submit" name="next_step" value="<?=GetMessage("NEXT_STEP")?>">
	<?}?>
	</form>
	<?}
	else
	{
	echo $errMess;?>
	<br>
	<a href = "/bitrix/admin/mcart_xls_start.php"><?=GetMessage("STEP_BACK")?></a>
	<?
	}
	?>
<?endif;?>	
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>