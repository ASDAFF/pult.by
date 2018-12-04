<?php
IncludeModuleLangFile(__FILE__);

class CBIblockMegaMetaTags{

	public static $iBlockTypeId='CEO_Tags';
	public static $iBlockCode='ceo';
	public static $proph1Code='H1';
	public static $propKeyWordsCode='KEYWORDS';
	public static $propDescriptionCode='DESCRIPTION';
	public static $propTitleCode='TITLE';
	public static $propTargetOnCode='TARGET_ON';
	public static $propTargetOffCode='TARGET_OFF';
	public static $propAllKeysCode='ALL_KEYS';
	public static $propRobotsCode = "ROBOTS";
	public static $propIndexCode = "INDEX";
	public static $propFollowCode = "FOLLOW";
	public static $moduleID='alexkova.megametatags';

	public function createIBLockType(){
		IncludeModuleLangFile(__FILE__);
		//create IB type
		global $DB,$APPLICATION;
		CModule::IncludeModule('iblock');

		$db_iblock_type = CIBlockType::GetList(array(),array('ID'=>self::$iBlockTypeId));
		if($ar_iblock_type = $db_iblock_type->Fetch())
		{
			$IBlockTypeID = $ar_iblock_type['ID'];
		}

		if (!$IBlockTypeID){
			$arFields = Array(
				'ID'=>self::$iBlockTypeId,
				'SECTIONS'=>'N',
				'IN_RSS'=>'N',
				'SORT'=>500,
				'LANG'=>Array(
					'en'=>Array(
						'NAME'=>self::$iBlockTypeId,
						'SECTION_NAME'=>'',
						'ELEMENT_NAME'=>"Tag's rule"
						),
					'ru'=>Array(
						'NAME'=>getMessage('IBLOCK_TYPE_NAME'),
						'SECTION_NAME'=>'',
						'ELEMENT_NAME'=>getMessage('IBLOCK_TYPE_ELEMENT_NAME')
						)
					)
				);
			$obBlockType = new CIBlockType;
			$DB->StartTransaction();

			$res = $obBlockType->Add($arFields);
			if(!$res)
			{
				$DB->Rollback();
				$APPLICATION->throwException($obBlockType->LAST_ERROR);
				return false;
			}
			else{
				$DB->Commit();
				$IBlockTypeID = self::$iBlockTypeId;
			}
		}

		return $IBlockTypeID;
	}

	public function createProperties($iblockID){
		if (!$iblockID){
			return false;
		}

		CModule::IncludeModule('iblock');

		$res = CIBlock::GetProperties($iblockID);
		$arProps = array();
		while($res_arr = $res->Fetch()){
			if ($res_arr['CODE'])
				$arProps[] = $res_arr['CODE'];
		}

		$iblockproperty = new CIBlockProperty;

		//h1 mask
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_H1'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$proph1Code,
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		//keywords mask
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_KEYWORDS'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$propKeyWordsCode,
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		//description mask
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_DESCRIPTION'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$propDescriptionCode,
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		//title mask
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_TITLE'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$propTitleCode,
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		//target off
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_TARGET_OFF'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$propTargetOffCode,
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "HTML",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		//target on
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_TARGET_ON'),
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => self::$propTargetOnCode,
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "HTML",
			"IBLOCK_ID" => $iblockID,
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		 //all keys in rule
		$arFields = Array(
			"NAME" => getMessage('PROPERTY_ALL_KEYS'),
			"ACTIVE" => "Y",
			"SORT" => "1500",
			"CODE" => self::$propAllKeysCode,
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblockID,
			"MULTIPLE" => "Y",
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		$arFields = Array(
			"NAME" => getMessage("PROPERTY_ROBOTS"),
			"ACTIVE" => "Y",
			"SORT" => "600",
			"MULTIPLE" => "N",
			"IS_REQUIRED" => "N",
			"USER_TYPE" => "",
			"CODE" => self::$propRobotsCode,
			"PROPERTY_TYPE" => "L",
			"LIST_TYPE" => "C",
			"IBLOCK_ID" => $iblockID,
		);

		$arFields["VALUES"][0] = Array(
				"VALUE" => "Y",
				"XML_ID" => "rewrite_robots",
				"DEF" => "N",
				"SORT" => "100"
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		$arFields = Array(
			"NAME" => "INDEX",
			"ACTIVE" => "Y",
			"SORT" => "610",
			"MULTIPLE" => "N",
			"IS_REQUIRED" => "N",
			"USER_TYPE" => "",
			"CODE" => self::$propIndexCode,
			"PROPERTY_TYPE" => "L",
			"IBLOCK_ID" => $iblockID,
		);

		$arFields["VALUES"][] = Array(
			"VALUE" => "index",
			"XML_ID" => "rewrite_index_y",
			"DEF" => "Y",
			"SORT" => "100"
		);
		$arFields["VALUES"][] = Array(
			"VALUE" => "noindex",
			"XML_ID" => "rewrite_index_no",
			"DEF" => "N",
			"SORT" => "100"
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		$arFields = Array(
			"NAME" => "FOLLOW",
			"ACTIVE" => "Y",
			"SORT" => "620",
			"MULTIPLE" => "N",
			"IS_REQUIRED" => "N",
			"USER_TYPE" => "",
			"CODE" => self::$propFollowCode,
			"PROPERTY_TYPE" => "L",
			"IBLOCK_ID" => $iblockID,
		);

		$arFields["VALUES"][] = Array(
			"VALUE" => "follow",
			"XML_ID" => "rewrite_follow_y",
			"DEF" => "Y",
			"SORT" => "100"
		);
		$arFields["VALUES"][] = Array(
			"VALUE" => "nofollow",
			"XML_ID" => "rewrite_follow_no",
			"DEF" => "N",
			"SORT" => "100"
		);

		if (!in_array($arFields["CODE"],$arProps)){
			if (!$iblockproperty->Add($arFields)){
				return false;
			}
		}

		 return true;
	}

	//return IBlockID (new/exist)
	public function createIBlock(){
		CModule::IncludeModule('iblock');

		$iblockTypeId = self::createIBLockType();

		if ($iblockTypeId){
			$res = CIBlock::GetList(
				Array(),
				Array(
					'TYPE'=>$iblockTypeId,
					'SITE_ID'=>"s1",
					"CODE"=>self::$iBlockCode
				),false
			);
			if($ar_res = $res->Fetch())
			{
				$iblockID = $ar_res['ID'];
			}

			if(!$iblockID){
				$ib = new CIBlock;
				$arFields = Array(
				  "ACTIVE" => "Y",
				  "NAME" => getMessage('IBLOCK_NAME'),
				  "CODE" => self::$iBlockCode,
				  "IBLOCK_TYPE_ID" => $iblockTypeId,
				  "INDEX_ELEMENT" => 'N',
				  "LID" =>
					array (
					  0 => 's1',
				    ),
				  "SORT" => 500,
				  "VERSION" => 2,
				  "INDEX_SECTION" => 'N',
				  "WORKFLOW" => 'N',
				  "BIZPROC" => 'N',
				);
				$iblockID = $ib->Add($arFields);
			}
			//create properties
			if (!self::createProperties($iblockID)){
				return false;
			}

			COption::SetOptionInt(self::$moduleID, "TAGS_IBLOCK_ID",$iblockID);
			return $iblockID;
		}else{
			return false;
		}
	}

	public function SetIBlockAllKeys(&$arFields){
		$IBlockID = COption::GetOptionInt(self::$moduleID, "TAGS_IBLOCK_ID");

		if($arFields["IBLOCK_ID"] == $IBlockID){
			Cmodule::IncludeModule('iblock');

			$arSelect = Array("ID", "NAME", "PROPERTY_".self::$proph1Code,"PROPERTY_".self::$propTitleCode,"PROPERTY_".self::$propKeyWordsCode,"PROPERTY_".self::$propDescriptionCode,"PREVIEW_TEXT","DETAIL_TEXT");
			$arFilter = Array("IBLOCK_ID"=>$arFields['IBLOCK_ID'],"ID"=>$arFields['ID']);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, array('nPageSize' => 1), $arSelect);
			$arAllMeta = array();
			if($arItem = $res->fetch())
			{
				$arAllMeta[self::$proph1Code] = $arItem["PROPERTY_".self::$proph1Code."_VALUE"];
				$arAllMeta[self::$propTitleCode] = $arItem["PROPERTY_".self::$propTitleCode."_VALUE"];
				$arAllMeta[self::$propKeyWordsCode] = $arItem["PROPERTY_".self::$propKeyWordsCode."_VALUE"];
				$arAllMeta[self::$propDescriptionCode] = $arItem["PROPERTY_".self::$propDescriptionCode."_VALUE"];

				//SEOTEXT1,SEOTEXT2
				$arAllMeta['KNSEOTEXT1'] = $arItem["PREVIEW_TEXT"];
				$arAllMeta["KNSEOTEXT2"] = $arItem["DETAIL_TEXT"];
			}
			if ($arAllMeta){
				$arAllKeys = array();
				foreach($arAllMeta as $key){
					preg_match_all("|#(.*)#|U",$key, $arOut);
					foreach($arOut[1] as $out){
						if (!in_array($out,$arAllKeys))
							$arAllKeys[] = $out;
					}
				}

				CIBlockElement::SetPropertyValues($arFields['ID'], $arFields['IBLOCK_ID'], $arAllKeys, self::$propAllKeysCode);
			}
		}
	}

	public function deleteIBlock(){
		CModule::IncludeModule('iblock');
		global $DB;

		if (!self::$iBlockTypeId){
			return false;
		}

		$DB->StartTransaction();
		if(!CIBlockType::Delete(self::$iBlockTypeId))
		{
			$DB->Rollback();
		}

		$DB->Commit();
	}

	function ShowKeyList(&$form){

		$IBlockID = COption::GetOptionInt(self::$moduleID, "TAGS_IBLOCK_ID");

		if (!$IBlockID){
			return false;
		}

		if ($_REQUEST['IBLOCK_ID'] == $IBlockID && $GLOBALS["APPLICATION"]->GetCurPage() == '/bitrix/admin/iblock_element_edit.php'){
			Cmodule::IncludeModule('iblock');

			/*get IBlock element data*/
			$elementId = intval($_REQUEST['ID']);
			$strUserProps = '';

			if ($elementId){
				$arSelect = Array();
				$arFilter = Array("IBLOCK_ID"=>$_REQUEST['IBLOCK_ID'], "ID"=>$elementId);
				$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
				if($ob = $res->GetNextElement())
				{
					$arFields = $ob->GetFields();
					$arFields['PROPERTIES'] = $ob->GetProperties();
					$keyValues = $arFields['PROPERTIES']['ALL_KEYS']['VALUE'];
					$arPropCode = $arFields['PROPERTIES']['ALL_KEYS']['ID'];
					$arPropName = $arFields['PROPERTIES']['ALL_KEYS']['NAME'];
				}
				if ($keyValues && is_array($keyValues)){
					foreach($keyValues as $index => $key){
						$strKeyValues .= "<span><b>{$key}</b>".($keyValues[$index+1] ? ', ' : '')."</span>";
					}
				}
			}else{
				$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['IBLOCK_ID'], "CODE"=>self::$propAllKeysCode));
				if($prop_fields = $properties->GetNext())
				{
					$arPropCode = $prop_fields['ID'];
					$arPropName = $prop_fields['NAME'];
				}
			}
			/**/

			//var_dump($form);
			foreach($form->arFields as $code=>$arFields){
				if ($arFields['id'] == 'PROPERTY_'.$arPropCode){
					$content = $arFields["content"];
					$form->arFields[$code]["custom_html"] = '<tr id="tr_PROPERTY_'.$arPropCode.'"><td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">'.$arPropName.':</td><td width="60%" class="adm-detail-content-cell-r"><table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tbee9135c35bdb02779709187d7c5f2320"><tbody><tr><td>'.$strKeyValues.'<br></td></tr></tbody></table></td></tr>';
				}
			}
		}
	}

	public function getIBElementByPage(){
		global $APPLICATION;
		CModule::IncludeModule('iblock');

		$IBlockID = COption::GetOptionInt(self::$moduleID, "TAGS_IBLOCK_ID");

		if (!$IBlockID){
			return false;
		}

		//get all seted keys
		$arSetedKeys = CMegaMetaKeys::getAllKeys();
		$arKeys = array();
		foreach($arSetedKeys as $key){
			if (!in_array($key['KEY'],$arKeys)){
				$arKeys[] = $key['KEY'];
			}
		}

		$arSelect = Array("ID","CODE","NAME","IBLOCK_ID");
		$arFilter = Array("IBLOCK_ID"=>$IBlockID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");

		if ($arKeys){
			$arFilter["PROPERTY_".self::$propAllKeysCode] = $arKeys;
		}

		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
		$arTagRules = array();
		while($ob = $res->GetNextElement(false,false))
		{
			$arFields = $ob->GetFields();
			$arFields['PROPERTIES'] = $ob->GetProperties();
			$arTmp = array();
			foreach($arFields['PROPERTIES'] as $property){
				if (in_array($property["CODE"],array("ALL_KEYS","TARGET_ON","TARGET_OFF","H1","TITLE","KEYWORDS","DESCRIPTION","ROBOTS","INDEX","FOLLOW"))){
					$arTmp[$property["CODE"]] = array(
						"ID" =>	$property["ID"],
						"NAME" => $property["NAME"],
						"CODE" => $property["CODE"],
						"VALUE" => $property["VALUE"],
						"~VALUE" => $property["~VALUE"],
					);
				}
			}
			$arFields['PROPERTIES'] = $arTmp;
			$arTagRules[] = $arFields;
		}
		//if even one rule
		if($arTagRules){
			return $arTagRules;
		}

		return false;
	}
}
?>
