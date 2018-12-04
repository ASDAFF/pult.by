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

if (empty($arResult["BRAND_BLOCKS"]))
	return;

?><div class="brand-element"><?

foreach ($arResult["BRAND_BLOCKS"] as $blockId => $arBB)
{
	$useLink = $arBB['LINK'] !== false;
	if ($useLink)
		$arBB['LINK'] = htmlspecialcharsbx($arBB['LINK']);

	if($arBB['TYPE'] == 'ONLY_PIC' || $arBB['TYPE'] == 'PIC_TEXT')
	{
		if($useLink)
			echo '<a href="'.str_replace('//', '/', SITE_DIR.'brands/'.strtolower($arBB['LINK']).'/').'">';

		?><img src="<?php echo $arBB['PICT']['SRC'] ?>" alt="<?php echo htmlspecialcharsbx($arBB['NAME']) ?>" /><?

		if ($useLink)
			echo '</a>';
	}
}
?>
	</div>