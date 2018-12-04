<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

if(!check_bitrix_sessid())
	die();

$module_id = 'unisoftmedia.styleshop';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application; 
use Bitrix\Main\IO\Directory; 
use Bitrix\Main\IO\File;
use Unisoftmedia\Styleshop\Theme;
use Unisoftmedia\Styleshop\Library\SimpleHtml;

if(!Loader::includeModule($module_id))
	die();

Loc::loadMessages(__FILE__);

$theme = new Theme($module_id);
$options = $theme->Option()->toArray();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'Y') {

	$site_lid = (isset($_REQUEST['site_lid']))? $_REQUEST['site_lid'] : 0;
	$template = (isset($_REQUEST['template']))? $_REQUEST['template'] : '';

	if(!empty($template) && !preg_match('/^[a-zA-Z]+[a-zA-Z0-9\s\_\-]+$/', $template) || empty($template)) {
		die();
	}
	$template = str_replace('/', '', $template);

	$rsSites = CSite::GetByID($site_lid);
	if(!$arSite = $rsSites->Fetch())
		die();
	
	$folderTemplate = Application::getDocumentRoot() . $arSite['DIR'] . $options['folderTemplate'] . '/' . $template . '/';
	$theme->Option()->delete(array(
		"name" => "section_{$template}",
		"site_id" => $site_lid
	));
	Directory::deleteDirectory($folderTemplate);

} else if(isset($_REQUEST['template']) && !empty($_REQUEST['template']) && preg_match('/^[a-zA-Z]+[a-zA-Z0-9\s\_\-]+$/', $_REQUEST['template'])) {

	$template = $_REQUEST['template'];
	$site_lid = $_REQUEST['site_lid'];
	$col_size = (isset($_REQUEST['col_size']) && !empty($_REQUEST['col_size']))? intval($_REQUEST['col_size']) : 12;

	$template = str_replace('/', '', $template);

	$rsSites = CSite::GetByID($site_lid);
	if(!$arSite = $rsSites->Fetch())
		die;

if(file_exists(Application::getDocumentRoot() . $arSite['DIR'] . $options['folderTemplate'] . '/' . $template . '/index.php'))
	$fileTemplates = file_get_contents(Application::getDocumentRoot() . $arSite['DIR'] . $options['folderTemplate'] . '/' . $template . '/index.php');

	if($fileTemplates) {
		$arGrid = array();
		$html = new SimpleHtml($fileTemplates);
		foreach($html->dom()->find('[data-grid]') as $k => $element) {
			if($element->attr['data-grid']) {
				$arGrid[] = json_decode(str_replace('&quot;', '"', $element->attr['data-grid']), true);
			}
		}
		$html->dom()->clear();
	}

	function sizeColumn($col_lg, $grid = 12)
	{
		$width = '100';
		if($col_lg > 0) {
			$columnCount = $grid / $col_lg;
			$width = 100 / $columnCount;
		}
		return $width;
	}

	if(!empty($arGrid)) {
		ob_start();
		foreach($arGrid as $k => $arItem):?><?

			?><?if ($previousLevel && $arItem['depth_level'] < $previousLevel):?><?
				?><?php echo str_repeat('</div></div>', ($previousLevel - ($arItem['depth_level'])));?><?
			?><?endif?><?

			?><?php /* ---------- IS_PARENT ---------- */ ?><?
			?><?php if ($arItem['is_parent']): // is_parent ?><?

				?><?php if ($arItem['row']): // row ?><?

						?><div class="grid-row grid-remove grid-depth-level" data-ncol="<?php echo $arItem['ncol'] ?>" data-row="<?php echo $arItem['row'] ?>" data-depth-level="<?php echo $arItem['depth_level'] ?>" data-is-parent="<?php echo $arItem['is_parent'] ?>"><?
							?><div class="grid-tools"><?
								?><div class="grid-tool-copy" onclick="G.copy(this)" title="<?php echo Loc::getMessage("COPY")?>"></div><?
								?><div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div><?
								?><div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div><?
								?><div class="grid-tool-sortable" title="<?php echo Loc::getMessage("SORTABLE")?>"></div><?
							?></div><?
							?><div class="grid-inner clearfix"><?
								?><div class="add-col" onclick="G.addCol(this)" title="<?php echo Loc::getMessage("ADD_COLUMN")?>"></div><?

				?><?php elseif($arItem['col_lg']): ?><?
					
					?><div style="width: <?php echo sizeColumn(intval($arItem['col_lg']), $col_size) ?>%" class="grid-depth-level grid-col grid-remove grid-container child" data-ncol="<?php echo $arItem['ncol'] ?>" data-depth-level="<?php echo $arItem['depth_level'] ?>" data-col-xs="<?php echo $arItem['col_xs'] ?>" data-col-sm="<?php echo $arItem['col_sm'] ?>" data-col-md="<?php echo $arItem['col_md'] ?>" data-col-lg="<?php echo $arItem['col_lg'] ?>" data-is-parent="<?php echo $arItem['is_parent'] ?>"><?
						?><div class="grid-inner clearfix"><?
							?><div class="grid-tools"><?
								?><div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div><?
								?><div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div><?
								?><div class="add-row" onclick="G.addRow(this)" title="<?php echo Loc::getMessage("ADD_ROW")?>"></div><?
								?><div class="grid-tool-sortable" title="<?php echo Loc::getMessage("SORTABLE")?>"></div><?
							?></div><?

				?><?php endif // row ?><?
				
			?><?php else: ?><?

				?><?php if ($arItem['row']): // row ?><?

					?><div class="grid-row grid-remove grid-depth-level" data-ncol="<?php echo $arItem['ncol'] ?>" data-row="<?php echo $arItem['row'] ?>" data-depth-level="<?php echo $arItem['depth_level'] ?>"><?
						?><div class="grid-tools"><?
							?><div class="grid-tool-copy" onclick="G.copy(this)" title="<?php echo Loc::getMessage("COPY")?>"></div><?
							?><div class="grid-tool-component" onclick="G.addComponent(this)" title="<?php echo Loc::getMessage("ADD_COMPONENT")?>"></div><?
							?><div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div><?
							?><div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div><?
							?><div class="grid-tool-sortable" title="<?php echo Loc::getMessage("SORTABLE")?>"></div><?
						?></div><?
						?><div class="grid-inner clearfix"><?
							?><div class="add-col" onclick="G.addCol(this)" title="<?php echo Loc::getMessage("ADD_COLUMN")?>"></div><?
						?></div><?
					?></div><?

				?><?php elseif ($arItem['col_lg']): // col ?><?

					?><div style="width: <?php echo sizeColumn(intval($arItem['col_lg']), $col_size) ?>%" class="grid-depth-level grid-col grid-remove grid-container" data-ncol="<?php echo $arItem['ncol'] ?>" data-depth-level="<?php echo $arItem['depth_level'] ?>" data-col-xs="<?php echo $arItem['col_xs'] ?>" data-col-sm="<?php echo $arItem['col_sm'] ?>" data-col-md="<?php echo $arItem['col_md'] ?>" data-col-lg="<?php echo $arItem['col_lg'] ?>"><?
						?><div class="grid-inner"><?
							?><div class="grid-tools"><?
								?><div class="grid-tool-component" onclick="G.addComponent(this)" title="<?php echo Loc::getMessage("ADD_COMPONENT")?>"></div><?
								?><div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div><?
								?><div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div><?
								?><div class="add-row" onclick="G.addRow(this)" title="<?php echo Loc::getMessage("ADD_ROW")?>"></div><?
							?></div><?
						?></div><?
					?></div><?

				?><?php endif // row ?><?

			?><?php endif // is_parent ?><?

			?><?php $previousLevel = $arItem['depth_level'];?><?

		?><?endforeach?><?

		?><?php if($previousLevel > 1):?><?
			?><?php echo str_repeat('</div></div>', ($previousLevel-1) );?><?
		?><?php endif?><?

		$templates = ob_get_contents();
		ob_end_clean();
		echo $templates;
	}
}
else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['grid']) && !empty($_REQUEST['grid']))
{
	$grid = $_REQUEST['grid'];
	$site_lid = (isset($_REQUEST['params']['site_lid']))? $_REQUEST['params']['site_lid'] : 0;
	$template = (isset($_REQUEST['params']['template']))? $_REQUEST['params']['template'] : '';
	$name_template = (isset($_REQUEST['params']['name_template']))? $_REQUEST['params']['name_template'] : '';
	$template = (!empty($template))? $template : $name_template;

	if(!empty($template) && !preg_match('/^[a-zA-Z]+[a-zA-Z0-9\s\_\-]+$/', $template) || empty($template)) {
		die();
	}

	$template = str_replace('/', '', $template);

	$rsSites = CSite::GetByID($site_lid);
	if(!$arSite = $rsSites->Fetch())
		die();

	$folderTemplate = Application::getDocumentRoot() . $arSite['DIR'] . $options['folderTemplate'] . '/' . $template . '/';
	$folderAreas = $options['folderTemplate'] . '/' . $template . '/include_areas/';
	$pathAreas = Application::getDocumentRoot() . $arSite['DIR'] . $folderAreas;
	if (!file_exists($folderTemplate)) {
		if(!Directory::createDirectory($folderTemplate)) {
			die;
		}
	}
	if (!file_exists($pathAreas)) {
		if(!Directory::createDirectory($pathAreas)) {
			die;
		}
	}
	$include_areas = array_diff(scandir($pathAreas), array('..', '.'));
	$fileAreas = array();
	foreach($include_areas as $v) {
		if(false !== strripos($v, '.php')) {
			$fileAreas[] = $v;
		}
	}
	
	$arSection = $theme->Option()->get("section_{$template}", '', $site_lid);
	if(!empty($arSection)) {
		$arSection = unserialize($arSection);
	}

	$arNcol = array();
	ob_start();
	$IncludeFile = true;
	$previousLevel = 0;
	$rn = "<br />";
	$space = "&nbsp;";
	foreach($grid as $k => $arItem):?><?

		/************************* section **************************/
		$class = '';
		$style = '';
		$bg_image = '';
		$bg_color = '';
		if(isset($arSection[$arItem['ncol']]['add_class']) && !empty($arSection[$arItem['ncol']]['add_class'])) {
			$class = $arSection[$arItem['ncol']]['add_class'];
		}
		if(isset($arSection[$arItem['ncol']]['add_style']) && !empty($arSection[$arItem['ncol']]['add_style'])) {
			$style = ' style="' . $arSection[$arItem['ncol']]['add_style'] . '"';
		}
		if(isset($arSection[$arItem['ncol']]['bg_color']) && !empty($arSection[$arItem['ncol']]['bg_color'])) {
			$bg_color = "background-color:".$arSection[$arItem['ncol']]['bg_color'].";";
			if($style != '') {
				$style = str_replace('style="', 'style="'.$bg_color, $style);
			} else {
				$style = ' style="' . $bg_color . '"';
			}
		}
		if(isset($arSection[$arItem['ncol']]['bg_image']) && !empty($arSection[$arItem['ncol']]['bg_image'])) {
			$bg_image = "background-image: url('".$arSection[$arItem['ncol']]['bg_image']."');";
			if($style != '') {
				$style = str_replace('style="', 'style="'.$bg_image, $style);
			} else {
				$style = ' style="' . $bg_image . '"';
			}
		}
		/************************* /section **************************/

		$json_item = str_replace('"', '&quot;', json_encode($arItem));

		//$s = str_repeat($space, ($arItem['depth_level']-1));

		?><?if ($previousLevel && $arItem['depth_level'] < $previousLevel):?><?
			?><?php echo str_repeat('</div>', ($previousLevel - ($arItem['depth_level'])));?><?
		?><?endif?><?

		?><?php /* ---------- IS_PARENT ---------- */ ?><?
		?><?php if ($arItem['is_parent']): // is_parent ?><?

			?><?php if ($arItem['row']): // row ?><?

					?><div class="||| section |||<?php echo $class ?>"<?php echo $style ?> data-grid="<?php echo $json_item ?>"><?

			?><?php elseif($arItem['col_lg']): ?><?

				?><div class="<?php echo $class ?>col-xs-<?php echo $arItem['col_xs'] ?> col-sm-<?php echo $arItem['col_sm'] ?> col-md-<?php echo $arItem['col_md'] ?> col-lg-<?php echo $arItem['col_lg'] ?>"<?php echo $style ?> data-grid="<?php echo $json_item ?>"><?

			?><?php endif // row ?><?
			
		?><?php else: ?><?

			?><?php if ($arItem['row']): // row ?><?

				$arNcol[] = 'area'.$arItem['ncol'].'.php';
				?><div class="||| section |||<?php echo $class ?>"<?php echo $style ?> data-grid="<?php echo $json_item ?>"><?
				echo '<?$APPLICATION->IncludeComponent("bitrix:main.include","",array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."'.$folderAreas.'area'.$arItem['row'].'.php","AREA_FILE_RECURSIVE" => "N","EDIT_MODE" => "html"),false,array("HIDE_ICONS" => "N"));?>';?><?
				?></div><?

			?><?php elseif ($arItem['col_lg']): // col ?><?

				$arNcol[] = 'area'.$arItem['ncol'].'.php';
				?><?php echo $s ?><div class="<?php echo $class ?> col-xs-<?php echo $arItem['col_xs'] ?> col-sm-<?php echo $arItem['col_sm'] ?> col-md-<?php echo $arItem['col_md'] ?> col-lg-<?php echo $arItem['col_lg'] ?>"<?php echo $style ?> data-grid="<?php echo $json_item ?>"><?
					echo '<?$APPLICATION->IncludeComponent("bitrix:main.include","",array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."'.$folderAreas.'area'.$arItem['ncol'].'.php","AREA_FILE_RECURSIVE" => "N","EDIT_MODE" => "html"),false,array("HIDE_ICONS" => "N"));?>';?></div><?
			?><?php endif // row ?><?

		?><?php endif // is_parent ?><?

		?><?php $previousLevel = $arItem['depth_level'];?><?

	?><?endforeach?><?

	?><?php if($previousLevel > 1):?><?
		?><?php echo str_repeat('</div>', ($previousLevel-1) );?><?
	?><?php endif?><?

	$templates = ob_get_contents();
	ob_end_clean();

	$fileAreas = array_diff($fileAreas, $arNcol);

	foreach($fileAreas as $file) {
		unlink($pathAreas.$file);
	}

	function replaceSection($html, $sectionDom, $space = "&nbsp;", $rn = "<br />")
	{
		static $templates;
		if(count($sectionDom) > 0) {
			foreach($sectionDom as $k => $element) {
				if ($element->tag=='div') {
					$attr = '';
					if(!empty($element->attr)) {
						foreach($element->attr as $k => $getAttr) {
							$getAttr = str_replace('||| section |||', '', $getAttr);
							if(!empty($getAttr)) {
								$attr .= ' ' . $k . '="' . $getAttr . '"';
							}
						}
					}
					//$s = "";
					if($element->attr['data-grid']) {
						$data_grid = json_decode(str_replace('&quot;', '"', $element->attr['data-grid']), true);
						//$s = str_repeat($space, ($data_grid['depth_level']-1));
					}
					if($data_grid['is_parent']) {
						if($data_grid['depth_level'] == 1)
							$element->outertext = "<section{$attr}><div class=\"container\"><div class=\"row\">" . $element->innertext . "</div></div></section>";
						else
							$element->outertext = "<section{$attr}><div class=\"row\">" . $element->innertext . "</div></section>";
					} else {
						$element->outertext = "<section{$attr}>" . $element->innertext . "</section>";
					}
				}
			}
			$templates = $html->dom()->save();
			$html->dom()->clear();
			$html = new SimpleHtml($templates);
			$sectionDom = $html->dom()->find('.section');
			replaceSection($html, $sectionDom, $space, $rn);
		}
		$html->dom()->clear();
		return $templates;
	}

	$html = new SimpleHtml($templates);
	$sectionDom = $html->dom()->find('.section');
	$templates = replaceSection($html, $sectionDom, $space, $rn);
	$templates = str_replace(array("<br />","&nbsp;"), array("\r\n","    "), $templates);

	File::putFileContents($folderTemplate . 'index.php', $templates);
}
require($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin_after.php');