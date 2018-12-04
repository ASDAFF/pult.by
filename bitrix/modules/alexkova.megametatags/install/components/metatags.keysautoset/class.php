<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CKeysAutoSet extends CBitrixComponent

{
	private $_componentModes = array(0 =>"auto", 1 => "request", 2 => "complex");
	private $_pages = array(0 => "INDEX", 1 => "LIST", 2 => "DETAIL");
	private $_curPage;
	private $_curMode;
	private $_elementId;
	private $_sectionId;
	private $_sectionCode;
	private $_elementCode;
	
	public function onPrepareComponentParams($arParams)
	{
		$result = array(
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
			"SECTION_ID" => intval($arParams["SECTION_ID"]),
			"ELEMENT_ID" => intval($arParams["ELEMENT_ID"]),
			"SECTION_CODE" => trim(strip_tags($arParams["SECTION_CODE"])),
			"ELEMENT_CODE" => trim(strip_tags($arParams["ELEMENT_CODE"])),
			"COMPLEX_COMPONENT_PATH" => trim(strip_tags($arParams["COMPLEX_COMPONENT_PATH"])),
			"COMPLEX_SECTION_PATH" => trim(strip_tags($arParams["COMPLEX_SECTION_PATH"])),
			"COMPLEX_ELEMENT_PATH" => trim(strip_tags($arParams["COMPLEX_ELEMENT_PATH"])),
			"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
			"IBLOCK_TYPE" => trim(strip_tags($arParams["IBLOCK_TYPE"])),
			"COMPONENT_MODE" => intval($arParams["COMPONENT_MODE"]),
		);
		return $result;
	}
	
	public function getSectionCode(){
		return $this->_sectionCode;
	}
	
	public function getSectionId(){
		return $this->_sectionId;
	}
	
	public function getElementId(){
		return $this->_elementId;
	}
	
	public function getElementCode(){
		return $this->_elementCode;
	}
	
	public function getCurrentMode(){
		return $this->_componentModes[$this->_curMode];
	}
	
	public function InitCurrentMode(){
		$this->_curMode = $this->arParams['COMPONENT_MODE'];;
	}

	public function setKeys(){
		$this->keysInstall();
	}
	
	public function setElementKeys(){
		$elementId = $this->getElementId();

		if (!$elementId || !$this->arParams['IBLOCK_ID']){
			return false;
		}
		
		$arSelect = Array("ID","IBLOCK_ID", "NAME");
		$arFilter = Array("IBLOCK_ID"=>$this->arParams['IBLOCK_ID'], "ID" =>$elementId);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
		if($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$this->arResult['META_KEYS']['ELEMENT_NAME'] = $arFields['NAME'];
			$arFields['PROPERTIES'] = $ob->GetProperties();

			foreach ($arFields['PROPERTIES'] as  $property){
				$addPropToKey = '';
				
				if ($property['PROPERTY_TYPE'] == 'S' && $property['USER_TYPE'] == 'HTML'){
					if ($property["MULTIPLE"] == "Y"){
						$arpropHtml = array();
						foreach($property["VALUE"] as $htmlProp){
							if ($htmlProp["TEXT"]){
								$arpropHtml[] = $htmlProp["TEXT"];
							}
						}
						$property['VALUE'] = implode(', ', $arpropHtml);
					}else{
						$property['VALUE'] = $property["VALUE"]["TEXT"];
					}
					
				}
			
				if ($property['PROPERTY_TYPE'] == 'S' || $property['PROPERTY_TYPE'] == 'L' || $property['PROPERTY_TYPE'] == 'N'){
					if ($property['VALUE'] && is_array($property['VALUE'])){
						$this->arResult['META_KEYS']['ELEMENT_'.$property['CODE']] = implode(', ', $property['VALUE']);
					}elseif($property['VALUE']){
						$this->arResult['META_KEYS']['ELEMENT_'.$property['CODE']] = $property['VALUE'];
					}
				}
			}
		}
	}
	
	public function setSectionIdByElementID(){
		$elementId = $this->getElementId();
		
		if (!$elementId){
			return false;
		}
		
		CModule::IncludeModule('iblock');
		
		$arSelect = Array("ID", "IBLOCK_SECTION_ID");
		$arFilter = Array("IBLOCK_ID"=>$this->arParams['IBLOCK_ID'], "ID" => $elementId);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
		if($arFields = $res->fetch())
		{
			if ($arFields['IBLOCK_SECTION_ID'])
				$this->_sectionId = $arFields['IBLOCK_SECTION_ID'];
		}
	}
	
	public function setSectionKeys(){
		$sectionId = $this->getSectionId();
		$elementId = $this->getElementId();
		
		if ($elementId && !$sectionId){
			$this->setSectionIdByElementID();
			$sectionId = $this->getSectionId();
		}
		
		if (!$sectionId || !$this->arParams['IBLOCK_ID']){
			return false;
		}

		$arFilter = Array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'ACTIVE'=>'Y',"ID" =>$sectionId);
		$db_list = CIBlockSection::GetList(Array(), $arFilter, false);
		if($ar_result = $db_list->fetch())
		{
			$arSection = $ar_result;
		}
		
		$prefix = $this->getCurPage();
		
		if ($arSection){
			$arFilter = Array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'GLOBAL_ACTIVE'=>'Y',"<=LEFT_BORDER" =>$arSection['LEFT_MARGIN'],">=RIGHT_BORDER" =>$arSection['RIGHT_MARGIN']);
			$db_list = CIBlockSection::GetList(Array(), $arFilter, false, array('UF_*'));
			while($ar_result = $db_list->fetch())
			{
				
				$this->arResult['META_KEYS'][$prefix.'SECTION_NAME'] = $ar_result['NAME'];
				$this->arResult['META_KEYS'][$prefix.'SECTION_'.$ar_result['DEPTH_LEVEL'].'_NAME'] = $ar_result['NAME'];				
				
				foreach($ar_result as $key => $value){
					$posUF = strpos($key, "UF");
					
					if ($posUF !== false) {//UF in key
	
						$depthSuffix = $ar_result['DEPTH_LEVEL'].'_';
						
						if ($value && is_array($value)){
							if ($sectionId == $ar_result['ID']){
								$this->arResult['META_KEYS'][$prefix.'SECTION_'.$key] = implode(',',$value);	
							}
							
							$this->arResult['META_KEYS'][$prefix.'SECTION_'.$depthSuffix.$key] = implode(',',$value);
							
						}elseif($value){
							if ($sectionId == $ar_result['ID']){
								$this->arResult['META_KEYS'][$prefix.'SECTION_'.$key] = $value;
							}
							
							$this->arResult['META_KEYS'][$prefix.'SECTION_'.$depthSuffix.$key] = $value;
						}
					}
				}
			}
		}
	}
	
	public function keysInstall(){
		if(CModule::IncludeModuleEx('alexkova.megametatags') == 1){
			$arKeys = array();
			foreach($this->arResult['META_KEYS'] as $key=>$nameKey){
			   $arKeys[] = array("KEY"=>$key,"VALUE"=>$nameKey,"WHERE_SET"=>$this);   
			}
			if ($arKeys){
				CMegaMetaKeys::setKeys($arKeys);      
			}
		}
	}
	
	public function setIdsInParams(){
		$sectionId = $this->getSectionId();
		$elementId = $this->getElementId();
		
		$this->arParams['SECTION_ID'] = $sectionId;
		$this->arParams['ELEMENT_ID'] = $elementId;
	}
	
	public function getParamsFromComplexComponent(){
		if (!$this->arParams['COMPLEX_COMPONENT_PATH'] ||
			(!$this->arParams['COMPLEX_SECTION_PATH'] &&
			!$this->arParams['COMPLEX_ELEMENT_PATH'])
		){
			return false;
		}
		
		$engine = new CComponentEngine($this);
		$engine->addGreedyPart("#SECTION_CODE_PATH#");
		$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
		$arUrlTemplates = array(
			"SECTION_ID" => $this->arParams['COMPLEX_SECTION_PATH'], 
			"ELEMENT_ID" => $this->arParams['COMPLEX_ELEMENT_PATH'],
		);

		$componentPage = $engine->guessComponentPath(
			$this->arParams['COMPLEX_COMPONENT_PATH'],
			$arUrlTemplates,
			$arVariables
		);

		$this->setCurPage($componentPage);
	
		if (!$arVariables){
			return false;
		}

		if ($arVariables['SECTION_ID']){
			$section = $arVariables['SECTION_ID'];
		}
		if ($arVariables['SECTION_CODE']){
			$section = $arVariables['SECTION_CODE'];
		}
		
		if($section){
			$this->setSectionId($section);
		}
		
		if ($arVariables['ELEMENT_ID']){
			$element = $arVariables['ELEMENT_ID'];
		}
		if ($arVariables['ELEMENT_CODE']){
			$element = $arVariables['ELEMENT_CODE'];
		}
		
		if($element){
			$this->setElementId($element);
		}
		
		return true;
	}
	
	public function setCurPage($page){
		if (!$page){
			return;
		}
		
		if ($page == 'ELEMENT_ID'){
			$this->_curPage = 2;
			return;
		}
		
		if ($page == 'SECTION_ID'){
			$this->_curPage = 1;
			return;
		}
		
		$this->_curPage = $page;
		return;
	}
	
	public function getCurPage(){
		if (preg_match('/^(0)$|^([1-9][0-9]*)$/u', $this->_curPage)){
			 $prefix = $this->_pages[$this->_curPage]."_";
		}else{
			 $prefix = $this->_curPage."_";
		}
		
		return $prefix;
	}
	
	public function setSectionId($sectionVal){
		if (!$sectionVal){
			return false;
		}

		if (preg_match('/^(0)$|^([1-9][0-9]*)$/u', $sectionVal) && intval($sectionVal)){
			$this->_sectionId = intval($sectionVal);
		}else{
			$this->_sectionCode = $sectionVal;
			$this->getSectionIdByCode();
		}
	}
	
	public function setElementId($elementVal){
		if (!$elementVal){
			return false;
		}

		if (preg_match('/^(0)$|^([1-9][0-9]*)$/u', $elementVal) && intval($elementVal)){
			$this->_elementId = intval($elementVal);
		}else{
			$this->_elementCode = $elementVal;
			$this->getElementIdByCode();
		}
	}
	
	public function getSectionIdByCode(){
		$sectionCode = $this->getSectionCode();

		if (!$sectionCode || !$this->arParams['IBLOCK_ID']){
			return false;
		}
		
		CModule::IncludeModule('iblock');
		
		$arFilter = Array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'CODE'=>$sectionCode);
		$db_list = CIBlockSection::GetList(Array(), $arFilter, false);
		if($ar_result = $db_list->fetch())
		{
			$this->_sectionId = $ar_result['ID'];
		}
	}
	
	public function getElementIdByCode(){
		$elementCode = $this->getElementCode();

		if (!$elementCode || !$this->arParams['IBLOCK_ID']){
			return false;
		}
		
		CModule::IncludeModule('iblock');
		
		$arSelect = Array("ID");
		$arFilter = Array("IBLOCK_ID"=>$this->arParams['IBLOCK_ID'],"CODE"=>$elementCode);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
		if($arFields = $res->fetch())
		{
			$this->_elementId = $arFields['ID'];
		}

	}
	
	public function getParamsFromRequest(){
		$arUri = explode('/',$_SERVER['REAL_FILE_PATH']);
		$page = toUpper(preg_replace('/\..+$/','',end($arUri)));
		$this->setCurPage($page);
		$this->getSectionParam();
		$this->getElementParam();
	}
	
	public function getSectionParam(){
		if ($this->arParams['SECTION_CODE'] && !$this->arParams['SECTION_ID']){
			$this->setSectionId($this->arParams['SECTION_CODE']);
			return;
		}
		
		$this->_sectionId = $this->arParams['SECTION_ID'];
		return;
	}
	
	public function getElementParam(){
		if ($this->arParams['ELEMENT_CODE'] && !$this->arParams['ELEMENT_ID']){
			$this->setElementId($this->arParams['ELEMENT_CODE']);
			return;
		}
		
		$this->_elementId = $this->arParams['ELEMENT_ID'];
		return;
	}
	
	public function executeComponent()
	{
		return parent::executeComponent();
	}
}