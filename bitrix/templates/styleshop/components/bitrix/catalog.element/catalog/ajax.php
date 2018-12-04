<?php

define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

use Bitrix\Main\Context,
	Bitrix\Main\Loader,
	Bitrix\Main\Type\DateTime,
	Bitrix\Currency,
	Bitrix\Catalog,
	Bitrix\Iblock;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//if($_SERVER["REQUEST_METHOD"] != "POST" || !check_bitrix_sessid())
	//die;
if(!CModule::IncludeModule("iblock"))
	return;

if(isset($_REQUEST['ajax_add2basket']) && !empty($_REQUEST['ajax_add2basket']) && isset($_REQUEST['elementId']) && 0 < intval($_REQUEST['elementId']))
{
	$elementId = intval($_REQUEST['elementId']);
	//$res = CIBlockElement::GetByID($elementId);
//SELECT
$arSelect = [
	"ID",
	"IBLOCK_ID",
	"CODE",
	"XML_ID",
	"NAME",
	"ACTIVE",
	"DATE_ACTIVE_FROM",
	"DATE_ACTIVE_TO",
	"SORT",
	"PREVIEW_TEXT",
	"PREVIEW_TEXT_TYPE",
	"DETAIL_TEXT",
	"DETAIL_TEXT_TYPE",
	"DATE_CREATE",
	"CREATED_BY",
	"TIMESTAMP_X",
	"MODIFIED_BY",
	"TAGS",
	"IBLOCK_SECTION_ID",
	"DETAIL_PAGE_URL",
	"LIST_PAGE_URL",
	"DETAIL_PICTURE",
	"PREVIEW_PICTURE",
	"PROPERTY_*",
	"CATALOG_QUANTITY"
];
$arFilter = [
	"ID" => $elementId,
];
	$arFilter = Array($arFilter);
	$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
	$arResult = [];
	while($obElement = $rsElement->GetNextElement())
	{
		$arResult = $obElement->GetFields();
		Iblock\Component\Tools::getFieldImageData(
			$arResult,
			array('PREVIEW_PICTURE', 'DETAIL_PICTURE'),
			Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);
		$arResult["PROPERTIES"] = $obElement->GetProperties();
		if (!isset($arResult["CATALOG_MEASURE_RATIO"]))
				$arResult["CATALOG_MEASURE_RATIO"] = 1;
			if (!isset($arResult['CATALOG_MEASURE']))
				$arResult['CATALOG_MEASURE'] = 0;
			$arResult['CATALOG_MEASURE'] = (int)$arResult['CATALOG_MEASURE'];
			if (0 > $arResult['CATALOG_MEASURE'])
				$arResult['CATALOG_MEASURE'] = 0;
			if (!isset($arResult['CATALOG_MEASURE_NAME']))
				$arResult['CATALOG_MEASURE_NAME'] = '';

			$rsRatios = CCatalogMeasureRatio::getList(
					array(),
					array('PRODUCT_ID' => $arResult['ID']),
					false,
					false,
					array('PRODUCT_ID', 'RATIO')
				);
			if ($arRatio = $rsRatios->Fetch())
			{
				$intRatio = (int)$arRatio['RATIO'];
				$dblRatio = doubleval($arRatio['RATIO']);
				$mxRatio = ($dblRatio > $intRatio ? $dblRatio : $intRatio);
				if (CATALOG_VALUE_EPSILON > abs($mxRatio))
					$mxRatio = 1;
				elseif (0 > $mxRatio)
					$mxRatio = 1;
				$arResult["CATALOG_MEASURE_RATIO"] = $mxRatio;
			}

			if (0 < $arResult['CATALOG_MEASURE'])
			{
				$rsMeasures = CCatalogMeasure::getList(
					array(),
					array('ID' => $arResult['CATALOG_MEASURE']),
					false,
					false,
					array('ID', 'SYMBOL_RUS')
				);
				if ($arMeasure = $rsMeasures->GetNext())
				{
					$arResult['CATALOG_MEASURE_NAME'] = $arMeasure['SYMBOL_RUS'];
					$arResult['~CATALOG_MEASURE_NAME'] = $arMeasure['~SYMBOL_RUS'];
				}
			}

			if ('' == $arResult['CATALOG_MEASURE_NAME'])
			{
				$arDefaultMeasure = CCatalogMeasure::getDefaultMeasure(true, true);
				$arResult['CATALOG_MEASURE_NAME'] = $arDefaultMeasure['SYMBOL_RUS'];
				$arResult['~CATALOG_MEASURE_NAME'] = $arDefaultMeasure['~SYMBOL_RUS'];
			}

			$arResult["PRICE_MATRIX"] = false;
			$arResult["PRICES"] = array();
			$arResult['MIN_PRICE'] = false;
			if($arParams["USE_PRICE_COUNT"])
			{
				$arResult["PRICE_MATRIX"] = CatalogGetPriceTableEx($arResult["ID"], 0, $arPriceTypeID, 'Y', $arConvertParams);
				if (isset($arResult["PRICE_MATRIX"]["COLS"]) && is_array($arResult["PRICE_MATRIX"]["COLS"]))
				{
					foreach($arResult["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arResult["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);
				}
			}
	}
}

?>

<?
if(!empty($arResult))
{
?>
	<div class="basket-small">
		<div class="col-sm-5">
			<?if(!is_array($arResult['PREVIEW_PICTURE']))
					$arResult['PREVIEW_PICTURE'] = $arResult['DETAIL_PICTURE'];?>
				<?if(!empty($arResult['PREVIEW_PICTURE'])):?>
					<div class="image">
						<?
						$arPic = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'],
												array(
													"width" => 300,
													"height" => 300
												),
												BX_RESIZE_IMAGE_PROPORTIONAL,
												true
											);?>
						<a href="<?php echo $arResult['DETAIL_PAGE_URL'] ?>" title="<?php echo $arResult["NAME"]; ?>">
							<img class="img-thumbnail" src="<?php echo $arPic['src'] ?>" alt="<?php echo $arResult["NAME"]; ?>" />
						</a>
					</div>
				<?endif?>
		</div>
		<div class="col-sm-7">
			<div class="name">
				<a href="<?php echo $arResult['DETAIL_PAGE_URL'] ?>"><?php echo $arResult["NAME"]; ?></a>
			</div>
		</div>
	</div>
<?
}