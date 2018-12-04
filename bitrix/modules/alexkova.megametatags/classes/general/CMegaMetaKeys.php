<?php

class CMegaMetaKeys{
	public static $MODULE_ID='alexkova.megametatags';
	private function _getString($data){
		//bitrix version
		$arVersion = explode('.',SM_VERSION);
		$mainVersion = $arVersion[0];

		if (is_string($data)){
			return $data;
		}
		
		if (is_object($data) && $mainVersion < 12){
			if ($data->__component){
				//template
				return array("COMPONENT_NAME"=>$data->__component->__name,"PATH"=>$data->__file,"TYPE"=>"template.php");	
			}elseif($data->__component_epilog){
				//template
				return array("COMPONENT_NAME"=>$data->__name,"PATH"=>$data->__component_epilog["epilogFile"],"TYPE"=>"component_epilog.php");	
			}else{
				//component
				return array("COMPONENT_NAME"=>$data->__name,"PATH"=>$data->__path,"TYPE"=>"component.php");				
			}
		}else{
			$arOut = array("COMPONENT_NAME"=>$data->__name,"COMPONENT_PATH"=>$data->__path);
			if ($data->__templatePage){
				$arOut['TEMPLATE_PAGE'] = $data->__templatePage;
			}
			if ($data->__template){
				$arOut['TEMPLATE_NAME'] = $data->__template->__name;
				$arOut['TEMPLATE_FILE'] = $data->__template->__file;
			}

			return $arOut;	
		}
		
		return false;
	}

	public function setPagenKey($whereSet){
		global $globalMetaMegaTags;
		
		$arUriParams = explode('?',$_SERVER['REQUEST_URI']);
		if ($arUriParams[1]){
			$arUriParams = explode('&',$arUriParams[1]);
			foreach($arUriParams as $param){
				if (strpos($param,'PAGEN_') !== false){
					$arPageNumber = explode('=',$param);
					$pageNumberKey = $arPageNumber[0];
					$pageNumberValue = $arPageNumber[1];
					break;
				}
			}
		}
		if ($pageNumberValue){
			$globalMetaMegaTags['KEYS'][$pageNumberKey]['KEY'] = $pageNumberKey;
			$globalMetaMegaTags['KEYS'][$pageNumberKey]['VALUE'] = $pageNumberValue;
			$globalMetaMegaTags['KEYS'][$pageNumberKey]['WHERE_SET'] = self::_getString($whereSet);
		}
		
	}
	
	public function setKey($key,$keyVal,$whereSet = false, $inCashe = false){
		global $globalMetaMegaTags;
		
		$inArray = false;
		foreach($globalMetaMegaTags['KEYS'] as $index => $keys){
			if ($keys['KEY'] == $key){
				$globalMetaMegaTags['KEYS'][$index]['KEY'] = $key;
				$globalMetaMegaTags['KEYS'][$index]['VALUE'] = $keyVal;
				if ($whereSet){
					$globalMetaMegaTags['KEYS'][$index]['WHERE_SET'] = self::_getString($whereSet);	
				}
				if ($inCashe){
					$globalMetaMegaTags['KEYS'][$index]['IN_CACHE'] = "Y";	
				}
				$inArray = true;
				break;
			}
		}
		
		if (!$inArray){
			$globalMetaMegaTags['KEYS'][$key]['KEY'] = $key;
			$globalMetaMegaTags['KEYS'][$key]['VALUE'] = $keyVal;
			
			if ($whereSet){
				$globalMetaMegaTags['KEYS'][$key]['WHERE_SET'] = self::_getString($whereSet);
			}
			if ($inCashe){
				$globalMetaMegaTags['KEYS'][$key]['IN_CACHE'] = "Y";	
			}
		}
		
		self::setPagenKey($whereSet);
		
		return true;
	}
	
	public function setKeys($arKeys){

		global $globalMetaMegaTags;
		
		if (!is_array($arKeys)){
			return false;
		}
		
		foreach($arKeys as $k){
			if (!is_array($k)){
				$arKeys = array($arKeys);
				break;
			}else{
				break;
			}
		}

		$arKeysCodes = array();
		foreach($arKeys as $keyIndex => $keyItem){
			if (!in_array($keyItem['KEY'],$arKeysCodes))
				$arKeysCodes[$keyIndex] = $keyItem['KEY'];
		}

		foreach($globalMetaMegaTags['KEYS'] as $index => $item){
			if (in_array($item['KEY'],$arKeysCodes)){
				$globalMetaMegaTags['KEYS'][$index]['KEY'] = $arKeys[array_search($item['KEY'],$arKeysCodes)]['KEY'];
				$globalMetaMegaTags['KEYS'][$index]['VALUE'] = $arKeys[array_search($item['KEY'],$arKeysCodes)]['VALUE'];
				if ($arKeys[array_search($item['KEY'],$arKeysCodes)]['WHERE_SET']){
					$globalMetaMegaTags['KEYS'][$index]['WHERE_SET'] =  self::_getString($arKeys[array_search($item['KEY'],$arKeysCodes)]['WHERE_SET']);	
				}
				if ($arKeys[array_search($item['KEY'],$arKeysCodes)]['IN_CACHE']){
					$globalMetaMegaTags['KEYS'][$index]['IN_CACHE'] =  "Y";	
				}
				unset($arKeys[array_search($item['KEY'],$arKeysCodes)]);
			}
		}
		
		if ($arKeys){
			foreach($arKeys as $keyIndex => $keyItem){
				$globalMetaMegaTags['KEYS'][$keyItem['KEY']]['KEY'] = $keyItem['KEY'];
				$globalMetaMegaTags['KEYS'][$keyItem['KEY']]['VALUE'] = $keyItem['VALUE'];
				if ($keyItem['WHERE_SET']){
					$globalMetaMegaTags['KEYS'][$keyItem['KEY']]['WHERE_SET'] =  self::_getString($keyItem['WHERE_SET']);					
				}
				if ($keyItem['IN_CACHE']){
					$globalMetaMegaTags['KEYS'][$keyItem['KEY']]['IN_CACHE'] = "Y";					
				}
			}
		}

		self::setPagenKey($whereSet);
		return true;
	}
	
	public function getKey($key){
		global $globalMetaMegaTags;
		foreach($globalMetaMegaTags['KEYS'] as $index => $item){
			if ($item['KEY'] == $key){
				return $item;
			}
		}
		
		return false;
	}
	
	public function getKeys($arKeys){
		global $globalMetaMegaTags;
		if (!is_array($arKeys)){
			return false;
		}
		
		$arResult = array();
		foreach($globalMetaMegaTags['KEYS'] as $index => $item){
			if (in_array($item['KEY'],$arKeys)){
				$arResult[$item['KEY']] = $item;
			}
		}
		
		if ($arResult){
			return $arResult;			
		}else{
			return false;
		}
	}
	
	public function getAllKeys(){
		global $globalMetaMegaTags;
		return $globalMetaMegaTags['KEYS'];
	}
	
	public function getCacheKeys(){
		global $globalMetaMegaTags;
		$arCacheKeys = array();
		foreach($globalMetaMegaTags['KEYS'] as $key){
			if ($key['IN_CACHE']){
				$arCacheKeys[$key['KEY']] = $key;
			}
		}
		return $arCacheKeys;
	}
	
	public function delAllKeys(){
		global $globalMetaMegaTags;
		
		unset($globalMetaMegaTags['KEYS']);
		return true;
	}
	
	public function delKey($key){
		global $globalMetaMegaTags;
		
		foreach($globalMetaMegaTags['KEYS'] as $index => $keys){
			if ($keys['KEY'] == $key){
				unset($globalMetaMegaTags['KEYS'][$index]);
				break;
			}
		}

		return true;
	}
	
	public function delKeys($arKeys){
		global $globalMetaMegaTags;
		
		if (!is_array($arKeys)){
			$arKeys = array($arKeys);
		}

		foreach($globalMetaMegaTags['KEYS'] as $index => $item){
			if (in_array($item['KEY'],$arKeys)){
				unset($globalMetaMegaTags['KEYS'][$index]);
			}
		}
		
		return true;
	}
	
	public function setTags($arElement){
		$arSetedKeys = CMegaMetaKeys::getAllKeys();
		if (!$arSetedKeys){
			return false;
		}
		
		$arReturn = array();
		foreach($arSetedKeys as $key){
			if (!in_array($key['KEY'],$arKeys)){
				$arKeys[] = $key['KEY'];
			}
		}

		$arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['VALUE'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$proph1Code]['VALUE'];
		$arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['MASK'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$proph1Code]['VALUE'];
		
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['VALUE'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propTitleCode]['VALUE'];
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['MASK'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propTitleCode]['VALUE'];
		
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE'];
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['MASK'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE'];
		
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE'];
		$arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['MASK'] = $arElement['PROPERTIES'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE'];
		
		//SEOTEXT
		if ($arElement['PREVIEW_TEXT']){
			$arReturn['TAGS']['PREVIEW_TEXT']['VALUE'] = $arElement['PREVIEW_TEXT'];
			$arReturn['TAGS']['PREVIEW_TEXT']['MASK'] = $arElement['PREVIEW_TEXT'];
		}
		if ($arElement['DETAIL_TEXT']){
			$arReturn['TAGS']['DETAIL_TEXT']['VALUE'] = $arElement['DETAIL_TEXT'];
			$arReturn['TAGS']['DETAIL_TEXT']['MASK'] = $arElement['DETAIL_TEXT'];
		}
		
		foreach($arSetedKeys as $key){
			if (in_array($key['KEY'],$arElement['PROPERTIES'][CBIblockMegaMetaTags::$propAllKeysCode]['VALUE'])){
				$arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['VALUE']);
				$arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['VALUE']);
				$arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE']);
				$arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE']);
				
				if($arElement['PREVIEW_TEXT']){
					$arReturn['TAGS']['PREVIEW_TEXT']['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS']['PREVIEW_TEXT']['VALUE']);
				}
				
				if($arElement['DETAIL_TEXT']){
					$arReturn['TAGS']['DETAIL_TEXT']['VALUE'] = str_replace("#{$key['KEY']}#",$key['VALUE'],$arReturn['TAGS']['DETAIL_TEXT']['VALUE']);
				}
			}
			$arReturn['WHERE_SET'][$key['KEY']] = $key['WHERE_SET'];
			$arReturn['IN_CACHE'][$key['KEY']] = $key['IN_CACHE'];
			$arReturn['KEYS'][$key['KEY']] = $key['VALUE'];
		}
		
		foreach ($arReturn['TAGS'] as $key=> $value){
			if (!$value['VALUE'])
				unset($arReturn['TAGS'][$key]);
		}
		
		//Set Meta Tags
		if ($arReturn){
			global $APPLICATION;
			$str_prop_h1 = htmlspecialchars(COption::GetOptionString(self::$MODULE_ID, "PROPERTY_".CBIblockMegaMetaTags::$proph1Code));
			//$str_prop_title = htmlspecialchars(COption::GetOptionString(self::$MODULE_ID, "PROPERTY_".CBIblockMegaMetaTags::$propTitleCode));
			$str_prop_keywords = htmlspecialchars(COption::GetOptionString(self::$MODULE_ID, "PROPERTY_".CBIblockMegaMetaTags::$propKeyWordsCode));
			$str_prop_description = htmlspecialchars(COption::GetOptionString(self::$MODULE_ID, "PROPERTY_".CBIblockMegaMetaTags::$propDescriptionCode));
			
			//handler before set
			$events = GetModuleEvents(self::$MODULE_ID,'OnBeforeSetTags');
			while($arEvent = $events->fetch()){
				ExecuteModuleEventEx($arEvent, array(&$arReturn));
			}
			
			if ($arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['VALUE'])
				$APPLICATION->SetPageProperty($str_prop_h1,$arReturn['TAGS'][CBIblockMegaMetaTags::$proph1Code]['VALUE']);
			
			if ($arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['VALUE'])
				$APPLICATION->SetTitle($arReturn['TAGS'][CBIblockMegaMetaTags::$propTitleCode]['VALUE']);
			
			if ($arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE'])
				$APPLICATION->SetPageProperty($str_prop_keywords,$arReturn['TAGS'][CBIblockMegaMetaTags::$propKeyWordsCode]['VALUE']);
			
			if ($arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE'])
				$APPLICATION->SetPageProperty($str_prop_description,$arReturn['TAGS'][CBIblockMegaMetaTags::$propDescriptionCode]['VALUE']);
			
			if ($arReturn['TAGS']["PREVIEW_TEXT"]['VALUE'])
				$APPLICATION->SetPageProperty('KNSEOTEXT1',$arReturn['TAGS']['PREVIEW_TEXT']['VALUE']);
				
			if ($arReturn['TAGS']["DETAIL_TEXT"]['VALUE'])
				$APPLICATION->SetPageProperty('KNSEOTEXT2',$arReturn['TAGS']['DETAIL_TEXT']['VALUE']);
				
			
			//handler after set
			$events = GetModuleEvents(self::$MODULE_ID,'OnAfterSetTags');
			while($arEvent = $events->fetch()){
				ExecuteModuleEventEx($arEvent, array(&$arReturn));
			}
		}

		return $arReturn;
	}
	
	function SetMegaTags(){
		global $USER;
		
		//get all keys
		global $APPLICATION;

		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return false;
		}
		if (CSite::InDir('/bitrix/')){
			return false;
		}
		
		if (file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/tools/alexkova.megametatags/megametatags_settags.php")){
			include_once ($_SERVER['DOCUMENT_ROOT']."/bitrix/tools/alexkova.megametatags/megametatags_settags.php");
		}else{
			return false;
		}
	}
}
?>
