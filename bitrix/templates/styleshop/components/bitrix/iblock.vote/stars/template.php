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
if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$votesValue = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$votesValue = 0;
}
else
{
	$votesValue = intval($arResult["PROPERTIES"]["rating"]["VALUE"]);
}

$votesCount = intval($arResult["PROPERTIES"]["vote_count"]["VALUE"]);

if(isset($arParams["AJAX_CALL"]) && $arParams["AJAX_CALL"]=="Y")
{
	$APPLICATION->RestartBuffer();

	die(json_encode( array(
		"value" => $votesValue,
		"votes" => $votesCount
		)
	));
}

CJSCore::Init(array("ajax"));
$strObName = "bx_vo_".$arParams["IBLOCK_ID"]."_".$arParams["ELEMENT_ID"].'_'.$this->randString();
$arJSParams = array(
	"progressId" => $strObName."_progr",
	"ratingId" => $strObName."_rating",
	"starsId" => $strObName."_stars",
	"ajaxUrl" => $componentPath."/component.php",
	"voteId" => $arResult["ID"],
);
$templateData = array(
	'JS_OBJ' => $strObName,
	'ELEMENT_ID' => $arParams["ELEMENT_ID"]
);
?><table class="bx_item_detail_rating">
	<tr>
		<td>
			<div class="bx_item_rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
				<meta itemprop="bestRating" content="5">
				<meta itemprop="ratingValue" content="<?php echo (isset($arResult['PROPERTIES']['rating']['VALUE']) && $arResult['PROPERTIES']['rating']['VALUE'] )? $arResult['PROPERTIES']['rating']['VALUE'] : 0 ?>">
				<meta itemprop="ratingCount" content="<?php echo (isset($arResult['PROPERTIES']['vote_count']['VALUE']) && $arResult['PROPERTIES']['vote_count']['VALUE']) ? $arResult['PROPERTIES']['vote_count']['VALUE'] : 0 ?>">
				<div class="bx_stars_container">
					<div id="<?=$arJSParams["starsId"]?>" class="bx_stars_bg"></div>
					<div id="<?=$arJSParams["progressId"]?>" class="bx_stars_progres"></div>
				</div>
			</div>
		</td>
		<td>
			<span id="<?=$arJSParams["ratingId"]?>" class="bx_stars_rating_votes">( 0 )</span>
		</td>
	</tr>
</table>
<script type="text/javascript">
BX.ready(function(){
	window.<?=$strObName;?> = new JCIblockVoteStars(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);

	window.<?=$strObName?>.ajaxParams = <?=$arResult["AJAX_PARAMS"]?>;
	window.<?=$strObName?>.setValue("<?=$votesCount > 0 ? ($votesValue+1)*20 : 0?>");
	window.<?=$strObName?>.setVotes("<?=$votesCount?>");
});
</script>