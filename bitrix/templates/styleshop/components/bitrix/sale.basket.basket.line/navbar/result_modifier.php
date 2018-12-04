<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$in_basket = array();

if(is_array($arResult['CATEGORIES']['READY']) && !empty($arResult['CATEGORIES']['READY']))
{
    foreach($arResult['CATEGORIES']['READY'] as $k => $arBasketItems)
    {
        $in_basket[$arBasketItems['PRODUCT_ID']] = $arBasketItems['PRODUCT_ID'];
		
		$propsIterator = CSaleBasket::GetPropsList(
			array('BASKET_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC'),
			array('BASKET_ID' => $arBasketItems['ID'])
		);
		while ($property = $propsIterator->GetNext())
		{
			$property['CODE'] = (string)$property['CODE'];
			if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID')
				continue;
			$arResult['CATEGORIES']['READY'][$k]['PROPS'][] = $property;
		}
		
    }
}
$arResult['IN_BASKT'] = $in_basket;