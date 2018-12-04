<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if(empty($arResult["BRAND_BLOCKS"]))
	return;

?><div class="tablesize"><?
    foreach ($arResult["BRAND_BLOCKS"] as $arBB)
    {
        ?><h2><?php echo $arBB['NAME'] ?></h2><?
        echo str_replace('%template%', SITE_TEMPLATE_ID, $arBB['DESCRIPTION']);
    }
?></div><?