<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (CModule::IncludeModule("catalog"))
{
	$arSKU = false;
	$boolSKU = false;
	$arOfferProps = array();

	// get iblock props from all catalog iblocks including sku iblocks
	$arSkuIblockIDs = array();
	$dbCatalog = CCatalog::GetList(array(), array());
	while ($arCatalog = $dbCatalog->GetNext())
	{
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCatalog['IBLOCK_ID']);

		if (!empty($arSKU) && is_array($arSKU))
		{
			$arSkuIblockIDs[] = $arSKU["IBLOCK_ID"];
			$arSkuData[$arSKU["IBLOCK_ID"]] = $arSKU;

			$boolSKU = true;
		}
	}

	// iblock props
	$arProps = array();
	foreach ($arSkuIblockIDs as $iblockID)
	{
		$dbProps = CIBlockProperty::GetList(
			array(
				"SORT"=>"ASC",
				"NAME"=>"ASC"
			),
			array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "N",
			)
		);

		while ($arProp = $dbProps->GetNext())
		{
			if ($arProp['ID'] == $arSkuData[$arSKU["IBLOCK_ID"]]["SKU_PROPERTY_ID"])
				continue;

			if ($arProp['XML_ID'] == 'CML2_LINK')
				continue;

			$strPropName = '['.$arProp['ID'].'] '.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];

			if ($arProp['PROPERTY_TYPE'] != 'F')
			{
				$arOfferProps[$arProp['CODE']] = $strPropName;
			}
		}

		if (!empty($arOfferProps) && is_array($arOfferProps))
		{
			$arTemplateParameters['OFFERS_PROPS'] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => GetMessage('SALE_PROPERTIES_RECALCULATE_BASKET'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'SIZE' => '7',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arOfferProps
			);
		}

		$arTemplateParameters['USE_CAROUSEL'] = array(
			'PARENT' => 'GIFTS',
			'NAME' => GetMessage('USE_CAROUSEL'),
			'TYPE' => 'CHECKBOX',
			"REFRESH" => "Y",
			'DEFAULT' => 'N',
		);
		$arListResponsiveCarousel = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);
		$arListResponsive = array(
			'12' => '1',
			'6' => '2',
			'4' => '3',
			'3' => '4'
		);
		if (isset($arCurrentValues['USE_CAROUSEL']) && $arCurrentValues['USE_CAROUSEL'] == 'Y')
		{
			$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_LG'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_LG'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsiveCarousel,
				'DEFAULT' => '5',
			);
			$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_MD'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_MD'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsiveCarousel,
				'DEFAULT' => '4',
			);
			$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_SM'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_SM'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsiveCarousel,
				'DEFAULT' => '3',
			);
			$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_XS'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_XS'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsiveCarousel,
				'DEFAULT' => '2',
			);
			$arTemplateParameters['RESPONSIVE_CAROUSEL_ITEMS_MOBILE'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_CAROUSEL_ITEMS_MOBILE'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsiveCarousel,
				'DEFAULT' => '2',
			);
			$arTemplateParameters['SLIDER_AUTOPLAY'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_AUTOPLAY'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			);
			$arTemplateParameters['SLIDER_SPEED'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_SPEED'),
				'TYPE' => 'STRING',
				'DEFAULT' => '1000',
			);
			$arTemplateParameters['SLIDER_SHOW_SPEED'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_SHOW_SPEED'),
				'TYPE' => 'STRING',
				'DEFAULT' => '8000',
			);
			$arTemplateParameters['SLIDER_AUTOPLAY_HOVER_PAUSE'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_AUTOPLAY_HOVER_PAUSE'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			);
			$arTemplateParameters['MOUSE_DRAG'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('MOUSE_DRAG'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
			);		
			$arTemplateParameters['SLIDER_NAV'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_NAV'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			);
			$arTemplateParameters['SLIDER_LOOP'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('SLIDER_LOOP'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			);
		}
		else
		{
			$arTemplateParameters['RESPONSIVE_ITEMS_LG'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_ITEMS_LG'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsive,
				'DEFAULT' => '3',
			);
			$arTemplateParameters['RESPONSIVE_ITEMS_MD'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_ITEMS_MD'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsive,
				'DEFAULT' => '3',
			);
			$arTemplateParameters['RESPONSIVE_ITEMS_SM'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_ITEMS_SM'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsive,
				'DEFAULT' => '4',
			);
			$arTemplateParameters['RESPONSIVE_ITEMS_XS'] = array(
				'PARENT' => 'GIFTS',
				'NAME' => GetMessage('RESPONSIVE_ITEMS_XS'),
				'TYPE' => 'LIST',
				'VALUES' => $arListResponsive,
				'DEFAULT' => '6',
			);
		}
	}
}
$arTemplateParameters['MAX_WIDTH'] = array(
    "NAME" => GetMessage("MAX_WIDTH"),
	'PARENT' => 'GIFTS',
	"TYPE" => "STRING",
	'DEFAULT' => '300',
);
$arTemplateParameters['MAX_HEIGHT'] = array(
    "NAME" => GetMessage("MAX_HEIGHT"),
	'PARENT' => 'GIFTS',
	"TYPE" => "STRING",
	'DEFAULT' => '300',
);