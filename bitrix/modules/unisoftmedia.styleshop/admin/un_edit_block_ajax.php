<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

$module_id = 'unisoftmedia.styleshop';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Unisoftmedia\Styleshop\Theme;

if(!Loader::includeModule($module_id))
	die();

$theme = new Theme($module_id);

Loc::loadMessages(__FILE__);

if($_SERVER["REQUEST_METHOD"] == 'POST' && $_REQUEST['save'] == 'Y') {

	if(!check_bitrix_sessid())
		die();

	if(isset($_REQUEST['section']) && is_array($_REQUEST['section']) && !empty($_REQUEST['section']) && isset($_REQUEST['template']) && !empty($_REQUEST['template'])) {
		$section = $_REQUEST['section'];
		$template = $_REQUEST['template'];
		$site_lid = (isset($_REQUEST['site_lid']))? $_REQUEST['site_lid'] : '';

		$rsSites = CSite::GetByID($site_lid);
		if(!$arSite = $rsSites->Fetch())
			die();

		$arOptionsSection = $theme->Option()->get("section_{$template}", '', $site_lid);

		if(!empty($arOptionsSection))
				$arOptionsSection = unserialize($arOptionsSection);

		$arOptionsSection[key($section)] = array_diff($section[key($section)], array(''));
		$theme->Option()->set("section_{$template}", (!empty($arOptionsSection))? serialize($arOptionsSection) : '', $site_lid);
	}

} else if(isset($_REQUEST['ncol']) && 0 < intval($_REQUEST['ncol']) && isset($_REQUEST['template'])) {
	$ncol = intval($_REQUEST['ncol']);
	$template = $_REQUEST['template'];
	$site_lid = $_REQUEST['site_lid'];
	$section = $theme->Option()->get("section_{$template}", '', $site_lid);
	if(!empty($section))
		$section = unserialize($section);
	?>
	<form action="/bitrix/admin/un_edit_block_ajax.php" name="edit_form_block" method="post" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="template" value="<?php echo $template ?>" />
		<input type="hidden" name="site_lid" value="<?php echo $site_lid ?>" />
		<input type="hidden" name="save" value="Y" />

		<table class="adm-detail-content-table edit-table">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('BG')?></td>
					<td width="60%" class="adm-detail-content-cell-r"><?
						?><input name="section[<?php echo $ncol ?>][bg_color]" type="text" value="<?php echo (isset($section[$ncol]['bg_color'])? $section[$ncol]['bg_color'] : '' ) ?>" /><?
						?><div class="scolorpickerHolder" value="<?php echo $section[$ncol]['bg_color'] ?>"></div><?
					?></td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('IMAGE')?></td>
					<td width="60%" class="adm-detail-content-cell-r"><?
						?><input name="section[<?php echo $ncol ?>][bg_image]" type="text" value="<?php echo (isset($section[$ncol]['bg_image'])? $section[$ncol]['bg_image'] : '' ) ?>" /><?
					?></td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('ADDCLASS')?></td>
					<td width="60%" class="adm-detail-content-cell-r"><?
						?><input name="section[<?php echo $ncol ?>][add_class]" type="text" value="<?php echo (isset($section[$ncol]['add_class'])? $section[$ncol]['add_class'] : '' ) ?>" /><?
					?></td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('ADDSTYLE')?></td>
					<td width="60%" class="adm-detail-content-cell-r"><?
						?><input name="section[<?php echo $ncol ?>][add_style]" type="text" value="<?php echo (isset($section[$ncol]['add_style'])? $section[$ncol]['add_style'] : '' ) ?>" /><?
					?></td>
				</tr>
			</tbody>
		</table>
	</form>
	<script type="text/javascript">
	$('.scolorpickerHolder').colorPicker();
	</script>
	<?
}
require($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin_after.php');