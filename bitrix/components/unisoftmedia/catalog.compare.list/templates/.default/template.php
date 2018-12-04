<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Localization\Loc;

$itemCount = count($arResult);
$isAjax = (isset($_REQUEST["ajax_action"]) && $_REQUEST["ajax_action"] == "Y");
$idCompareCount = 'compareList'.$this->randString();
$obCompare = 'ob'.$idCompareCount;
$idCompareTable = $idCompareCount.'_tbl';
$idCompareRow = $idCompareCount.'_row_';
$idCompareAll = $idCompareCount.'_count';
$mainClass = 'bx_catalog-compare-list';
$style = ($itemCount == 0 ? ' style="display: none;"' : '');
?><div id="<?php echo $idCompareCount; ?>" class="<?php echo $mainClass; ?> animated slideOutLeft"<?php echo $style; ?>><?
unset($style, $mainClass);
if ($isAjax)
{
	$APPLICATION->RestartBuffer();
}
$frame = $this->createFrame($idCompareCount)->begin('');
if (!empty($arResult))
{
?>
<a data-toggle="dropdown" href="javascript:void(0)" role="button" class="btn btn-primary compare-list-button"><div><?php echo Loc::getMessage("CATALOG_COMPARE_ELEMENTS")?> (<span id="<?php echo $idCompareAll; ?>"><?php echo $itemCount; ?></span>)</div></a>
<div class="bx_catalog_compare_form dropdown-menu">
	<div class="panel-body">
		<div id="<?php echo $idCompareTable; ?>" class="compare-items">
		<?
			foreach($arResult as $arElement)
			{
				?><div class="compare-item" id="<?php echo $idCompareRow.$arElement['PARENT_ID']; ?>">
					<div class="row">
						<div class="col-xs-4">
							<a class="image" data-id="<?php echo $arElement['PARENT_ID']; ?>" href="javascript:void(0);" title="<?php echo Loc::getMessage("CATALOG_DELETE")?>">
								<?if(is_array($arElement['PREVIEW_PICTURE'])):?>
									<img class="img-thumbnail" src="<?php echo $arElement['PREVIEW_PICTURE']['SRC'] ?>" width="<?php echo $arElement['PREVIEW_PICTURE']['WIDTH'] ?>" height="<?php echo $arElement['PREVIEW_PICTURE']['HEIGHT'] ?>" />
								<?elseif(is_array($arElement['DETAIL_PICTURE'])):?>
									<img class="img-thumbnail" src="<?php echo $arElement['DETAIL_PICTURE']['SRC'] ?>" width="<?php echo $arElement['DETAIL_PICTURE']['WIDTH'] ?>" height="<?php echo $arElement['DETAIL_PICTURE']['HEIGHT'] ?>" />
								<?else:?>
									<img class="img-thumbnail" src="<?=$this->GetFolder().'/images/no_photo.png'?>" />
								<?endif?>
							</a>
						</div>
						<div class="col-xs-8">
							<a class="compare-name" href="<?php echo $arElement["DETAIL_PAGE_URL"]?>"><?php echo $arElement["NAME"]?></a>
						</div>
					</div>
				</div><?
			}
		?>
		</div>
	</div>
<?if ($itemCount > 1)
{
	?><div class="panel-footer">
		<a class="btn btn-default" href="<?php echo $arParams["COMPARE_URL"]; ?>"><?php echo GetMessage('CP_BCCL_TPL_MESS_COMPARE_PAGE'); ?></a>
	</div><?
}?>
</div>
<?
}
$frame->end();
if ($isAjax)
{
	die();
}
$currentPath = CHTTP::urlDeleteParams(
	$APPLICATION->GetCurPageParam(),
	array(
		$arParams['PRODUCT_ID_VARIABLE'],
		$arParams['ACTION_VARIABLE'],
		'ajax_action'
	),
	array("delete_system_params" => true)
);

$jsParams = array(
	'VISUAL' => array(
		'ID' => $idCompareCount,
	),
	'AJAX' => array(
		'url' => $currentPath,
		'params' => array(
			'ajax_action' => 'Y'
		),
		'templates' => array(
			'delete' => (strpos($currentPath, '?') === false ? '?' : '&').$arParams['ACTION_VARIABLE'].'=DELETE_FROM_COMPARE_LIST&'.$arParams['PRODUCT_ID_VARIABLE'].'='
		)
	)
);
?></div>
<script type="text/javascript">
var <? echo $obCompare; ?> = new JCCatalogCompareList(<? echo CUtil::PhpToJSObject($jsParams, false, true); ?>)
</script>