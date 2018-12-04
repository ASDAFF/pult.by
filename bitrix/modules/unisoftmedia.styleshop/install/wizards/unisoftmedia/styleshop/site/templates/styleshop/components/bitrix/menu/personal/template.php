<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

$this->setFrameMode(true);

if(!empty($arResult)):

	$previousLevel = 0;
	?>
	<nav class="leftmenu block hidden-xs hidden-sm">
		<h2><?php echo Loc::getMessage('MENU_NAME') ?></h2>
		<ul class="nav nav-pills nav-stacked">
			<?foreach($arResult as $k => $arItem):
			if (!$arItem['IS_PARENT'] && $arItem['PERMISSION'] < 'D') 
				continue;

				if ($previousLevel && $arItem['DEPTH_LEVEL'] < $previousLevel) 
					echo str_repeat('</ul></li>', ($previousLevel - $arItem['DEPTH_LEVEL']));?>

				<li class="<?if ($arItem['SELECTED']):?>active<?endif?><?php echo $arItem['IS_PARENT'] ? ' parent' : '' ?>"><a title="<?=$arItem['TEXT']?>" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?

				if ($arItem['IS_PARENT']): // is_parent
					?><ul class="animated fadeIn"><?

				endif; // is_parent

				$previousLevel = $arItem['DEPTH_LEVEL'];
				
			endforeach;

			if ($previousLevel > 1)//close last item tags
				echo str_repeat('</ul></li>', ($previousLevel-1) );?>

		</ul>
	</nav>
<?endif?>