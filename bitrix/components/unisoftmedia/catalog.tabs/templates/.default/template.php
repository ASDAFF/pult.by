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
$this->setFrameMode(true);
?>

<?if(!empty($arResult['ITEMS']))
{
	$componentId = $component->getComponentId();

	$catalogTabsId = "catalogTabs".$component->getNextNumber();

	$arParams['catalogTabsId'] = $catalogTabsId ;

	$arTabs = array_slice($arResult['ITEMS'], 0, 1);
	?>
	<section class="catalog-tabs" id="<?=$catalogTabsId?>">
		<!-- Nav tabs -->
		<ul class="nav nav-pills" role="tablist">
			<?foreach ($arResult['ITEMS'] as $key => $tab):?>
		    <li role="presentation" data-code="<?php echo $key ?>" class="<?=($arTabs[$key])? 'active' : 'empty' ?>">
		    	<a href="#catalog-tab-<?=$componentId?>-<?=strtolower($key)?>" role="tab" data-toggle="tab"><?php echo $tab ?></a>
		    </li>
	  	<?endforeach?>
	  </ul>
	  <!-- Tab panes -->
	  <div class="tab-content">
	  	<?foreach ($arResult['ITEMS'] as $key => $tab):?>
	  		<?if($arTabs[$key]):?>
			  	<div role="tabpanel" class="tab-pane active" id="catalog-tab-<?=$componentId?>-<?=strtolower($key)?>">
			  		<?require(realpath(dirname(__FILE__)).'/ajax_template.php');?>
			  	</div>
		  	<?else:?>
		  	<div role="tabpanel" class="tab-pane" id="catalog-tab-<?=$componentId?>-<?=strtolower($key)?>"></div>
		  	<?endif?>
		  <?endforeach?>
	  </div>
	  <script>
			var <?=$catalogTabsId?> = new CCatalogTabs;
	  	<?=$catalogTabsId?>.siteId       	= '<?=SITE_ID?>';
			<?=$catalogTabsId?>.catalogTabsId = '<?=$catalogTabsId?>';
			<?=$catalogTabsId?>.ajaxPath     	= '<?=$componentPath?>/ajax.php';
			<?=$catalogTabsId?>.templateName 	= '<?=$templateName?>';
			<?=$catalogTabsId?>.arParams     	=  <?=CUtil::PhpToJSObject($arParams)?>;
			<?=$catalogTabsId?>.activate();
	  </script>
	</section>
<?}?>