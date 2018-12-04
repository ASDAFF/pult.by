<?if(!$USER->IsAdmin()) return;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Unisoftmedia\Styleshop\Theme;
use Unisoftmedia\Styleshop\Library\SimpleHtml;

Loc::loadMessages(__FILE__);
$bitrix_include_areas['bitrix_include_areas'] = "Y";
$GLOBALS["APPLICATION"]->SetShowIncludeAreas($bitrix_include_areas['bitrix_include_areas']=="Y");
$module_id = 'unisoftmedia.styleshop';

Loader::includeModule($module_id);
$theme = new Theme($module_id);
$options = $theme->Option()->toArray();
CUtil::InitJSCore(array('window'));

///////////////////////////////////////////////////////////

if (isset($_REQUEST['site_lid']) && strlen($_REQUEST['site_lid']) > 0) {	
	$_SESSION['site_lid'] = htmlspecialcharsbx($_REQUEST['site_lid']);
} else if(isset($_REQUEST['site_id']) && strlen($_REQUEST['site_id']) > 0) {
	$_SESSION['site_lid'] = htmlspecialcharsbx($_REQUEST['site_id']);
}

$rsSites = CSite::GetList($by = 'sort', $order = 'desc');
$defaultSite = array();
while ($arSite = $rsSites->Fetch())
{
	$sites['REFERENCE'][] = $arSite['NAME'];
	$sites['REFERENCE_ID'][] = $arSite['LID'];

	if (isset($_SESSION['site_lid']) && strlen($_SESSION['site_lid']) > 0) {

		if ($_SESSION['site_lid'] == $arSite['LID']) {
			$defaultSite = $arSite;
		}

	} else if(empty($defaultSite)) {
		$defaultSite = $arSite;
	}
}

if (strlen($_SESSION['site_lid']) > 0) {
	$siteId = $_SESSION['site_lid'];
} else {
	$siteId = $sites['REFERENCE_ID'][0];
}
/////////////////////////////////////////////////////////


if($REQUEST_METHOD == "POST" && strlen($Update) > 0 && check_bitrix_sessid())
{
	$theme->Option()->set('googlefonts', ( isset($_REQUEST['googlefonts']) && !empty($_REQUEST['googlefonts'])? serialize($_REQUEST['googlefonts']) : '' ), $siteId);
	if(isset($_REQUEST['fonts']) && !empty($_REQUEST['fonts'])) {
		$fonts = array();
		foreach($_REQUEST['fonts'] as $k => $font) {
			if(isset($font) & !empty($font)) {
				$fonts[$k] = $font;
			}
		}
		$theme->Option()->set('fonts', (!empty($fonts)) ? serialize($fonts) : '', $siteId);
	}
	$theme->Option()->set('responsive', ( isset($_REQUEST['responsive'])? $_REQUEST['responsive'] : 0 ), $siteId);

	$theme->Option()->set('themes', ( isset($_REQUEST['themes'])? $_REQUEST['themes'] : 0 ), $siteId);
	$theme->Option()->set('type_header', ( isset($_REQUEST['type_header'])? $_REQUEST['type_header'] : 0 ), $siteId);

	$theme->Option()->set('top_panel_color', ( isset($_REQUEST['top_panel_color'])? $_REQUEST['top_panel_color'] : 0 ), $siteId);
	/************** detail ******************/
	$theme->Option()->set('detail_column', ( isset($_REQUEST['detail_column'])? $_REQUEST['detail_column'] : '' ), $siteId);
	/************** section ******************/
	$theme->Option()->set('section_column', ( isset($_REQUEST['section_column'])? $_REQUEST['section_column'] : '' ), $siteId);
	$theme->Option()->set('section_list', ( isset($_REQUEST['section_list'])? $_REQUEST['section_list'] : '' ), $siteId);
	$theme->Option()->set('smart_filter', ( isset($_REQUEST['smart_filter'])? $_REQUEST['smart_filter'] : '' ), $siteId);
	/************** main menu ******************/
	$theme->Option()->set('menu_color', ( isset($_REQUEST['menu_color'])? $_REQUEST['menu_color'] : '' ), $siteId);

	$template = (isset($_REQUEST['template']))? $_REQUEST['template'] : '';
	$name_template = (isset($_REQUEST['name_template']))? $_REQUEST['name_template'] : '';
	$template = (!empty($template))? $template : $name_template;
	if(!empty($template)) {
		$theme->Option()->set('template', $template, $siteId);
	}
}
$aTabs = array(
	array("DIV" => "un_main_settings", "TAB" => Loc::getMessage('MAIN_TAB_SET'), "ICON" => "settings", "TITLE" => Loc::getMessage('SETTINGS_THEMES')),
	array("DIV" => "un_typography_settings", "TAB" => Loc::getMessage('TYPOGRAPHY_TAB_SET'), "ICON" => "settings", "TITLE" => Loc::getMessage('SETTINGS_THEMES_TYPOGRAPHY')),
	array("DIV" => "un_home_settings", "TAB" => Loc::getMessage('HOME_TAB_TITLE_SET'), "ICON" => "settings", "TITLE" => Loc::getMessage('SETTINGS_THEMES_HOME')),
);

$APPLICATION->SetAdditionalCSS(BX_ROOT."/css/".$module_id."/jquery-ui.min.css");
$APPLICATION->SetAdditionalCSS(BX_ROOT."/css/main/font-awesome.min.css");
$APPLICATION->SetAdditionalCSS(BX_ROOT."/themes/".$module_id.".css");
$APPLICATION->SetAdditionalCSS(BX_ROOT."/css/".$module_id."/colorpicker/colorpicker.css");
$APPLICATION->AddHeadScript(BX_ROOT."/js/".$module_id."/jquery.js");
$APPLICATION->AddHeadScript(BX_ROOT."/js/".$module_id."/jquery-ui.min.js");
$APPLICATION->AddHeadScript(BX_ROOT."/js/".$module_id."/colorpicker/jqColorPicker.min.js");
//$APPLICATION->AddHeadScript(BX_ROOT."/js/".$module_id."/colorpicker/index.js");

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();?>
<form method="POST" action="<?php echo $APPLICATION->GetCurPage()?>?mid=<?php echo htmlspecialcharsbx($mid)?>&lang=<?php echo LANGUAGE_ID?>">
<input type="hidden" name="module_id" id="module_id" value="<?php echo $module_id ?>">
<?php echo bitrix_sessid_post() ?>
<?$tabControl->BeginNextTab();?>

	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('LIST_SITE') ?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?php echo SelectBoxFromArray(
				"site_id",
				$sites,
				$siteId,
				"",
				"onchange='change_site( this.value );'"
				);
			?>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><b><?php echo Loc::getMessage('GENERAL') ?></b></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage("RESPONSIVE")?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsResponse = array();
			foreach($options['responsive'] as $res_k => $responsive)
			{
				$optionsResponse['REFERENCE'][] = Loc::getMessage($responsive);
				$optionsResponse['REFERENCE_ID'][] = $res_k;
			}
			?>
			<?php echo SelectBoxFromArray(
				"responsive",
				$optionsResponse,
				$theme->Option()->get('responsive', '', $siteId),
				"",
				""
				);
				unset($optionsResponse);
			?>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage("COLOR_SITE")?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$folderThemes = Application::getDocumentRoot() . '/bitrix/templates/styleshop/theme/';
			$arThemes = (file_exists($folderThemes))? array_diff(scandir($folderThemes), array('..', '.')) : array();
			$themes__i = 0;

			$optionsThemes = array();

			$optionsThemes['REFERENCE'][] = Loc::getMessage('COLOR_SITE_DEFAULT');
			$optionsThemes['REFERENCE_ID'][] = '';

			foreach($arThemes as $res_k => $themes)
			{
				$optionsThemes['REFERENCE'][] = ++$themes__i;
				$optionsThemes['REFERENCE_ID'][] = $themes;
			}
			?>
			<?php echo SelectBoxFromArray(
				"themes",
				$optionsThemes,
				$theme->Option()->get('themes', '', $siteId),
				"",
				""
				);
				unset($optionsThemes);
			?>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage("VIEW_HEADER")?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsTypeHeader = array();
			for($i = 1; $i <= 7; $i++)
			{
				$optionsTypeHeader['REFERENCE'][] = $i;
				$optionsTypeHeader['REFERENCE_ID'][] = $i;
			}
			?>
			<?php echo SelectBoxFromArray(
				"type_header",
				$optionsTypeHeader,
				$theme->Option()->get('type_header', '', $siteId),
				"",
				""
				);
				unset($optionsTypeHeader);
			?>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage("TOP_PANEL")?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsTopPanelColor = array();
			foreach($options['top_panel_color'] as $res_k => $top_panel_color)
			{
				$optionsTopPanelColor['REFERENCE'][] = Loc::getMessage($top_panel_color);
				$optionsTopPanelColor['REFERENCE_ID'][] = $res_k;
			}
			?>
			<?php echo SelectBoxFromArray(
				"top_panel_color",
				$optionsTopPanelColor,
				$theme->Option()->get('top_panel_color', '', $siteId),
				"",
				""
				);
				unset($optionsTopPanelColor);
			?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?php echo Loc::getMessage('DETAIL_ELEMENT') ?></b></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('DETAIL_COLUMN')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsDetailColumn = array();
			foreach($options['column'] as $res_k => $column)
			{
				$optionsDetailColumn['REFERENCE'][] = $column;
				$optionsDetailColumn['REFERENCE_ID'][] = $res_k;
			}
			?>
			<?php echo SelectBoxFromArray(
				"detail_column",
				$optionsDetailColumn,
				$theme->Option()->get('detail_column', '', $siteId),
				"",
				""
				);
				unset($optionsDetailColumn);
			?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?php echo Loc::getMessage('SECTION_ELEMENT') ?></b></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('DETAIL_COLUMN')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsSectionColumn = array();
			foreach($options['column'] as $res_k => $column)
			{
				$optionsSectionColumn['REFERENCE'][] = $column;
				$optionsSectionColumn['REFERENCE_ID'][] = $res_k;
			}
			?>
			<?php echo SelectBoxFromArray(
				"section_column",
				$optionsSectionColumn,
				$theme->Option()->get('section_column', '', $siteId),
				"",
				""
				);
				unset($optionsSectionColumn);
			?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?php echo Loc::getMessage('SETTINGS_MENU') ?></b></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('MENU_COLOR')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?
			$optionsMenuColor = array();
			for($i = 1; $i <= 3; $i++)
			{
				$optionsMenuColor['REFERENCE'][] = $i;
				$optionsMenuColor['REFERENCE_ID'][] = $i;
			}
			?>
			<?php echo SelectBoxFromArray(
				"menu_color",
				$optionsMenuColor,
				$theme->Option()->get('menu_color', '', $siteId),
				"",
				""
				);
				unset($optionsMenuColor);
			?>
		</td>
	</tr>
<?$tabControl->BeginNextTab();?>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('GOOGLE_FONTS')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?$fontSelected = $theme->Option()->get('googlefonts', '', $siteId);

			if(!empty($fontSelected))
				$fontSelected = unserialize($fontSelected);
			else
				$fontSelected = array();
?>
			<select name="googlefonts[]" multiple="multiple">
				<?foreach($options['googlefonts'] as $family)
				{
					?><option value="<?php echo $family ?>"<?php echo in_array($family, $fontSelected)? ' selected' : '' ?>><?php echo $family ?></option><?
				}?>
			</select>
		</td>
	</tr>
	<?$fonts = $theme->Option()->get('fonts', '', $siteId);
	if(!empty($fonts))
		$fonts = unserialize($fonts);?>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('GENERAL_FONT')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[body]" value="<?php echo isset($fonts['body'])? $fonts['body'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H1_FONT')?><small><?php echo Loc::getMessage('H1_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h1]" value="<?php echo isset($fonts['h1'])? $fonts['h1'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H2_FONT')?><small><?php echo Loc::getMessage('H2_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h2]" value="<?php echo isset($fonts['h2'])? $fonts['h2'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H3_FONT')?><small><?php echo Loc::getMessage('H3_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h3]" value="<?php echo isset($fonts['h3'])? $fonts['h3'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H4_FONT')?><small><?php echo Loc::getMessage('H4_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h4]" value="<?php echo isset($fonts['h4'])? $fonts['h4'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H5_FONT')?><small><?php echo Loc::getMessage('H5_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h5]" value="<?php echo isset($fonts['h5'])? $fonts['h5'] : '' ?>" />
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('H6_FONT')?><small><?php echo Loc::getMessage('H6_FONT_DEFAULT')?></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="fonts[h6]" value="<?php echo isset($fonts['h6'])? $fonts['h6'] : '' ?>" />
		</td>
	</tr>
<?$tabControl->BeginNextTab();?>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('LIST_SITE')?><small></small></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
		<?php echo SelectBoxFromArray(
				"site_lid",
				$sites,
				$siteId,
				"",
				"onchange='G.changeSiteLid(this)'"
				);
			?>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('TEMPLATE')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<?$folderTemplate = Application::getDocumentRoot() . $defaultSite['DIR'] . $options['folderTemplate'];
			$templates = (file_exists($folderTemplate))? array_diff(scandir($folderTemplate), array('..', '.')) : array();
			$templateSelected = $theme->Option()->get('template', '', $siteId);?>
			<select name="template" onchange="G.changeTemplate(this)"><?
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
			?></select>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('TEMPLATE_NAME')?></td>
		<td nowrap width="60%" class="adm-detail-content-cell-r">
			<input type="text" size="30" maxlength="255" name="name_template" value="" />
		</td>
	</tr>
	<tr>
		<td width="20%" style="text-align: left;"><?php echo Loc::getMessage('DESIGN_IN')?></td>
		<td width="60%" style="text-align: left;"><?php echo Loc::getMessage('GRID_TOGGLE')?></td>
	</tr>
	<tr>
		<td width="20%" style="text-align: left;">
			<ul class="design_in">
				<li><a class="adm-btn active" data-option="large" href="#" onclick="G.designIn(this); return false;" title="<?php echo Loc::getMessage('LARGE_DEVICE')?>"><i class="fa fa-desktop"></i></a></li>
				<li><a class="adm-btn" data-option="medium" href="#" onclick="G.designIn(this); return false;" title="<?php echo Loc::getMessage('MEDIUM_DEVICE')?>"><i class="fa fa-laptop"></i></a></li>
				<li><a class="adm-btn" data-option="tablet" href="#" onclick="G.designIn(this); return false;" title="<?php echo Loc::getMessage('TABLET')?>"><i class="fa fa-tablet"></i></a></li>
				<li><a class="adm-btn" data-option="mobile" href="#" onclick="G.designIn(this); return false;" title="<?php echo Loc::getMessage('SMARTPHONE')?>"><i class="fa fa-mobile"></i></a></li>
			</ul>
		</td>
		<td width="60%" style="text-align: left;"><a class="adm-btn grid-toggle" onclick="G.gridToggle('.grid-layout'); return false;" href="#"><i class="fa fa-th-large"></i></a></td>
	</tr>
	<tr>
		<td width="40%" colspan="2">
			<div class="grid-system adm-profit-indicators-block">
				<div class="grid grid-container">
					<div class="grid-inner ui-sortable grid-begin" id="sortable"></div>
					<div class="add-row sg-row" onclick="G.addRow(this)" title="<?php echo Loc::getMessage("ADD_ROW")?>"><i class="fa fa-plus-square-o"></i></div>
				</div>
			</div>
			<div class="grid-btn-block">
				<a class="adm-btn grid-button save" onclick="G.save(this); return false;" href="#"><?php echo Loc::getMessage('UN_BTN_SAVE')?></a>
				<a class="adm-btn grid-button" onclick="G.templateDelete(this); return false;" href="#"><?php echo Loc::getMessage('DELETE')?></a>
			</div>
		</td>
	</tr>
<script type="text/javascript">
	'use strict';
		$('.colorpickerHolder').colorPicker({
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		opacity: false,

		buildCallback: function($elm) {
			var colorInstance = this.color,
				colorPicker = this;

			$elm.prepend('<div class="cp-panel">' +
				'R <input type="text" class="cp-r" /><br>' +
				'G <input type="text" class="cp-g" /><br>' +
				'B <input type="text" class="cp-b" /><hr>' +
				'H <input type="text" class="cp-h" /><br>' +
				'S <input type="text" class="cp-s" /><br>' +
				'B <input type="text" class="cp-v" /><hr>' +
				'<input type="text" class="cp-HEX" />' +
			'</div>').on('change', 'input', function(e) {alert('change');
				var value = this.value,
					className = this.className,
					type = className.split('-')[1],
					color = {};

				color[type] = value;
				colorInstance.setColor(type === 'HEX' ? value : color,
					type === 'HEX' ? 'HEX' : /(?:r|g|b)/.test(type) ? 'rgb' : 'hsv');
				colorPicker.render();
				this.blur();
			});
		},

		cssAddon: // could also be in a css file instead
			'.cp-color-picker{box-sizing:border-box; width:226px;}' +
			'.cp-color-picker .cp-panel {line-height: 21px; float:right;' +
				'padding:0 1px 0 8px; margin-top:-1px; overflow:visible}' +
			'.cp-xy-slider:active {cursor:none;}' +
			'.cp-panel, .cp-panel input {color:#bbb; font-family:monospace,' +
				'"Courier New",Courier,mono; font-size:12px; font-weight:bold;}' +
			'.cp-panel input {width:28px; height:12px; padding:2px 3px 1px;' +
				'text-align:right; line-height:12px; background:transparent;' +
				'border:1px solid; border-color:#222 #666 #666 #222;}' +
			'.cp-panel hr {margin:0 -2px 2px; height:1px; border:0;' +
				'background:#666; border-top:1px solid #222;}' +
			'.cp-panel .cp-HEX {width:44px; position:absolute; margin:1px -3px 0 -2px;}' +
			'.cp-alpha {width:155px;}',

		renderCallback: function($elm, toggled) {
			var colors = this.color.colors.RND,
				modes = {
					r: colors.rgb.r, g: colors.rgb.g, b: colors.rgb.b,
					h: colors.hsv.h, s: colors.hsv.s, v: colors.hsv.v,
					HEX: this.color.colors.HEX
				};
			$($elm).parent().children('input').val('#' + this.color.colors.HEX);
			$('input', '.cp-panel').each(function() {
				this.value = modes[this.className.substr(3)];
			});
		}
	});
</script>
<script type="text/javascript">
	'use strict';

	function Grid(options) {

		/**
		 * Current options set by the caller including defaults.
		 * @public
		 */
		this.options = $.extend({}, Grid.Defaults, options);

		//this.row = this.maxInt(this.options.classSortable + ' [' + this.options.attrRow + ']',this.options.attrRow);
		this.ncol = this.maxInt(this.options.classSortable + ' ['+ this.options.attrNcol +']',this.options.attrNcol);

		//this.setup();
		this.initialize();
	}

	/**
	 * Default options.
	 * @public
	 */
	Grid.Defaults = {
		grid: 12,
		paddingCol: 30,
		classSortable: '#sortable',
		classContainer: '.grid-system',
		attrNcol: 'data-ncol',
		classCol: '.grid-col',
		attrRow: 'data-row',
		attrNcol: 'data-ncol',
		screen: 'large',
		folderTemplate: '',
	};

	Grid.prototype.initialize = function()
	{
		this.sortable(this.options.classSortable,
			{
				items: '> div',
				handle: '.grid-tool-sortable',
				placeholder: 'sortable-placeholder'
			}
		);
	};

	Grid.prototype.setup = function()
	{
		var container = $(this.options.classContainer);
		container.prepend('<div class="grid-layout"></div>');
		var grid_layout = container.children('.grid-layout'),
				col = '';
		for (var i = 0; i < this.options.grid; i++) {
		   col += '<div><span></span></div>';
		}
		grid_layout.append(col);
		grid_layout.children('div').css({'width': 100 / this.options.grid + '%', 'padding-left': this.options.paddingCol / 2 + 'px', 'padding-right': this.options.paddingCol / 2 + 'px'});
	};
	
	Grid.prototype.sortable = function(element, options)
	{
		$( element ).sortable(options);
	}

	Grid.prototype.updateColsWidth = function(obj, all)
	{
		var $grid_col = $(obj),
				grid_inner = $grid_col.closest('.grid-inner'),
				grid_col_width = parseFloat($grid_col.width()),
				c = Math.floor((this.options.grid * grid_col_width / (grid_inner.width() - this.options.paddingCol))) + 1;
		if( c > this.options.grid ){
			c = this.options.grid;
		}
		$grid_col.css('width', c / this.options.grid * 100 + '%');

		if(all !== undefined) {
			$grid_col.attr('data-col-lg', c);
			$grid_col.attr('data-col-md', c);
			$grid_col.attr('data-col-sm', c);
			$grid_col.attr('data-col-xs', this.options.grid);
		} else {
			if(this.options.screen == 'large')
				$grid_col.attr('data-col-lg', c);
			if(this.options.screen == 'medium')
				$grid_col.attr('data-col-md', c);
			if(this.options.screen == 'tablet')
				$grid_col.attr('data-col-sm', c);
			if(this.options.screen == 'mobile')
				$grid_col.attr('data-col-xs', c);
		}
	}

	Grid.prototype.updateColWidthByScreen = function()
	{
		var Grid = this;
		var data_col;
		switch( Grid.options.screen ){
			case 'medium':
				data_col = 'data-col-md';
				break;
			case 'tablet':
				data_col = 'data-col-sm';
				break;
			case 'mobile':
				data_col = 'data-col-xs';
				break;
			default: 
				data_col = 'data-col-lg';	
		}

		$(Grid.options.classCol).each( function(){
			$(this).css('width', parseInt($(this).attr(data_col)) / Grid.options.grid * 100 + '%' );  
		});
	}

	Grid.prototype.alignColsWidth = function($this)
	{
		var Grid = this;
		var grid_inner = $this.closest('.grid-inner');
		$this.addClass('current');
		grid_inner.children(Grid.options.classCol + '.current ~ ' + Grid.options.classCol).each(function(){
			var sum = 0;
			var css_width = 0;
			grid_inner.children(Grid.options.classCol).each(function(i){
				sum = sum + parseFloat(this.style.width);
				if(css_width > 0) {
					$(this).css('width', css_width + '%');
					Grid.updateColsWidth($(this), true);
				}
				if($(this).hasClass('current')) {
					css_width = (100 - sum) / parseFloat(grid_inner.children(Grid.options.classCol + ':gt('+i+')').length);
					$(this).removeClass('current');
				}
			});
			$(this).addClass('current');
		});
	}

	Grid.prototype.colsResizable = function(grid_col)
	{
		var Grid = this;
		grid_col.resizable({
			handles: 'e',
			stop: function(event, ui) {
				setTimeout( function(){
					Grid.updateColsWidth($(event.target));
				}, 200 );
			},
		});
	}

	Grid.prototype.maxInt = function(c, attr)
	{
		var max = 0;
		$(c).each(function(){
			var cv;
			var attr_int = parseInt($(this).attr(attr));
			if(attr_int > 0) {
				cv = attr_int * 1;
				max = max < cv ? cv : max;
			}
		});
		return max;
	}

	Grid.prototype.loader = function(className, remove)
	{
		if(remove) {
			$(className + ' .spinner').remove();
			$(className + ' .un-overlay').remove();
		} else {
			$(className).append('<div class="spinner"></div>');
			$(className).append('<div class="un-overlay"></div>');
		}
	}

	Grid.prototype.addRow = function(obj)
	{
		var $this = $(obj);
		this.row = ++this.row;
		this.ncol = ++this.ncol;
		var sort = 'grid-tool-sortable';
		if($this.closest(this.options.classCol).length) {
			//sort = 'grid-tool-sortable-child';
		}
		var depth_level = $this.parents('.grid-depth-level').length + 1;
		$this.closest('.grid-depth-level').attr('data-is-parent', 1);
		$this.closest('.grid-container').children('.grid-inner').append('<div class="grid-row grid-remove grid-depth-level" data-ncol="'+this.ncol+'" data-row="'+this.row+'" data-depth-level="' + depth_level + '">'+
		'<div class="grid-tools">'+
		'<div class="grid-tool-copy" onclick="G.copy(this)" title="<?php echo Loc::getMessage("COPY")?>"></div>'+
		'<div class="grid-tool-component" onclick="G.addComponent(this)" title="<?php echo Loc::getMessage("ADD_COMPONENT")?>"></div>'+
		'<div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div>'+
		'<div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div>'+
		'<div class="'+sort+'" title="<?php echo Loc::getMessage("SORTABLE")?>"></div></div>'+
		'<div class="grid-inner clearfix"><div class="add-col" onclick="G.addCol(this)" title="<?php echo Loc::getMessage("ADD_COLUMN")?>"></div></div></div>');
		$this.closest(this.options.classCol).addClass('child');

		this.sortable(this.options.classCol + ' > .grid-inner',
			{
				items: '> .grid-row',
				handle: '.grid-tool-sortable',
				placeholder: 'sortable-placeholder'
			}
		);
	}

	Grid.prototype.addCol = function(obj)
	{
		var Grid = this;
		Grid.ncol = ++Grid.ncol;

		var $this = $(obj),
				grid_inner = $this.closest('.grid-inner'),
				grid_inner_length = parseFloat(grid_inner.children(Grid.options.classCol).length),
				column = 100 / (grid_inner_length + 1),
				nrow = grid_inner.closest('.grid-row').attr(Grid.options.attrRow);
		if(grid_inner_length >= Grid.options.grid) {
			return false;
		}

		var depth_level = $this.parents('.grid-depth-level').length + 1;

		$this.closest('.grid-depth-level').attr('data-is-parent', 1);
		grid_inner.append('<div class="grid-depth-level ' + Grid.options.classCol.substring(1) + ' grid-remove grid-container" data-ncol="'+Grid.ncol+'" data-depth-level="' + depth_level + '"><div class="grid-inner">'+
		'<div class="grid-tools">'+
		'<div class="grid-tool-component" onclick="G.addComponent(this)" title="<?php echo Loc::getMessage("ADD_COMPONENT")?>"></div>'+
		'<div class="grid-tool-edit" onclick="G.editCol(this)" title="<?php echo Loc::getMessage("EDIT")?>"></div>'+
		'<div class="grid-tool-delete" onclick="G.delete(this)" title="<?php echo Loc::getMessage("DELETE")?>"></div>'+
		'<div class="add-row" onclick="G.addRow(this)" title="<?php echo Loc::getMessage("ADD_ROW")?>"></div>'+
		'</div></div></div>').children(Grid.options.classCol).css('width', column + '%');

		grid_inner.children(Grid.options.classCol).each(function(){
			Grid.updateColsWidth($(this), true);
			Grid.alignColsWidth($(this));
		});
		Grid.colsResizable($(this.options.classCol));
		Grid.sortable('.grid-row > .grid-inner',
			{
				items: '> ' + Grid.options.classCol,
				placeholder: 'sortable-placeholder-col',
				start: function( event, ui ){
					$( '.sortable-placeholder-col', event.target ).width( $(ui.item).width() );
				}
			}
		);
	}

	Grid.prototype.gridToggle = function(className)
	{
		$(className).toggle();
	}

	Grid.prototype.copy = function(obj)
	{
		var Grid = this;

		this.row = ++this.row;
		this.ncol = ++this.ncol;

		var row = this.row,
				ncol = this.ncol,
				$this = $(obj),
				grid_row_clone = $this.closest('.grid-row').clone();

		$(grid_row_clone).find('.ui-resizable-handle').remove();
		$(grid_row_clone).attr(Grid.options.attrRow, row);
		$(grid_row_clone).attr(Grid.options.attrNcol, ncol);

		grid_row_clone = $(grid_row_clone).appendTo( $this.closest('.grid-inner') );
		var grid_col = $(grid_row_clone).find('.grid-depth-level');
		grid_col.each(function(){
			ncol = ++ncol;
			$(this).attr(Grid.options.attrNcol, ncol);
		});
		$(grid_row_clone).find('.grid-row').each(function(){
				row = ++row;
				$(this).attr(Grid.options.attrRow, row);
		});
		this.row = row;
		this.ncol = ncol;
		this.colsResizable(grid_col);
	}

	Grid.prototype.delete = function(obj)
	{
		if (confirm('<?php echo Loc::getMessage("DELETE_SURE")?>')) {
			var $this = $(obj),
					grid = $this.closest('.grid-remove'),
					grid_inner = grid.parent();
			grid.remove();
			if(!grid_inner.children('.grid-depth-level').length) {
				grid_inner.closest('.grid-depth-level').removeAttr('data-is-parent');
			} else {
				this.updateColsWidth(grid_inner.children(this.options.classCol));
			}
		}
	}

	Grid.prototype.templateDelete = function(obj)
	{
		if (confirm('<?php echo Loc::getMessage("DELETE_SURE")?>')) {
			var $this = $(obj);
			var Grid = this;
			var data = {};
			data['sessid'] = BX.bitrix_sessid();
			data['site_lid'] = $('select[name=site_lid]').val();
			data['template'] = $('select[name=template]').val();
			data['delete'] = 'Y';
			$.ajax({
				type: 'POST',
				url: '/bitrix/admin/un_grid_ajax.php',
				data: data,
			}).done(function(response) {
				location.reload();
				Grid.loader(Grid.options.classSortable, true);
				$this.removeClass('disabled');
			});
		}
	}

	Grid.prototype.save = function(obj)
	{
		var Grid = this;
		var $this = $(obj),
				template = $('select[name=template]').val(),
				name_template = $('input[name=name_template]').val(),
				data = {};

		data['grid'] = {};
		
		if($this.hasClass('disabled')) {
			return false;
		}
		if(template == '' && name_template == '') {
			alert('<?php echo Loc::getMessage("TEMPLATE_NAME") ?>');
			return false;
		}

		$(this.options.classSortable).find('.grid-depth-level').each(function(index){
			if(data['grid'][index] === undefined) {
				data['grid'][index] = {};
			}
			data['grid'][index]['row'] = $(this).attr(Grid.options.attrRow);
			data['grid'][index]['col_lg'] = $(this).attr('data-col-lg');
			data['grid'][index]['col_md'] = $(this).attr('data-col-md');
			data['grid'][index]['col_sm'] = $(this).attr('data-col-sm');
			data['grid'][index]['col_xs'] = $(this).attr('data-col-xs');
			data['grid'][index]['ncol'] = $(this).attr(Grid.options.attrNcol);
			data['grid'][index]['depth_level'] = $(this).attr('data-depth-level');
			data['grid'][index]['is_parent'] = $(this).attr('data-is-parent');
		});

		if($.isEmptyObject(data['grid'])) {
			return false;
		}
		Grid.loader(Grid.options.classSortable);
		$this.addClass('disabled');
		data['sessid'] = BX.bitrix_sessid();
		data['params'] = {};
		data['params']['site_lid'] = $('select[name=site_lid]').val();
		data['params']['template'] = $('select[name=template]').val();
		data['params']['name_template'] = $('input[name=name_template]').val();

		$.ajax({
			type: 'POST',
			url: '/bitrix/admin/un_grid_ajax.php',
			data: data,
		}).done(function(response) {
			if(template == '')
				location.reload();
			Grid.loader(Grid.options.classSortable, true);
			$this.removeClass('disabled');
		});
	}

	Grid.prototype.changeSiteLid = function(obj)
	{
		var Grid = this;
		var $this = $(obj),
				data = {},
				$template = $('select[name=template]');

		data['sessid'] = BX.bitrix_sessid();
		data['site_lid'] = $this.val();
		data['module_id'] = $('#module_id').val();
		$this.attr('disabled', 'disabled');
		$template.attr('disabled', 'disabled');
		$.ajax({
			method: 'GET',
			url: '/bitrix/admin/un_changeSiteLid.php',
			data: data,
		}).done(function(response) {
			$this.removeAttr('disabled');
			$template
				.html(response)
				.removeAttr('disabled')
				.change();
		});
	}

	Grid.prototype.changeTemplate = function(obj)
	{
		var Grid = this;
		var data = {},
				$this = $(obj),
				name_template = $('input[name=name_template]');

		data['sessid'] = BX.bitrix_sessid();
		data['template'] = $this.val();
		data['col_size'] = this.options.grid;
		data['site_lid'] = $('select[name=site_lid]').val();
		data['module_id'] = $('#module_id').val();

		if(data['template'] == '') {
			Grid.row = 0;
			Grid.ncol = 0;
			name_template.removeAttr('disabled');
			$(Grid.options.classSortable).empty();
			return false;
		}
		name_template.val('').attr('disabled', 'disabled');
		Grid.loader(Grid.options.classSortable);
		$this.attr('disabled', 'disabled');
		$('select[name=site_lid]').attr('disabled', 'disabled');
		$.ajax({
			method: 'GET',
			url: '/bitrix/admin/un_grid_ajax.php',
			data: data,
		}).done(function(response) {
			Grid.loader(Grid.options.classSortable, true);
			$this.removeAttr('disabled');
			$('select[name=site_lid]').removeAttr('disabled');
			$(Grid.options.classSortable).html(response);
			Grid.row = Grid.maxInt(Grid.options.classSortable + ' [' + Grid.options.attrRow + ']',Grid.options.attrRow);
			Grid.ncol = Grid.maxInt(Grid.options.classSortable + ' [' + Grid.options.attrNcol + ']',Grid.options.attrNcol);
			Grid.colsResizable($(Grid.options.classSortable + ' ' + Grid.options.classCol));
			$('[data-option=' + Grid.options.screen + ']').trigger('click');
			Grid.sortable(Grid.options.classSortable + ' .grid-row > .grid-inner',
				{
					items: '> ' + Grid.options.classCol,
					placeholder: 'sortable-placeholder-col',
					start: function( event, ui ){
						$( '.sortable-placeholder-col', event.target ).width( $(ui.item).width() );
					}
				}
			);
			Grid.sortable(Grid.options.classCol + ' > .grid-inner',
				{
					items: '> .grid-row',
					handle: '.grid-tool-sortable',
					placeholder: 'sortable-placeholder'
				}
			);
		});
	}

	Grid.prototype.addComponent = function(obj)
	{
		var $this = $(obj),
				data = $this.closest('.grid-depth-level'),
				data_ncol = data.attr(this.options.attrNcol),
				template = $('select[name=template]').val();

		if(data_ncol === undefined || data_ncol == '') {
			data_ncol = data.attr(this.options.attrRow);
			if(data_ncol === undefined || data_ncol == '') {
				return false;
			}
		}

		if(template == '' || this.options.folderTemplate == '') {
			alert('<?php echo Loc::getMessage("SETTINGS_TEMPLATE_SAVE") ?>');
			return false;
		}

		var dialog = (new BX.CAdminDialog(
			{
				'content_url':'/bitrix/admin/public_file_edit.php?bxpublic=Y&from=main.include&template=file_inc.php&path=/' + this.options.folderTemplate + '/' + template + '/include_areas/area' + data_ncol + '.php&back_url='+location.href,
				'width':'770',
				'height':'470',
			}
		));
		dialog.Show();

		$(document).on('submit','form[name=editor_form]',function(){
			$this = $(this);
			$this.find('[name="back_url"]').val(location.href);
			$('.grid-button.save').click();
			<?php if(LANG_CHARSET == 'UTF-8'): ?>
			setTimeout(function(){
				$.ajax({
				method: "POST",
				url: $this.attr('action'),
				data: $this.serialize()
			}).done(function() {
				CloseWaitWindow();
				dialog.Close(true);
			});
			},1000);
			return false;
			<?php endif; ?>
		  
		});

	}

	Grid.prototype.editCol = function(obj)
	{
		var $this = $(obj),
				data = $this.closest('.grid-depth-level'),
				template = $('select[name=template]').val(),
				site_lid = $('select[name=site_lid]').val(),
				data_ncol = data.attr(this.options.attrNcol),
				btn_save = {
		   title: BX.message('JS_CORE_WINDOW_SAVE'),
		   id: 'savebtn',
		   name: 'savebtn',
		   className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
		   action: function () {
			   ShowWaitWindow();
				var edit_form_block = $( 'form[name=edit_form_block]' );
			   $.ajax({
					type: 'POST',
					url: edit_form_block.attr('action'),
					data: edit_form_block.serialize(),
				}).done(function(response) {
					CloseWaitWindow();
					top.BX.WindowManager.Get().AllowClose(); 
					top.BX.WindowManager.Get().Close();
				});
		   }
		};
	
		if(data_ncol === undefined || data_ncol == '') {
			data_ncol = data.attr(this.options.attrRow);
			if(data_ncol === undefined || data_ncol == '') {
				return false;
			}
		}

		if(template == '') {
			alert('<?php echo Loc::getMessage("SETTINGS_TEMPLATE_SAVE") ?>');
			return false;
		}
	
		(new BX.CAdminDialog(
			{
				'title': '<?php echo Loc::getMessage("POPUP_SETTINGS_BLOCK") ?>',
				'content_url':'/bitrix/admin/un_edit_block_ajax.php?ncol='+data_ncol + '&template=' + template + '&site_lid=' + site_lid,
				'width':'770',
				'height':'470',
				'buttons': [btn_save, BX.CDialog.btnCancel]
			}
		)).Show();
	}

	Grid.prototype.designIn = function(obj)
	{
		var $this = $(obj);
		$this.closest('ul').find('a').removeClass('active');
		$this.addClass('active');
		this.options.screen = $this.data('option');
		this.updateColWidthByScreen();
	}

	var G = new Grid({
		'folderTemplate': '<?php echo $options['folderTemplate'] ?>',
	});

	$(document).ready(function() {
		var issetTemplate = false;
		$(document).on('click', '#tab_cont_un_home_settings', function() {
			if(issetTemplate == false) {
				G.setup();
				$('select[name=template]').trigger('change');
				issetTemplate = true;
			}
		});
		<?if(isset($_REQUEST['template'])):?>
		if($('#un_home_settings').is(':visible')) {
			$('#tab_cont_un_home_settings').trigger('click');
		}
		<?endif?>
	});
	function change_site( sid ) {
		window.location.href = '<?= $APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam() ?>' + '&site_lid=' + sid ;
	}
	</script><?
$tabControl->Buttons();
	?><input class="adm-btn-save" type="submit" name="Update" value="<?php echo Loc::getMessage('UN_BTN_SAVE')?>" /><?
	?><input type="hidden" name="Update" value="Y" /><?
$tabControl->End();
?></form><?