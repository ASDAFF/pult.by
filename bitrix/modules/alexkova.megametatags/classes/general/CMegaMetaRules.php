<?
IncludeModuleLangFile(__FILE__);

function megaMeta_compare($v1, $v2) 
{ 
   if (intval($v1['SORT']) == intval($v2['SORT'])) return 0; 
   return (intval($v1['SORT']) < intval($v2['SORT']))?-1:1; 
} 

class CMegaMetaRules{
	
	private function _prepare($arIn){
		if (!is_array($arIn)){
			return false;
		}
		
		foreach($arIn as $el){
			if (!is_array($el)){
				$arIn = array($arIn);
				break;
			}else{
				break;
			}
		}
		
		return $arIn;
	}
	
	public function filterIBElementsTargeting($arElements){
		$arElements = CMegaMetaRules::_prepare($arElements);
		
		$arReturn = array();
		
		foreach ($arElements as $elementIndex=>$element)
		{
			//targeting
			$bError = false;
			
			if (empty($element['PROPERTIES']) || !isset($element['PROPERTIES'])){
				continue;
			}
			
			$curPage = $GLOBALS['APPLICATION']->GetCurPage(true);

			$targetOnProp = $element['PROPERTIES'][CBIblockMegaMetaTags::$propTargetOnCode]['~VALUE']['TEXT'];
			if(!empty($targetOnProp))
			{
				$bError = true;
				$showON = explode("\r\n",$targetOnProp);

				foreach ($showON as $pageON)
				{
					$pageON = trim($pageON);
					if(strlen($pageON)>0){
						$mask = str_replace("*", ".*", $pageON);
						$mask = str_replace("/", "\/", $mask);
						if(preg_match("/^$mask/", $curPage)){
							$bError = false;
							break;
						}
					}
				}
			}
			
			$targetOffProp = $element['PROPERTIES'][CBIblockMegaMetaTags::$propTargetOffCode]['~VALUE']['TEXT'];
			if(!empty($targetOffProp) && !$bError)
			{
				$showOFF = explode("\r\n",$targetOffProp);

				foreach ($showOFF as $pageOFF)
				{
					$pageOFF = trim($pageOFF);
					if(strlen($pageOFF)>0){
						$mask = str_replace("*", ".*", $pageOFF);
						$mask = str_replace("/", "\/", $mask);
						if(preg_match("/^$mask/", $curPage)){
							$bError = true;
							break;
						}
					}
				}
			}
			
			if(!$bError)//if can show
			{
				$arReturn[] = $element;
			}
		}
		
		return $arReturn;
	}
	
	public function filterIBElementsKeys($arElements){
		$arElements = CMegaMetaRules::_prepare($arElements);
		
		//all seted Keys
		$arSetedKeys = CMegaMetaKeys::getAllKeys();
		$arKeys = array();
		foreach($arSetedKeys as $key){
			if (!in_array($key['KEY'],$arKeys)){
				$arKeys[] = $key['KEY'];
			}
		}
		
		$arReturn = array();
		foreach ($arElements as $elementIndex=>$element){
			if (empty($element['PROPERTIES']) 
					|| !isset($element['PROPERTIES']) 
					|| (!$element['PROPERTIES']['ALL_KEYS']['VALUE'] && !is_array($element['PROPERTIES']['ALL_KEYS']['VALUE']))){
				continue;
			}
			
			$hasElement = true;
			foreach($element['PROPERTIES']['ALL_KEYS']['VALUE'] as $valKey){
				if (!in_array($valKey,$arKeys)){
					$hasElement = false;
					break;
				}
			}
				
			if ($hasElement){
				$arReturn[] = $element;
			}
		}
		
		return $arReturn;
	}
	
	public function filterIBElementsSort($arElements){
		$arElements = CMegaMetaRules::_prepare($arElements);
		reset($arElements);

		if (count($arElements) == 1){
			return (current($arElements)); // return first element of array
		}else{
			//sort by Sort :)
			usort($arElements,'megaMeta_compare');
			return (current($arElements)); // return first element of array
		}
	}
}

?>