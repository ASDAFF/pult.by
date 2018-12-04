<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
use Bitrix\Main\Localization\Loc;

$bxajaxid = $component->getBxAjaxId();
$recallId = "recall".$component->getIdForm();
?>
<?if($arParams['POPUP_FORM'] != 'Y' || $arParams['AJAX'] == 'Y' && $arParams['POPUP_FORM'] == 'Y' && isset($_REQUEST['form_popup']) && $_REQUEST['form_popup'] == $bxajaxid):?>
	<?if($arParams['POPUP_FORM'] != 'Y'):?>
		<div class="row">
	<?endif?>
		<div class="<?if($arParams['POPUP_FORM'] == 'Y'):?>col-xs-12<?else:?>col-sm-8 col-md-7 col-lg-6<?endif?>">
			<?if($arParams['MESS_TITLE'] && $arParams['POPUP_FORM'] != 'Y'):?>
				<h2><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></h2>
			<?endif?>
			<div id="<?php echo $recallId ?>">
				<?require(realpath(dirname(__FILE__)).'/ajax_template.php');?>
			</div>
		</div>
	<?if($arParams['POPUP_FORM'] != 'Y'):?>
		</div>
	<?endif?>
<?else:?>
<a class="ajax-recall" data-ajax-container="#<?php echo $recallId ?>" data-form-popup="<?=$bxajaxid?>" data-form-title="<?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?>" data-form-action="<?=POST_FORM_ACTION_URI?>" href="#"><?php echo Loc::getMessage("MFT_RECALL_LINK") ?></a>
<?endif?>