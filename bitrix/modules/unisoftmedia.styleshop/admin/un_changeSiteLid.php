<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

if(!check_bitrix_sessid())
	die();

use Bitrix\Main\Loader,
		Unisoftmedia\Styleshop\Theme,
		Bitrix\Main\Localization\Loc,
		Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

$site_lid = (isset($_REQUEST['site_lid']))? $_REQUEST['site_lid'] : 0;
$module_id = (isset($_REQUEST['module_id']))? $_REQUEST['module_id'] : '';

if(!Loader::includeModule($module_id))
	die();

$theme = new Theme($module_id);
$options = $theme->Option()->toArray();

$rsSites = CSite::GetByID($site_lid);
if(!$arSite = $rsSites->Fetch())
	die();

$folderTemplate = Application::getDocumentRoot() . $arSite['DIR'] . $options['folderTemplate'];

$templates = (file_exists($folderTemplate))?array_diff(scandir($folderTemplate), array('..', '.')) : array();
$templateSelected = $theme->Option()->get('template', '', $arSite['LID']);
	$trueTemplate = false;
	if(!empty($templates)) {
			foreach($templates as $template)
			{
				if(preg_match('/^[a-zA-Z]+[a-zA-Z0-9\s\_\-]+$/', $template)) {
					if($trueTemplate == false) {
						?><option value=""><?php echo Loc::getMessage('TEMPLATE_CREATE')?></option><?
					}
					$trueTemplate = true;
					?><option value="<?php echo $template ?>"<?php echo ($templateSelected == $template)? ' selected' : '' ?>><?php echo $template ?></option><?
				}
			}
	}
	if($trueTemplate == false) {
		?><option value=""><?php echo Loc::getMessage('NOT_TEMPLATE')?></option><?
	}

require($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin_after.php');