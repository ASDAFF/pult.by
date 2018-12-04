<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$COLOR_ID = $_SESSION["ESHOP_HBLOCK_COLOR_ID"];
unset($_SESSION["ESHOP_HBLOCK_COLOR_ID"]);

$BRAND_ID = $_SESSION["ESHOP_HBLOCK_BRAND_ID"];
unset($_SESSION["ESHOP_HBLOCK_BRAND_ID"]);

$SIZECLOTHES_ID = $_SESSION["ESHOP_HBLOCK_SIZECLOTHES_ID"];
unset($_SESSION["ESHOP_HBLOCK_SIZECLOTHES_ID"]);

$SIZESHOES_ID = $_SESSION["ESHOP_HBLOCK_SIZESHOES_ID"];
unset($_SESSION["ESHOP_HBLOCK_SIZESHOES_ID"]);

$TABLESIZE_ID = $_SESSION["ESHOP_HBLOCK_TABLESIZE_ID"];
unset($_SESSION["ESHOP_HBLOCK_TABLESIZE_ID"]);

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if ($COLOR_ID)
{
	$hldata = HL\HighloadBlockTable::getById($COLOR_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	
	$arColors = array(
		"PURPLE" => "references_files/purple.jpg",
		"BROWN" => "references_files/brown.jpg",
		"SEE" => "references_files/see.jpg",
		"BLUE" => "references_files/blue.jpg",
		"RED" => "references_files/red.jpg",
		"GREEN" => "references_files/green.jpg",
		"WHITE" => "references_files/white.jpg",
		"BLACK" => "references_files/black.jpg",
		"PINK" => "references_files/pink.jpg",
		"AZURE" => "references_files/azure.jpg",
		"GREENTWO" => "references_files/greentwo.jpg",
		"GRAY" => "references_files/gray.jpg",
		"BERRY" => "references_files/berry.jpg",
		"GOLD" => "references_files/gold.jpg",
		"YELLOW" => "references_files/yellow.jpg",
	);
	$sort = 0;
	foreach($arColors as $colorName=>$colorFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_COLOR_".$colorName),
			'UF_FILE' =>
				array (
					'name' => ToLower($colorName).".jpg",
					'type' => 'image/jpeg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$colorFile
				),
			'UF_DESCRIPTION' => GetMessage("WZD_REF_COLOR_DESCR_".$colorName),
			'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_COLOR_FULL_DESCR_".$colorName),
      'UF_LINK' => GetMessage("WZD_REF_COLOR_LINK_".$colorName),
			'UF_SORT' => $sort,
			'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => ToLower($colorName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$COLOR_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$COLOR_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

if ($BRAND_ID)
{
	$hldata = HL\HighloadBlockTable::getById($BRAND_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arBrands = array(
		"INCITY" => "brands_files/incity.jpg",
		"ADIDAS" => "brands_files/adidas.jpg",
		"NIKE" => "brands_files/nike.jpg",
		"ASICS" => "brands_files/asics.jpg",
		"BETSY" => "brands_files/betsy.jpg",
		"BIKKEMBERGS" => "brands_files/bikkembergs.jpg",
		"BOSSGREEN" => "brands_files/bossgreen.jpg",
    "BOSSORANGE" => "brands_files/bossorange.jpg",
		"BOTTICELLILIMITED" => "brands_files/botticellilimited.jpg",
		"BRACCIALINI" => "brands_files/braccialini.jpg",
		"CROCS" => "brands_files/crocs.jpg",
		"CROSBY" => "brands_files/crosby.jpg",
		"DIESEL" => "brands_files/diesel.jpg",
		"GRANDSTYLE" => "brands_files/grandstyle.jpg",
    "GUESS" => "brands_files/guess.jpg",
		"CANOE" => "brands_files/canoe.jpg",
		"INARIO" => "brands_files/inario.jpg",
		"KEDDO" => "brands_files/keddo.jpg",
		"KIRAPLASTININA" => "brands_files/kiraplastinina.jpg",
		"LACOSTE" => "brands_files/lacoste.jpg",
		"LAMANIA" => "brands_files/lamania.jpg",
    "LEVIS" => "brands_files/levis.jpg",
		"MANGO" => "brands_files/mango.jpg",
		"MCCRAIN" => "brands_files/mccrain.jpg",
		"ICEWATCH" => "brands_files/icewatch.jpg"
	);
	$sort = 0;
	foreach($arBrands as $brandName=>$brandFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_BRAND_".$brandName),
			'UF_FILE' =>
				array (
					'name' => ToLower($brandName).".jpg",
					'type' => 'image/jpeg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$brandFile
				),
			'UF_SORT' => $sort,
			'UF_DESCRIPTION' => GetMessage("WZD_REF_BRAND_DESCR_".$brandName),
			'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_BRAND_FULL_DESCR_".$brandName),
      'UF_LINK' => GetMessage("WZD_REF_BRAND_LINK_".$brandName),
			'UF_XML_ID' => ToLower($brandName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$BRAND_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$BRAND_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

if ($SIZECLOTHES_ID)
{
	$hldata = HL\HighloadBlockTable::getById($SIZECLOTHES_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arSizeClothes = array(
		"XS" => "5Rxyqd6g",
		"S" => "cI67mZCX",
		"M" => "o8TnfqwN",
		"L" => "JeQeapBE",
		"XL" => "TKQuXMyq",
		"XXL" => "oNZ4J7Jh",
		"XXXL" => "PdIlXrTV"
	);
	$sort = 0;
	foreach($arSizeClothes as $k => $v)
	{
		$sort += 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_SIZECLOTHES_".$k),
			'UF_SORT' => $sort,
			'UF_DESCRIPTION' => GetMessage("WZD_REF_SIZECLOTHES_DESCR_".$k),
			'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_SIZECLOTHES_FULL_DESCR_".$k),
      'UF_LINK' => GetMessage("WZD_REF_SIZECLOTHES_LINK_".$k),
			'UF_XML_ID' => $v
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$SIZECLOTHES_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$SIZECLOTHES_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

if ($SIZESHOES_ID)
{
	$hldata = HL\HighloadBlockTable::getById($SIZESHOES_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arSizeShoes = array(
		"36" => "y5uC5kjp",
		"37" => "wfsUQd5Y",
		"38" => "yd3ow1uo",
		"39" => "MNp47NIi",
		"40" => "Sq1lg6g6",
		"41" => "RQ6dMnRu"
	);
	$sort = 0;
	foreach($arSizeShoes as $k => $v)
	{
		$sort += 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_SIZESHOES_".$k),
			'UF_SORT' => $sort,
			'UF_DESCRIPTION' => GetMessage("WZD_REF_SIZESHOES_DESCR_".$k),
			'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_SIZESHOES_FULL_DESCR_".$k),
      'UF_LINK' => GetMessage("WZD_REF_SIZESHOES_LINK_".$k),
			'UF_XML_ID' => $v
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$SIZESHOES_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$SIZESHOES_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

if ($TABLESIZE_ID)
{
	$hldata = HL\HighloadBlockTable::getById($TABLESIZE_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arSizeShoes = array(
		"WOMENCLOTHES" => "QbZ3wzAw",
		"WOMENSHOES" => "oZbJuTbf",
		"HOWCLOTHES" => "bDRPEqXR",
		"HOWSHOES" => "QwwEixgn",
		"HOWPANTS" => "8n1hk5GD",
		"WOMENPANTS" => "WFed7sup",
		"WOMENUNDERWEAR" => "5m4wqaNA",
		"HOWUNDERWEAR" => "YShzsfWy",
		"HOWCLOTHESKIDS" => "Jzp8KMUd",
		"HOWSHOESKIDS" => "KThJyqL6"
	);
	$sort = 0;
	foreach($arSizeShoes as $k => $v)
	{
		$sort += 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_TABLESIZE_".$k),
			'UF_SORT' => $sort,
			'UF_DESCRIPTION' => GetMessage("WZD_REF_TABLESIZE_DESCR_".$k),
			'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_TABLESIZE_FULL_DESCR_".$k),
      'UF_LINK' => GetMessage("WZD_REF_TABLESIZE_LINK_".$k),
			'UF_XML_ID' => $v
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$TABLESIZE_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$TABLESIZE_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}