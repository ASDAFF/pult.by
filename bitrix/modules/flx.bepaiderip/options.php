<?

use Bitrix\Main\Localization\Loc;

if( ! $USER->isAdmin() || ! \Bitrix\Main\Loader::includeModule("sale") ) return ;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$module_id = "flx.bepaiderip";

$all_options = array(
					"address_for_send" => Loc::getMessage("DEVTM_ERIP_DOMAIN_API_DESC"),
					"shop_id" => Loc::getMessage("DEVTM_ERIP_SHOP_ID_DESC"),
					"shop_key" => Loc::getMessage("DEVTM_ERIP_SHOP_KEY_DESC"),
					"expired_at" => Loc::getMessage("DEVTM_ERIP_EXPIRED_AT_DESC"),
					"notification_url" => Loc::getMessage("DEVTM_ERIP_NOTIFICATION_URL_DESC"),
					"service_number" => Loc::getMessage("DEVTM_ERIP_SERVICE_NUMBER_DESC"),
					"company_name" => Loc::getMessage("DEVTM_ERIP_COMPANY_NAME_DESC"),
					"sale_name" => Loc::getMessage("DEVTM_ERIP_SALE_NAME_DESC"),
					"path_to_service" => Loc::getMessage("DEVTM_ERIP_PATH_TO_SERVICE_DESC"),
					"service_info" => Loc::getMessage("DEVTM_ERIP_FOR_PAYER_DESC"),
					"receipt" => Loc::getMessage("DEVTM_ERIP_RECEIPT_PAYER_DESC"),
					"set_automatic" => Loc::getMessage("DEVTM_ERIP_SET_AUTOMATIC_DESC")
				);
$tabs = array(
			array(
				"DIV" => "edit1",
				"TAB" => Loc::getMessage("DEVTM_ERIP_TAB_NAME"),
				"ICON" => "erip-icon",
				"TITLE" => Loc::getMessage("DEVTM_ERIP_TAB_DESC")
			),
		);
		
$o_tab = new CAdminTabControl("EripTabControl", $tabs);

if( $REQUEST_METHOD == "POST" && strlen( $save . $reset ) > 0 && check_bitrix_sessid() )
{
	if( strlen($reset) > 0 )
	{
		foreach( $all_options as $name => $desc )
		{
			\Bitrix\Main\Config\Option::delete( $module_id, $name );
		}
	}
	else
	{
		foreach( $all_options as $name => $desc )
		{
			if($name == "set_automatic")	
				$_REQUEST["set_automatic"] = $_REQUEST["set_automatic"] == "Y" ? "Y" : "N";

			if( isset( $_REQUEST[$name] ) )
			{
				if($name == "expired_at")
					$_REQUEST[$name] = is_numeric($_REQUEST[$name]) && $_REQUEST[$name] >= 1 ? $_REQUEST[$name] : 1;

				\Bitrix\Main\Config\Option::set( $module_id, $name, $_REQUEST[$name] );
			}
		}
	}
	
	LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$o_tab->ActiveTabParam());
}

$o_tab->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?
$o_tab->BeginNextTab();
foreach( $all_options as $name => $desc ):
	$cur_opt_val = htmlspecialcharsbx(Bitrix\Main\Config\Option::get( $module_id, $name ));
	$name = htmlspecialcharsbx($name);
?>
	<tr>
		<td width="40%">
			<label for="<?echo $name?>"><?echo $desc?>:</label>
		</td>
		<td width="60%">
			<?if($name == "set_automatic"):?>
				<input type="checkbox" id="<?echo $name?>" <?if($cur_opt_val == "Y"):?>checked<?endif?> value="Y" name="<?echo $name?>">
			<?else:?>
				<input type="text" id="<?echo $name?>" value="<?echo $cur_opt_val?>" name="<?echo $name?>">
			<?endif?>
		</td>
	</tr>
<?endforeach?>
<?$o_tab->Buttons();?>
	<input type="submit" name="save" value="<?= Loc::getMessage("DEVTM_ERIP_SAVE_BTN_NAME")?>" title="<?= Loc::getMessage("DEVTM_ERIP_SAVE_BTN_NAME")?>" class="adm-btn-save">
	<input type="submit" name="reset" title="<?= Loc::getMessage("DEVTM_ERIP_RESET_BTN_NAME")?>" OnClick="return confirm('<?echo AddSlashes(Loc::getMessage("DEVTM_ERIP_RESTORE_WARNING"))?>')" value="<?= Loc::getMessage("DEVTM_ERIP_RESET_BTN_NAME")?>">
	<?=bitrix_sessid_post();?>
<?$o_tab->End();?>
</form>