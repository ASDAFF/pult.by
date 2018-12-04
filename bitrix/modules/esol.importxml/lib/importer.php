<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Importer {
	var $rcurrencies = array('#USD#', '#EUR#');
	var $xmlParts = array();
	
	function __construct($filename, $params, $fparams, $stepparams, $pid = false)
	{
		$this->filename = $_SERVER['DOCUMENT_ROOT'].$filename;
		$this->params = $params;
		$this->fparams = $fparams;
		$this->sections = array();
		$this->sectionIds = array();
		$this->propVals = array();
		$this->hlbl = array();
		$this->errors = array();
		$this->breakWorksheet = false;
		$this->maxStepRows = 1000;
		$this->xmlRowDiff = 0;
		$this->stepparams = $stepparams;
		$this->stepparams['total_read_line'] = intval($this->stepparams['total_read_line']);
		$this->stepparams['total_line'] = intval($this->stepparams['total_line']);
		$this->stepparams['correct_line'] = intval($this->stepparams['correct_line']);
		$this->stepparams['error_line'] = intval($this->stepparams['error_line']);
		$this->stepparams['killed_line'] = intval($this->stepparams['killed_line']);
		$this->stepparams['offer_killed_line'] = intval($this->stepparams['offer_killed_line']);
		$this->stepparams['element_added_line'] = intval($this->stepparams['element_added_line']);
		$this->stepparams['element_updated_line'] = intval($this->stepparams['element_updated_line']);
		$this->stepparams['element_removed_line'] = intval($this->stepparams['element_removed_line']);
		$this->stepparams['sku_added_line'] = intval($this->stepparams['sku_added_line']);
		$this->stepparams['sku_updated_line'] = intval($this->stepparams['sku_updated_line']);
		$this->stepparams['section_added_line'] = intval($this->stepparams['section_added_line']);
		$this->stepparams['section_updated_line'] = intval($this->stepparams['section_updated_line']);
		$this->stepparams['zero_stock_line'] = intval($this->stepparams['zero_stock_line']);
		$this->stepparams['offer_zero_stock_line'] = intval($this->stepparams['offer_zero_stock_line']);
		$this->stepparams['old_removed_line'] = intval($this->stepparams['old_removed_line']);
		$this->stepparams['offer_old_removed_line'] = intval($this->stepparams['offer_old_removed_line']);
		$this->stepparams['xmlCurrentRow'] = intval($this->stepparams['xmlCurrentRow']);
		$this->stepparams['xmlSectionCurrentRow'] = intval($this->stepparams['xmlSectionCurrentRow']);
		$this->stepparams['total_file_line'] = 1;

		if(!$this->params['SECTION_UID']) $this->params['SECTION_UID'] = 'NAME';
		
		$this->skuInElement = (bool)(isset($this->params['GROUPS']['OFFER']) && strpos($this->params['GROUPS']['OFFER'], $this->params['GROUPS']['ELEMENT'])===0);
		$this->subSectionInSection = (bool)(isset($this->params['GROUPS']['SUBSECTION']) && strpos($this->params['GROUPS']['SUBSECTION'], $this->params['GROUPS']['SECTION'])===0);
		$this->elementInSection = (bool)(isset($this->params['GROUPS']['ELEMENT']) && strpos($this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['SECTION'])===0);
		if($this->elementInSection)
		{
			if(strpos($this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['SUBSECTION'])===0)
			{
				$this->xpathElementInSection = trim(substr($this->params['GROUPS']['ELEMENT'], strlen($this->params['GROUPS']['SUBSECTION'])), '/');
			}
			else
			{
				$this->xpathElementInSection = trim(substr($this->params['GROUPS']['ELEMENT'], strlen($this->params['GROUPS']['SECTION'])), '/');
			}
		}
		$this->propertyInOffer = (bool)(isset($this->params['GROUPS']['OFFER']) && isset($this->params['GROUPS']['PROPERTY']) && strpos($this->params['GROUPS']['PROPERTY'], $this->params['GROUPS']['OFFER'])===0);
		$this->propertyInElement = (bool)(!$this->propertyInOffer && isset($this->params['GROUPS']['PROPERTY']) && strpos($this->params['GROUPS']['PROPERTY'], $this->params['GROUPS']['ELEMENT'])===0);
		
		$saveStat = (bool)($params['STAT_SAVE']=='Y');
		$this->logger = new \Bitrix\EsolImportxml\Logger($saveStat, $pid);
		$this->fl = new \Bitrix\EsolImportxml\FieldList();
		$this->conv = new \Bitrix\EsolImportxml\Conversion($this);
		$this->cloud = new \Bitrix\EsolImportxml\Cloud();
		$this->sftp = new \Bitrix\EsolImportxml\Sftp();
		
		$moduleId = 'esol.importxml';
		$this->useProxy = false;
		$this->proxySettings = array(
			'proxyHost' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_HOST', ''),
			'proxyPort' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_PORT', ''),
			'proxyUser' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_USER', ''),
			'proxyPassword' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_PASSWORD', ''),
		);
		if($this->proxySettings['proxyHost'] && $this->proxySettings['proxyPort'])
		{
			$this->useProxy = true;
		}
		
		/*Temp folders*/
		$this->filecnt = 0;
		$dir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/esol.importxml/';
		CheckDirPath($dir);
		if(!$this->stepparams['tmpdir'])
		{
			$i = 0;
			while(($tmpdir = $dir.$i.'/') && file_exists($tmpdir)){$i++;}
			$this->stepparams['tmpdir'] = $tmpdir;
			CheckDirPath($tmpdir);
		}
		$this->tmpdir = $this->stepparams['tmpdir'];
		$this->imagedir = $this->stepparams['tmpdir'].'images/';
		CheckDirPath($this->imagedir);
		
		$this->tmpfile = $this->tmpdir.'params.txt';
		$this->fileElementsId = $this->tmpdir.'elements_id.txt';
		$this->fileOffersId = $this->tmpdir.'offers_id.txt';
		/*/Temp folders*/
		
		if(file_exists($this->tmpfile) && filesize($this->tmpfile) > 0)
		{
			$this->stepparams = array_merge($this->stepparams, unserialize(file_get_contents($this->tmpfile)));
		}
		
		if(!isset($this->stepparams['curstep'])) $this->stepparams['curstep'] = 'import_sections';
		if(isset($this->stepparams['sectionIds']))
		{
			$this->sectionIds = $this->stepparams['sectionIds'];
			unset($this->stepparams['sectionIds']);
		}
		
		if(!isset($this->params['MAX_EXECUTION_TIME']) || $this->params['MAX_EXECUTION_TIME']!==0)
		{
			if(\Bitrix\Main\Config\Option::get('esol.importxml', 'SET_MAX_EXECUTION_TIME')=='Y' && is_numeric(\Bitrix\Main\Config\Option::get('esol.importxml', 'MAX_EXECUTION_TIME')))
			{
				$this->params['MAX_EXECUTION_TIME'] = intval(\Bitrix\Main\Config\Option::get('esol.importxml', 'MAX_EXECUTION_TIME'));
				if(ini_get('max_execution_time') && $this->params['MAX_EXECUTION_TIME'] > ini_get('max_execution_time') - 5) $this->params['MAX_EXECUTION_TIME'] = ini_get('max_execution_time') - 5;
				if($this->params['MAX_EXECUTION_TIME'] < 1) $this->params['MAX_EXECUTION_TIME'] = 1;
				if($this->params['MAX_EXECUTION_TIME'] > 300) $this->params['MAX_EXECUTION_TIME'] = 300;
			}
			else
			{
				$this->params['MAX_EXECUTION_TIME'] = intval(ini_get('max_execution_time')) - 10;
				if($this->params['MAX_EXECUTION_TIME'] < 10) $this->params['MAX_EXECUTION_TIME'] = 15;
				if($this->params['MAX_EXECUTION_TIME'] > 50) $this->params['MAX_EXECUTION_TIME'] = 50;
			}
		}
		
		if($pid!==false)
		{
			$this->procfile = $dir.$pid.'.txt';
			$this->errorfile = $dir.$pid.'_error.txt';
			if($this->stepparams['total_line'] < 1)
			{
				$oProfile = new \Bitrix\EsolImportxml\Profile();
				$oProfile->UpdateFields($pid, array('DATE_START'=>new \Bitrix\Main\Type\DateTime()));
				
				if(file_exists($this->procfile)) unlink($this->procfile);
				if(file_exists($this->errorfile)) unlink($this->errorfile);
			}
			$this->pid = $pid;
		}
	}
	
	public function CheckTimeEnding($time)
	{
		return ($this->params['MAX_EXECUTION_TIME'] && (time()-$time >= $this->params['MAX_EXECUTION_TIME']));
	}
	
	public function Import()
	{
		register_shutdown_function(array($this, 'OnShutdown'));
		$time = time();
		
		if($this->stepparams['curstep'] == 'import_sections')
		{
			if($this->params['GROUPS']['SECTION'])
			{
				$this->InitImport('section');

				while($arItem = $this->GetNextSectionRecord($time))
				{
					$this->currentSectionXpath = rtrim($this->params['GROUPS']['SECTION'], '/');
					if(is_array($arItem)) $this->SaveSectionRecord($arItem);
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
			}
			$this->stepparams['curstep'] = 'import';
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		
		if($this->stepparams['curstep'] == 'import' && !$this->elementInSection)
		{
			if($this->params['GROUPS']['ELEMENT'])
			{
				$this->InitImport('element');

				while($arItem = $this->GetNextRecord($time))
				{
					if(is_array($arItem)) $this->SaveRecord($arItem);
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
			}
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		return $this->EndOfLoading($time);
	}
	
	public function EndOfLoading($time)
	{
		if(is_callable(array('CIBlock', 'clearIblockTagCache')))
		{
			$bEventRes = true;
			foreach(GetModuleEvents("esol.importxml", "OnBeforeClearCache", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array($this->params['IBLOCK_ID']))===false)
				{
					$bEventRes = false;
				}
			}
			if($bEventRes)
			{
				\CIBlock::clearIblockTagCache($this->params['IBLOCK_ID']);
			}
		}
		
		$arElemDefaults = array();
		if($this->params['CELEMENT_MISSING_DEFAULTS'])
		{
			$arElemDefaults = unserialize(base64_decode($this->params['CELEMENT_MISSING_DEFAULTS']));
			if(!is_array($arElemDefaults)) $arElemDefaults = array();
		}
		$bSetDefaultProps = (bool)(count($arElemDefaults) > 0);

		$bElemDeactivate = (bool)($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params['CELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params['CELEMENT_MISSING_TO_ZERO']=='Y' || $this->params['CELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y' || $this->params['OFFER_MISSING_DEACTIVATE']=='Y' || $this->params['OFFER_MISSING_TO_ZERO']=='Y' || $this->params['OFFER_MISSING_REMOVE_PRICE']=='Y' || $this->params['OFFER_MISSING_REMOVE_ELEMENT']=='Y');
		
		if($bElemDeactivate || $bSetDefaultProps)
		{
			$bOnlySetDefaultProps = (bool)($bSetDefaultProps && !$bElemDeactivate);
			if($this->stepparams['curstep'] == 'import')
			{
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
				$this->stepparams['curstep'] = 'deactivate_elements';
				$this->stepparams['deactivate_element_last'] = \Bitrix\EsolImportxml\Utils::SortFileIds($this->fileElementsId);
				$this->stepparams['deactivate_offer_last'] = \Bitrix\EsolImportxml\Utils::SortFileIds($this->fileOffersId);
				$this->stepparams['deactivate_element_first'] = 0;
				$this->stepparams['deactivate_offer_first'] = 0;
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time + 1000)) return $this->GetBreakParams();
			}
			
			$arFieldsList = array();
			$offersExists = false;			
			if(count(preg_grep('/;OFFER_/', $this->params['FIELDS'])) > 0)
			{
				$offersExists = true;
			}
			
			$arFieldsList = array(
				'IBLOCK_ID' => $this->params['IBLOCK_ID']
			);
			if($this->params['SECTION_ID'])
			{
				$arFieldsList['SECTION_ID'] = $this->params['SECTION_ID'];
				$arFieldsList['INCLUDE_SUBSECTIONS'] = 'Y';
			}
			if(is_array($this->fparams))
			{
				$propsDef = $this->GetIblockProperties($this->params['IBLOCK_ID']);
				foreach($this->fparams as $k2=>$ffilter)
				{
					if($ffilter['USE_FILTER_FOR_DEACTIVATE']=='Y' && (!empty($ffilter['UPLOAD_VALUES']) || !empty($ffilter['NOT_UPLOAD_VALUES'])))
					{
						$fieldName = '';
						$fieldFull = $this->params['FIELDS'][$k2];
						list($xpath, $field) = explode(';', $fieldFull, 2);
						
						if(strpos($field, 'IE_')===0)
						{
							$fieldName = substr($field, 3);
							if(strpos($fieldName, '|')!==false) $fieldName = current(explode('|', $fieldName));
						}
						elseif(strpos($field, 'IP_PROP')===0)
						{
							$propId = substr($field, 7);
							$fieldName = 'PROPERTY_'.$propId;
							if($propsDef[$propId]['PROPERTY_TYPE']=='L')
							{
								$fieldName .= '_VALUE';
							}
							elseif($propsDef[$propId]['PROPERTY_TYPE']=='S' && $propsDef[$propId]['USER_TYPE']=='directory')
							{
								if(is_array($ffilter['UPLOAD_VALUES']))
								{
									foreach($ffilter['UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['UPLOAD_VALUES'][$k3] = $this->GetHighloadBlockValue($propsDef[$propId], $v3);
									}
								}
								if(is_array($ffilter['NOT_UPLOAD_VALUES']))
								{
									foreach($ffilter['NOT_UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['NOT_UPLOAD_VALUES'][$k3] = $this->GetHighloadBlockValue($propsDef[$propId], $v3);
									}
								}
							}
							elseif($propsDef[$propId]['PROPERTY_TYPE']=='E')
							{
								if(is_array($ffilter['UPLOAD_VALUES']))
								{
									foreach($ffilter['UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['UPLOAD_VALUES'][$k3] = $this->GetIblockElementValue($propsDef[$propId], $v3, $ffilter);
									}
								}
								if(is_array($ffilter['NOT_UPLOAD_VALUES']))
								{
									foreach($ffilter['NOT_UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['NOT_UPLOAD_VALUES'][$k3] = $this->GetIblockElementValue($propsDef[$propId], $v3, $ffilter);
									}
								}
							}
						}
						if(strlen($fieldName) > 0)
						{
							if(!empty($ffilter['UPLOAD_VALUES']))
							{
								$arFieldsList[$fieldName] = $ffilter['UPLOAD_VALUES'];
							}
							elseif(!empty($ffilter['NOT_UPLOAD_VALUES']))
							{
								$arFieldsList['!'.$fieldName] = $ffilter['NOT_UPLOAD_VALUES'];
							}
						}
					}
				}
			}
		
			while($this->stepparams['deactivate_element_first'] < $this->stepparams['deactivate_element_last'])
			{
				$arUpdatedIds = \Bitrix\EsolImportxml\Utils::GetPartIdsFromFile($this->fileElementsId, $this->stepparams['deactivate_element_first']);
				if(empty($arUpdatedIds))
				{
					$this->stepparams['deactivate_element_first'] = $this->stepparams['deactivate_element_last'];
					continue;
				}
				$lastElement = end($arUpdatedIds);
				
				$arFields = $arFieldsList;
				$arFields["CHECK_PERMISSIONS"] = "N";
				if($this->stepparams['begin_time'])
				{
					$arFields['<TIMESTAMP_X'] = $this->stepparams['begin_time'];
				}
				
				$arSubFields = $this->GetMissingFilter(false, $arFields['IBLOCK_ID']);
				
				if($offersExists && ($arOfferIblock = $this->GetCachedOfferIblock($arFields['IBLOCK_ID'])))
				{
					$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
					$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
					$arOfferFields = array("IBLOCK_ID" => $OFFERS_IBLOCK_ID);
					$arSubOfferFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
					if(!empty($arSubOfferFields))
					{
						if(count($arSubOfferFields) > 1) $arOfferFields[] = array_merge(array('LOGIC' => 'OR'), $arSubOfferFields);
						else $arOfferFields = array_merge($arOfferFields, $arSubOfferFields);
						$arSubFields['ID'] = \CIBlockElement::SubQuery('PROPERTY_'.$OFFERS_PROPERTY_ID, $arOfferFields);
					}
				}
				
				if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
				else $arFields = array_merge($arFields, $arSubFields);
				
				$arFields['!ID'] = $arUpdatedIds;
				if($this->stepparams['deactivate_element_first'] > 0) $arFields['>ID'] = $this->stepparams['deactivate_element_first'];
				if($lastElement < $this->stepparams['deactivate_element_last']) $arFields['<=ID'] = $lastElement;
				$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
				while($arr = $dbRes->Fetch())
				{
					if($this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y')
					{
						if($offersExists)
						{
							$this->DeactivateAllOffersByProductId($arr['ID'], $arFields['IBLOCK_ID'], $time, true);
						}
						\CIblockElement::Delete($arr['ID']);
						$this->stepparams['old_removed_line']++;
					}
					else
					{
						$this->MissingElementsUpdate($arr['ID'], $arFields['IBLOCK_ID'], false);

						if($offersExists)
						{
							$this->DeactivateAllOffersByProductId($arr['ID'], $arFields['IBLOCK_ID'], $time);
						}
					}
					
					$this->SaveStatusImport();
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
				if($offersExists)
				{
					$ret = $this->DeactivateOffersByProductIds($arUpdatedIds, $arFields['IBLOCK_ID'], $time);
					if(is_array($ret)) return $ret;
				}

				$this->stepparams['deactivate_element_first'] = $lastElement;
			}
			$this->SaveStatusImport();
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		
		if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		if(($this->params['SECTION_EMPTY_DEACTIVATE']=='Y' || $this->params['SECTION_NOTEMPTY_ACTIVATE']=='Y') && class_exists('\Bitrix\Iblock\SectionElementTable'))
		{
			$this->stepparams['curstep'] = 'deactivate_sections';

			$sectionId = (int)$this->params['SECTION_ID'];
			$arFilterSections  = array('IBLOCK_ID' => $this->params['IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N');
			$arFilterSE = array('IBLOCK_SECTION.IBLOCK_ID' => $this->params['IBLOCK_ID'], 'IBLOCK_ELEMENT.ACTIVE' => 'Y');
			
			if($sectionId)
			{
				$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$sectionId, 'CHECK_PERMISSIONS' => 'N'), false, array('LEFT_MARGIN', 'RIGHT_MARGIN'));
				if($arr = $dbRes->Fetch())
				{
					$arFilterSections['>=LEFT_MARGIN'] = $arr['LEFT_MARGIN'];
					$arFilterSections['<=RIGHT_MARGIN'] = $arr['RIGHT_MARGIN'];
					$arFilterSE['>=IBLOCK_SECTION.LEFT_MARGIN'] = $arr['LEFT_MARGIN'];
					$arFilterSE['<=IBLOCK_SECTION.RIGHT_MARGIN'] = $arr['RIGHT_MARGIN'];
				}
			}
			
			$arListSections = array();
			$dbRes = \CIBlockSection::GetList(array('DEPTH_LEVEL'=>'DESC'), $arFilterSections, false, array('ID', 'IBLOCK_SECTION_ID'));
			while($arr = $dbRes->Fetch())
			{
				$arListSections[$arr['ID']] = ($sectionId==$arr['ID'] ? false : $arr['IBLOCK_SECTION_ID']);
			}
			
			$arActiveSections = array();
			$dbRes = \Bitrix\Iblock\SectionElementTable::GetList(array('filter'=>$arFilterSE, 'group'=>array('IBLOCK_SECTION_ID'), 'select'=>array('IBLOCK_SECTION_ID')));
			while($arr = $dbRes->Fetch())
			{
				$sid = $arr['IBLOCK_SECTION_ID'];
				$arActiveSections[] = $sid;
				while($sid = $arListSections[$sid])
				{
					$arActiveSections[] = $sid;
				}
			}
			
			$sect = new \CIBlockSection();
			if($this->params['SECTION_NOTEMPTY_ACTIVATE']=='Y')
			{
				if(!empty($arActiveSections))
				{
					$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$arActiveSections, 'ACTIVE'=>'N', 'CHECK_PERMISSIONS' => 'N'), false, array('ID'));
					while($arr = $dbRes->Fetch())
					{
						$sect->Update($arr['ID'], array('ACTIVE'=>'Y'));
						$this->SaveStatusImport();
						if($this->CheckTimeEnding($time)) return $this->GetBreakParams();						
					}
				}
			}
			
			if($this->params['SECTION_EMPTY_DEACTIVATE']=='Y')
			{
				$arInactiveSections = array_diff(array_keys($arListSections), $arActiveSections);
				if(!empty($arInactiveSections))
				{
					$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$arInactiveSections, 'ACTIVE'=>'Y', 'CHECK_PERMISSIONS' => 'N'), false, array('ID'));
					while($arr = $dbRes->Fetch())
					{
						$sect->Update($arr['ID'], array('ACTIVE'=>'N'));
						$this->SaveStatusImport();
						if($this->CheckTimeEnding($time)) return $this->GetBreakParams();						
					}
				}
			}
		}
		
		$this->SaveStatusImport(true);
		
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance();
		$oProfile->UpdateFileHash($this->pid, $this->filename);
		
		foreach(GetModuleEvents("esol.importxml", "OnEndImport", true) as $arEvent)
		{
			$bEventRes = ExecuteModuleEventEx($arEvent, array($this->pid));
		}
		
		return $this->GetBreakParams('finish');
	}
	
	public function DeactivateAllOffersByProductId($ID, $IBLOCK_ID, $time, $deleteMode = false)
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		$arFields = array(
			'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
			'PROPERTY_'.$OFFERS_PROPERTY_ID => $ID,
			'CHECK_PERMISSIONS' => 'N'
		);
		
		$arSubFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
		
		if(!empty($arSubFields))
		{
			if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
			else $arFields = array_merge($arFields, $arSubFields);
						
			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
			while($arr = $dbRes->Fetch())
			{
				if($deleteMode)
				{
					\CIblockElement::Delete($arr['ID']);
					$this->stepparams['offer_old_removed_line']++;
				}
				else
				{
					$this->MissingElementsUpdate($arr['ID'], $OFFERS_IBLOCK_ID, true);
				}
				if($this->CheckTimeEnding($time))
				{
					return $this->GetBreakParams();
				}
			}
		}
	}
	
	public function DeactivateOffersByProductIds(&$arElementIds, $IBLOCK_ID, $time)
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		while($this->stepparams['deactivate_offer_first'] < $this->stepparams['deactivate_offer_last'])
		{
			$arUpdatedIds = \Bitrix\EsolImportxml\Utils::GetPartIdsFromFile($this->fileOffersId, $this->stepparams['deactivate_offer_first']);
			if(empty($arUpdatedIds))
			{
				$this->stepparams['deactivate_offer_first'] = $this->stepparams['deactivate_offer_last'];
				continue;
			}
			$lastElement = end($arUpdatedIds);

			$arFields = array(
				'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
				'PROPERTY_'.$OFFERS_PROPERTY_ID => $arElementIds,
				'!ID' => $arUpdatedIds,
				'CHECK_PERMISSIONS' => 'N'
			);
			
			$arSubFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
			if(!empty($arSubFields))
			{
				if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
				else $arFields = array_merge($arFields, $arSubFields);
			}
			
			if($this->stepparams['begin_time'])
			{
				$arFields['<TIMESTAMP_X'] = $this->stepparams['begin_time'];
			}
			if($this->stepparams['deactivate_offer_first'] > 0) $arFields['>ID'] = $this->stepparams['deactivate_offer_first'];
			if($lastElement < $this->stepparams['deactivate_offer_last']) $arFields['<=ID'] = $lastElement;
			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
			while($arr = $dbRes->Fetch())
			{
				if($this->params['OFFER_MISSING_REMOVE_ELEMENT']=='Y')
				{
					\CIblockElement::Delete($arr['ID']);
					$this->stepparams['offer_old_removed_line']++;
				}
				else
				{
					$this->MissingElementsUpdate($arr['ID'], $OFFERS_IBLOCK_ID, true);
				}
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time))
				{
					return $this->GetBreakParams();
				}
			}
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
			$this->stepparams['deactivate_offer_first'] = $lastElement;
		}
		$this->stepparams['deactivate_offer_first'] = 0;
	}
	
	public function MissingElementsUpdate($ID, $IBLOCK_ID, $isOffer = false)
	{
		if(!$ID) return;
		$prefix = ($isOffer ? 'OFFER' : 'CELEMENT');
		$updateElement = false;
		if($this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params[$prefix.'_MISSING_TO_ZERO']=='Y')
		{
			\CCatalogProduct::Update($ID, array('QUANTITY'=>0));
			$dbRes2 = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$ID, '>AMOUNT'=>0), false, false, array('ID'));
			while($arStore = $dbRes2->Fetch())
			{
				\CCatalogStoreProduct::Update($arStore["ID"], array('AMOUNT'=>0));
			}
			if($isOffer) $this->stepparams['offer_zero_stock_line']++;
			else $this->stepparams['zero_stock_line']++;
		}
		if($this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params[$prefix.'_MISSING_REMOVE_PRICE']=='Y')
		{
			$dbRes = \CPrice::GetList(array(), array('PRODUCT_ID'=>$ID), false, false, $arKeys);
			while($arPrice = $dbRes->Fetch())
			{
				\CPrice::Delete($arPrice["ID"]);
			}
		}
		
		$arDefaults = array();
		if($this->params[$prefix.'_MISSING_DEFAULTS'])
		{
			$arDefaults = unserialize(base64_decode($this->params[$prefix.'_MISSING_DEFAULTS']));
			if(!is_array($arDefaults)) $arDefaults = array();
		}
		if(!empty($arDefaults))
		{
			$arElemVals = array();
			$arProps = array();
			$arStores = array();
			$arPrices = array();
			foreach($arDefaults as $propKey=>$propVal)
			{
				if(strpos($propKey, 'IE_')===0)
				{
					$arElemVals[substr($propKey, 3)] = $propVal;
				}
				elseif(preg_match('/ICAT_STORE(\d+)_AMOUNT/', $propKey, $m))
				{
					$arStores[$m[1]] = array('AMOUNT' => $propVal);
				}
				elseif(preg_match('/ICAT_PRICE(\d+)_PRICE/', $propKey, $m))
				{
					$arPrices[$m[1]] = array('PRICE' => $propVal);
				}
				else
				{
					$arProps[$propKey] = $propVal;
				}
			}
			if(!empty($arPrices))
			{
				$this->SavePrice($ID, $arPrices);
			}
			if(!empty($arStores))
			{
				$this->SaveStore($ID, $arStores);
			}
			if(!empty($arProps))
			{
				$this->SaveProperties($ID, $IBLOCK_ID, $arProps);
			}
			if(!empty($arElemVals))
			{
				$el = new \CIblockElement();
				$el->Update($ID, $arElemVals);
				$updateElement = true;
			}
		}
		
		$el = new \CIblockElement();
		if($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params[$prefix.'_MISSING_DEACTIVATE']=='Y')
		{
			$el->Update($ID, array('ACTIVE'=>'N'));
			$updateElement = true;
			if($isOffer) $this->stepparams['offer_killed_line']++;
			else $this->stepparams['killed_line']++;
		}
		
		if(!$updateElement && $this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']!='Y')
		{
			$el->Update($ID, array('ID'=>$ID));
		}
	}
	
	public function GetMissingFilter($isOffer = false, $IBLOCK_ID = 0)
	{
		$arSubFields = array();
		$prefix = ($isOffer ? 'OFFER' : 'CELEMENT');
		if($this->params[$prefix.'_MISSING_REMOVE_ELEMENT']=='Y') return $arSubFields;
		if($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params[$prefix.'_MISSING_DEACTIVATE']=='Y') $arSubFields['ACTIVE'] = 'Y';
		if($this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params[$prefix.'_MISSING_TO_ZERO']=='Y') $arSubFields['>CATALOG_QUANTITY'] = 0;
		if($this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params[$prefix.'_MISSING_REMOVE_PRICE']=='Y') $arSubFields['!CATALOG_PRICE_'.$this->GetBasePriceId()] = false;
		
		$arDefaults = array();
		if($this->params[$prefix.'_MISSING_DEFAULTS'])
		{
			$arDefaults = unserialize(base64_decode($this->params[$prefix.'_MISSING_DEFAULTS']));
			if(!is_array($arDefaults)) $arDefaults = array();
		}
		if($IBLOCK_ID > 0 && !empty($arDefaults))
		{
			$propsDef = $this->GetIblockProperties($IBLOCK_ID);
			foreach($arDefaults as $uid=>$valUid)
			{				
				if(strpos($uid, 'IE_')===0)
				{
					$uid = substr($uid, 3);
				}
				elseif(preg_match('/ICAT_STORE(\d+)_AMOUNT/', $uid, $m))
				{
					$uid = 'CATALOG_STORE_AMOUNT_'.$m[1];
				}
				elseif(preg_match('/ICAT_PRICE(\d+)_PRICE/', $uid, $m))
				{
					$uid = 'CATALOG_PRICE_'.$m[1];
					if($valUid=='-') $valUid = false;
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, array());
					}
					$uid = 'PROPERTY_'.$uid;
				}
				$arSubFields['!'.$uid] = $valUid;
			}
		}

		return $arSubFields;
	}
	
	public function InitImport($type = 'element')
	{
		if($type == 'element' && $this->params['GROUPS']['ELEMENT'])
		{
			$emptyFields = array();
			foreach($this->params['ELEMENT_UID'] as $uidField)
			{
				if(!is_array($this->params['FIELDS']) || count(preg_grep('/;'.$uidField.'$/', $this->params['FIELDS']))==0)
				{
					$emptyFields[] = $uidField;
				}
			}
			if(!empty($emptyFields))
			{
				$arFieldsDef = $this->fl->GetFields($this->params['IBLOCK_ID']);
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					if(strpos($field, 'IE_')===0)
					{
						$emptyFieldNames[] = $arFieldsDef['element']['items'][$field];
					}
					elseif(strpos($field, 'IP_PROP')===0)
					{
						$emptyFieldNames[] = $arFieldsDef['prop']['items'][$field];
					}
				}
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		if($type == 'section' && $this->params['GROUPS']['SECTION'])
		{
			$emptyFields = array();
			$sectionUid = $this->params['SECTION_UID'];
			if(!is_array($sectionUid)) $sectionUid = array($sectionUid);
			foreach($sectionUid as $uidField)
			{
				$uidField = 'ISECT_'.$uidField;
				if(!is_array($this->params['FIELDS']) || count(preg_grep('/;'.$uidField.'$/', $this->params['FIELDS']))==0)
				{
					$emptyFields[] = $uidField;
				}
			}
			if(!empty($emptyFields))
			{
				$arFieldsDef = $this->fl->GetIblockSectionFields('');
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					$emptyFieldNames[] = $arFieldsDef[$field]['name'];
				}
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_SECTION_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		$this->fieldOnlyNew = array();
		$this->fieldOnlyNewOffer = array();
		$this->fieldsForSkuGen = array();
		$this->fieldSettings = array();
		foreach($this->params['FIELDS'] as $k=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if(strpos($field, '|')!==false) $field = substr($field, 0, strpos($field, '|'));
			
			$this->fieldSettings[$field] = $this->fparams[$k];
			
			if($this->fparams[$k]['SET_NEW_ONLY']=='Y')
			{
				if(strpos($field, 'OFFER_')===0) $this->fieldOnlyNewOffer[] = substr($field, 6);
				else $this->fieldOnlyNew[] = $field;
			}
			
			if(strpos($field, 'OFFER_')===0 && $this->fparams[$k]['USE_FOR_SKU_GENERATE']=='Y')
			{
				$this->fieldsForSkuGen[] = $k;
			}
		}
		$this->conv = new \Bitrix\EsolImportxml\Conversion($this, $this->params['IBLOCK_ID'], $this->fieldSettings);
		
		//$this->fileEncoding = \Bitrix\EsolImportxml\Utils::GetXmlEncoding($this->filename);
		$this->fileEncoding = 'utf-8';
		$this->siteEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
		//$this->xmlObject = simplexml_load_file($this->filename);
		
		$this->InitXml($type);
		
		return true;
	}
	
	public function InitXml($type)
	{
		if($type == 'element')
		{
			if(!isset($this->xmlCurrentRow)) $this->xmlCurrentRow = intval($this->stepparams['xmlCurrentRow']);
			//$this->CheckGroupParams('ELEMENT', 'yml_catalog/shop/offers', 'yml_catalog/shop/offers/offer');
			//$this->CheckGroupParams('ELEMENT', 'yml_catalog/offers', 'yml_catalog/offers/offer');
			if(preg_match('/\/offers$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/offer');
			if(preg_match('/\/'.Loc::getMessage("ESOL_IX_PRODUCTS_TAG_1C").'$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/'.Loc::getMessage("ESOL_IX_PRODUCT_TAG_1C"));
			
			$count = 0;
			$this->xmlElements = $this->GetXmlObject($count, $this->xmlCurrentRow, $this->params['GROUPS']['ELEMENT']);
			$this->xmlElementsCount = $this->stepparams['total_file_line'] = $count;
		}
		
		if($type == 'section')
		{
			if(!isset($this->xmlSectionCurrentRow)) $this->xmlSectionCurrentRow = intval($this->stepparams['xmlSectionCurrentRow']);
			//$this->CheckGroupParams('SECTION', 'yml_catalog/shop/categories', 'yml_catalog/shop/categories/category');
			//$this->CheckGroupParams('SECTION', 'yml_catalog/categories', 'yml_catalog/categories/category');
			if(preg_match('/\/categories$/', $this->params['GROUPS']['SECTION'])) $this->CheckGroupParams('SECTION', $this->params['GROUPS']['SECTION'], $this->params['GROUPS']['SECTION'].'/category');
			if(preg_match('/\/'.Loc::getMessage("ESOL_IX_SECTIONS_TAG_1C").'$/', $this->params['GROUPS']['SECTION'])) $this->CheckGroupParams('SECTION', $this->params['GROUPS']['SECTION'], $this->params['GROUPS']['SECTION'].'/'.Loc::getMessage("ESOL_IX_SECTION_TAG_1C"));
			
			$count = 0;
			$this->xmlSections = $this->GetXmlObject($count, 0, $this->params['GROUPS']['SECTION'], true);
			$this->xmlSectionsCount = $count;
		}
		return true;
	}
	
	public function CheckGroupParams($type, $xpathFrom, $xpathTo)
	{
		if(trim($this->params['GROUPS'][$type], '/')==$xpathFrom)
		{
			$xmlSectionCurrentRow = $this->xmlSectionCurrentRow;
			$xmlCurrentRow = $this->xmlCurrentRow;
			$maxStepRows = $this->maxStepRows;
			$this->maxStepRows = 2;
			$xmlElements = $this->GetXmlObject(($count=0), 0, $xpathTo);
			if(is_array($xmlElements) && count($xmlElements) > 0)
			{
				$this->params['GROUPS'][$type] = $xpathTo;
			}
			$this->xmlSectionCurrentRow = $xmlSectionCurrentRow;
			$this->xmlCurrentRow = $xmlCurrentRow;
			$this->maxStepRows = $maxStepRows;
		}
	}
	
	public function GetXmlObject(&$countRows, $beginRow, $xpath, $nolimit = false)
	{
		$xpath = trim($xpath);
		if(strlen($xpath) == 0) return;
		
		$arXpath = explode('/', trim($xpath, '/'));
		$this->xpath = '/'.$xpath;
		$countRows = 0;
		if(!class_exists('\XMLReader'))
		{
			$this->xmlRowDiff = 0;
			$this->xmlObject = simplexml_load_file($this->filename);
			//$rows = $this->xmlObject->xpath('/'.$xpath);
			$rows = $this->Xpath($this->xmlObject, '/'.$xpath);
			$countRows = count($rows); 
			return $rows;
		}

		$xml = new \XMLReader();
		$res = $xml->open($this->filename);
		
		$arObjects = array();
		$arObjectNames = array();
		$arXPaths = array();
		$curDepth = 0;
		$isRead = false;
		$countLoadedRows = 0;
		$break = false;
		$countRows = -1;
		while(($isRead || $xml->read()) && !$break) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $curName = $xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				
				$curXPath = implode('/', $arObjectNames);
				if(strpos($xpath, $curXPath)!==0 && strpos($curXPath, $xpath)!==0)
				{
					$isRead = false;
					while(!$isRead && $xml->next($arXpath[$curDepth])) $isRead = true;
					continue;
				}
				if($xpath==$curXPath)
				{
					$countRows++;
					while($countRows < $beginRow && $xml->next($curName)) $countRows++;
				}
				if($countRows < $beginRow && strlen($curXPath)>=strlen($xpath)) continue;
				if($xpath==$curXPath)
				{
					$countLoadedRows++;
					/*if($countLoadedRows > $this->maxStepRows)
					{
						$break = true;
						continue;
					}*/
					if($countLoadedRows > $this->maxStepRows && !$nolimit)
					{
						while($xml->next($curName)) $countRows++;
					}
				}
				if($countLoadedRows > $this->maxStepRows && !$nolimit) continue;
				
				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					while($xml->moveToNextAttribute ())
					{
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
				}
				$xml->moveToElement();
				

				$curName = $xml->name;
				$curValue = null;
				$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);

				while($xml->read() && $xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE){}
				if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
				{
					$curValue = $xml->value;
				}
				else
				{
					$isRead = true;
				}

				if($curDepth == 0)
				{
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					$arObjects[$curDepth] = &$xmlObj;
				}
				else
				{
					$curValue = str_replace('&', '&amp;', $curValue);
					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}			

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();
		$countRows++;
		
		if(is_object($xmlObj))
		{
			$this->xmlRowDiff = $beginRow;
			$this->xmlObject = $xmlObj;
			//return $this->xmlObject->xpath('/'.$xpath);
			return $this->Xpath($this->xmlObject, '/'.$xpath);
		}
		return false;
	}
	
	public function GetPartXmlObject($xpath)
	{
		if(!class_exists('\XMLReader'))
		{
			$xmlObject = simplexml_load_file($this->filename);
			//$rows = $xmlObject->xpath('/'.$xpath);
			$rows = $this->Xpath($xmlObject, '/'.$xpath);
			return $rows;
		}
		
		$xml = new \XMLReader();
		$res = $xml->open($this->filename);
		
		$arObjects = array();
		$arObjectNames = array();
		$arXPaths = array();
		$curDepth = 0;
		$isRead = false;
		$break = false;
		while(($isRead || $xml->read()) && !$break) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				
				$curXPath = implode('/', $arObjectNames);
				if(strpos($xpath, $curXPath)!==0 && strpos($curXPath, $xpath)!==0) continue;
				
				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					while($xml->moveToNextAttribute ())
					{
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
				}
				$xml->moveToElement();
				

				$curName = $xml->name;
				$curValue = null;
				$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);

				while($xml->read() && $xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE){}
				if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
				{
					$curValue = $xml->value;
				}
				else
				{
					$isRead = true;
				}

				if($curDepth == 0)
				{
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					$arObjects[$curDepth] = &$xmlObj;
				}
				else
				{
					$curValue = str_replace('&', '&amp;', $curValue);
					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}			

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();

		if(is_object($xmlObj))
		{
			//return $xmlObj->xpath('/'.$xpath);
			return $this->Xpath($xmlObj, '/'.$xpath);
		}
		return false;
	}
	
	public function GetBreakParams($action = 'continue')
	{
		$arStepParams = array(
			'params'=> array_merge($this->stepparams, array(
				'xmlCurrentRow' => intval($this->xmlCurrentRow),
				'xmlSectionCurrentRow' => intval($this->xmlSectionCurrentRow),
				'sectionIds' => $this->sectionIds
			)),
			'action' => $action,
			'errors' => $this->errors,
			'sessid' => bitrix_sessid()
		);
		
		if($action == 'continue')
		{
			file_put_contents($this->tmpfile, serialize($arStepParams['params']));
			unset($arStepParams['params']['sectionIds']);
			if(file_exists($this->imagedir))
			{
				DeleteDirFilesEx(substr($this->imagedir, strlen($_SERVER['DOCUMENT_ROOT'])));
			}
		}
		elseif(file_exists($this->tmpdir))
		{
			DeleteDirFilesEx(substr($this->tmpdir, strlen($_SERVER['DOCUMENT_ROOT'])));
			unlink($this->procfile);
		}
		
		return $arStepParams;
	}
	
	public function CheckSkipLine($arItem, $type='element')
	{
		$load = true;
		
		if($load)
		{
			foreach($this->fparams as $k=>$v)
			{
				if(!is_array($v)) continue;
				
				list($xpath, $field) = explode(';', $this->params['FIELDS'][$k], 2);
				if($type=='element' && (strpos($field, 'ISECT_')===0 || strpos($field, 'ISUBSECT_')===0)) continue;
				if($type=='section' && strpos($field, 'ISECT_')!==0) continue;
				if($type=='subsection' && strpos($field, 'ISUBSECT_')!==0) continue;
				if(strpos($xpath, $this->params['GROUPS'][ToUpper($type)])!==0) continue;
				
				if(is_array($v['UPLOAD_VALUES']) || is_array($v['NOT_UPLOAD_VALUES']) || $v['FILTER_EXPRESSION'])
				{
					$val = $arItem[$k];
					$valOrig = $arItem['~'.$k];
					$val = $this->ApplyConversions($valOrig, $v['CONVERSION'], array());
					$val = ToLower(trim($val));
				}
				else
				{
					$val = '';
				}
				
				if(is_array($v['UPLOAD_VALUES']))
				{
					$subload = false;
					foreach($v['UPLOAD_VALUES'] as $needval)
					{
						$needval = ToLower(trim($needval));
						if($needval==$val 
							|| ($needval=='{empty}' && strlen($val)==0)
							|| ($needval=='{not_empty}' && strlen($val) > 0))
						{
							$subload = true;
						}
					}
					$load = ($load && $subload);
				}
				
				if(is_array($v['NOT_UPLOAD_VALUES']))
				{
					$subload = true;
					foreach($v['NOT_UPLOAD_VALUES'] as $needval)
					{
						$needval = ToLower(trim($needval));
						if($needval==$val 
							|| ($needval=='{empty}' && strlen($val)==0)
							|| ($needval=='{not_empty}' && strlen($val) > 0))
						{
							$subload = false;
						}
					}
					$load = ($load && $subload);
				}
				
				if($v['FILTER_EXPRESSION'])
				{
					$load = ($load && $this->ExecuteFilterExpression($valOrig, $v['FILTER_EXPRESSION']));
				}
			}
		}
		
		return !$load;
	}
	
	public function ExecuteFilterExpression($val, $expression, $altReturn = true)
	{
		$expression = trim($expression);
		try{				
			if(stripos($expression, 'return')===0)
			{
				return eval($expression.';');
			}
			elseif(preg_match('/\$val\s*=/', $expression))
			{
				eval($expression.';');
				return $val;
			}
			else
			{
				return eval('return '.$expression.';');
			}
		}catch(Exception $ex){
			return $altReturn;
		}
	}
	
	public function ExecuteOnAfterSaveHandler($handler, $ID)
	{
		try{				
			eval($handler.';');
		}catch(Exception $ex){}
	}
	
	public function GetNextSectionRecord($time)
	{
		/*while(isset($this->xmlSections[$this->xmlSectionCurrentRow - $this->xmlRowDiff])
			|| ($this->xmlSectionsCount > $this->xmlSectionCurrentRow
				&& $this->InitXml('section')
				&& isset($this->xmlSections[$this->xmlSectionCurrentRow - $this->xmlRowDiff])))
		{*/
		if(!isset($this->xmlSectionCurrentRow) || !is_numeric($this->xmlSectionCurrentRow))
		{
			$this->xmlSectionCurrentRow = 0;
		}
		$moveCnt = 0;
		while(isset($this->xmlSections[$this->xmlSectionCurrentRow]) && ($moveCnt < count($this->xmlSections)))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlSections[$this->xmlSectionCurrentRow];
			$arItem = array();
			$break = $unset = false;
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($fieldName, 'ISECT_')!==0) continue;
				if(strlen($this->params['GROUPS']['SUBSECTION']) > 0 && strpos($xpath, $this->params['GROUPS']['SUBSECTION'])===0) continue;
				
				$xpath = substr($xpath, strlen($this->params['GROUPS']['SECTION']) + 1);
				if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
				else $arPath = array();
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							$val[] = (string)$v->attributes()->{$attr};
						}
					}
					else
					{
						$val = (string)$simpleXmlObj2->attributes()->{$attr};
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							$val[] = (string)$v;
						}
					}
					else
					{
						$val = (string)$simpleXmlObj2;
					}					
				}
				
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
				}
				
				if(in_array($fieldName, array('ISECT_PARENT_TMP_ID', 'ISECT_TMP_ID')))
				{
					$conversions = $this->fparams[$key]['CONVERSION'];
					if(!empty($conversions))
					{
						$val = $this->ApplyConversions($val, $conversions, $arItem, array('KEY'=>$fieldName, 'NAME'=>$fieldName), array());
					}
				}
				
				if($fieldName=='ISECT_PARENT_TMP_ID' && trim($val) && !isset($this->sectionIds[trim($val)]))
				{
					$break = true;
					break;
				}
				if($fieldName=='ISECT_TMP_ID' && trim($val) && isset($this->sectionIds[trim($val)]))
				{
					$unset = true;
					$break = true;
					break;
				}
		
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}
			if($break)
			{
				if($unset)
				{
					unset($this->xmlSections[$this->xmlSectionCurrentRow]);
					$this->xmlSections = array_values($this->xmlSections);
					$this->xmlSectionCurrentRow = 0;
					$moveCnt = 0;
				}
				else
				{
					$tmpSection = $this->xmlSections[$this->xmlSectionCurrentRow];
					unset($this->xmlSections[$this->xmlSectionCurrentRow]);
					$this->xmlSections = array_values($this->xmlSections);
					$this->xmlSections[] = $tmpSection;
					$this->xmlSectionCurrentRow = 0;
					$moveCnt++;
				}
				continue;
			}
			//$this->xmlSectionCurrentRow++;
			unset($this->xmlSections[$this->xmlSectionCurrentRow]);
			$this->xmlSections = array_values($this->xmlSections);
			$this->xmlSectionCurrentRow = 0;
			
			if(!$this->CheckSkipLine($arItem, 'section'))
			{
				return $arItem;
			}
		}
		
		return false;		
	}
	
	public function GetNextSubsection($ID, $arItem, $xmlSubsectionCurrentRow)
	{
		$currentSectionXpath = $this->currentSectionXpath;
		if(!is_object($this->currentXmlObj)) return false;
		//while(isset($this->xmlSubsections[$xmlSubsectionCurrentRow]))
		while(($simpleXmlObj = $this->currentXmlObj)
			&& ($this->currentSectionXpath = $currentSectionXpath.'['.($xmlSubsectionCurrentRow + 1).']')
			&& ($this->xpathReplace = array('FROM' => $this->params['GROUPS']['SUBSECTION'], 'TO' => $this->currentSectionXpath))
			&& ($subsectionXpath = substr($this->xpath, 1))
			&& ($objXpath = substr($this->ReplaceXpath($this->params['GROUPS']['SUBSECTION']), strlen($subsectionXpath) + 1))
			//&& ($simpleXmlObj->xpath($objXpath))
			&& ($this->Xpath($simpleXmlObj, $objXpath))
			)
		{
			/*$simpleXmlObj = $this->currentXmlObj;
			$this->currentSectionXpath = $currentSectionXpath.'['.($xmlSubsectionCurrentRow + 1).']';
			$this->xpathReplace = array(
				//'FROM' => $currentSectionXpath,
				'FROM' => $this->params['GROUPS']['SUBSECTION'],
				'TO' => $this->currentSectionXpath
			);
			$subsectionXpath = substr($this->xpath, 1);*/
			$this->xmlPartObjects = array();

			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath, $this->params['GROUPS']['SUBSECTION'])!==0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($subsectionXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
				
				$xpath = substr($this->ReplaceXpath($xpath), strlen($subsectionXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v->attributes()->{$attr};
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2->attributes()->{$attr};
						}
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v;
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2;
						}
					}					
				}
				
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
				}
		
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}

			if(!$this->CheckSkipLine($arItem, 'subsection'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextRecord($time)
	{
		while(isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])
			|| (!$this->elementInSection 
				&& $this->xmlElementsCount > $this->xmlCurrentRow
				&& $this->InitXml('element')
				&& isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff];
			$this->xmlPartObjects = array();
			
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($fieldName, 'ISECT_')===0) continue;
				if(strlen($this->params['GROUPS']['OFFER']) > 0 && strpos($xpath, $this->params['GROUPS']['OFFER'])===0) continue;
				if($this->propertyInElement && strpos($xpath, $this->params['GROUPS']['PROPERTY'])===0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($m[1], strlen($this->params['GROUPS']['ELEMENT']) + 1);
					}
				}
				
				$xpath = substr($xpath, strlen($this->params['GROUPS']['ELEMENT']) + 1);
				$arPath = array_diff(explode('/', $xpath), array(''));
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v->attributes()->{$attr};
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2->attributes()->{$attr};
						}
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v;
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2;
						}
					}					
				}
				
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
				}
		
				/*$arItem[$fieldName] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$fieldName] = $val;*/
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'element'))
			{
				return $arItem;
			}
			if($this->CheckTimeEnding($time)) return false;
		}
		
		return false;
	}
	
	public function GetNextOffer($ID, $arItem)
	{
		while(isset($this->xmlOffers[$this->xmlOfferCurrentRow]))
		{
			$simpleXmlObj = $this->currentXmlObj;
			//$this->currentXmlObj = $simpleXmlObj = $this->xmlOffers[$this->xmlOfferCurrentRow];
			$this->xmlPartObjects = array();
		
			$this->xpathReplace = array(
				'FROM' => $this->params['GROUPS']['OFFER'],
				'TO' => $this->params['GROUPS']['OFFER'].'['.($this->xmlOfferCurrentRow + 1).']'
			);
			$offerXpath = substr($this->xpath, 1);
			
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath, $this->params['GROUPS']['OFFER'])!==0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($offerXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
					
				$xpath = substr($this->ReplaceXpath($xpath), strlen($offerXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v->attributes()->{$attr};
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2->attributes()->{$attr};
						}
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v;
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2;
						}
					}					
				}
				
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
				}
		
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlOfferCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'offer'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextProperty()
	{
		while(isset($this->xmlProperties[$this->xmlPropertiesCurrentRow]))
		{
			$simpleXmlObj = $this->currentParentXmlObj;
			$this->currentXmlObj = $this->xmlProperties[$this->xmlPropertiesCurrentRow];
			$this->xmlPartObjects = array();
		
			$this->xpathReplace = array(
				'FROM' => $this->params['GROUPS']['PROPERTY'],
				'TO' => $this->params['GROUPS']['PROPERTY'].'['.($this->xmlPropertiesCurrentRow + 1).']'
			);
			$propertyXpath = substr($this->parentXpath, 1);
			
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath, $this->params['GROUPS']['PROPERTY'])!==0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($propertyXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
				
				$xpath = substr($this->ReplaceXpath($xpath), strlen($propertyXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);	
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v->attributes()->{$attr};
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2->attributes()->{$attr};
						}
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v;
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2;
						}
					}					
				}
				
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
				}
		
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlPropertiesCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'property'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function ReplaceXpath($xpath)
	{
		if(is_array($this->xpathReplace) && isset($this->xpathReplace['FROM']) && isset($this->xpathReplace['TO']))
		{
			$xpath = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $xpath);
		}
		return $xpath;
	}
	
	public function ReplaceConditionXpath($m)
	{
		$offerXpath = substr($this->xpath, 1);
		if(strpos($m[1], $offerXpath)===0)
		{
			return '{'.substr($this->ReplaceXpath($m[1]), strlen($offerXpath) + 1).'}';
		}
		else
		{
			return '{'.$this->ReplaceXpath($m[1]).'}';
		}
	}
	
	public function ReplaceConditionXpathToValue($m)
	{
		$xpath = $this->replaceXpath;
		$simpleXmlObj = $this->replaceSimpleXmlObj;
		$simpleXmlObj2 = $this->replaceSimpleXmlObj2;
		$xpath2 = $m[1];
		if(strpos($xpath2, $xpath)===0)
		{
			$xpath2 = substr($xpath2, strlen($xpath) + 1);
			$simpleXmlObj = $simpleXmlObj2;
		}
		else
		{
			$arXpath2 = $this->GetXPathParts($xpath2);
			if(strlen($arXpath2['xpath']) > 0)
			{
				if(!isset($this->xmlParts[$arXpath2['xpath']]))
				{
					$this->xmlParts[$arXpath2['xpath']] = $this->GetPartXmlObject($arXpath2['xpath']);
				}
				$xmlPart = $this->xmlParts[$arXpath2['xpath']];
				if(is_array($xmlPart))
				{
					$valXpath = $xpath;
					if(isset($this->parentXpath) && strlen($this->parentXpath) > 0) $valXpath = rtrim($this->parentXpath, '/').'/'.ltrim($valXpath, '/');
					$val = $this->GetValueByXpath($valXpath, $simpleXmlObj);
					
					foreach($xmlPart as $xmlObj)
					{
						if(strlen($arXpath2['subpath'])==0) $xmlObj2 = $xmlObj;
						//else $xmlObj2 = $xmlObj->xpath($arXpath2['subpath']);
						else $xmlObj2 = $this->Xpath($xmlObj, $arXpath2['subpath']);
						if(is_array($xmlObj2)) $xmlObj2 = current($xmlObj2);
						if($arXpath2['attr']!==false && is_callable(array($xmlObj2, 'attributes')))
						{
							$val2 = (string)$xmlObj2->attributes()->{$arXpath2['attr']};
						}
						else
						{
							$val2 = (string)$xmlObj2;
						}
						if($val2==$val)
						{
							$this->xmlPartObjects[$arXpath2['xpath']] = $xmlObj;
							return $val;
						}
					}
				}
			}
		}
		$arPath = explode('/', $xpath2);
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}
		if(count($arPath) > 0)
		{
			//$simpleXmlObj3 = $simpleXmlObj->xpath(implode('/', $arPath));
			$simpleXmlObj3 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
			if(count($simpleXmlObj3)==1) $simpleXmlObj3 = current($simpleXmlObj3);
		}
		else $simpleXmlObj3 = $simpleXmlObj;
		
		if(is_array($simpleXmlObj3)) $simpleXmlObj3 = current($simpleXmlObj3);
		$condVal = (string)(($attr!==false && is_callable(array($simpleXmlObj3, 'attributes'))) ? $simpleXmlObj3->attributes()->{$attr} : $simpleXmlObj3);
		return $condVal;
	}
	
	public function GetXPathParts($xpath)
	{
		$arPath = explode('/', $xpath);
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}
		$xpath2 = implode('/', $arPath);
		$xpath3 = '';
		if(strpos($xpath2, '//')!==false)
		{
			list($xpath2, $xpath3) = explode('//', $xpath2, 2);
		}
		return array('xpath'=>$xpath2, 'subpath' => $xpath3, 'attr'=>$attr);
	}
	
	public function CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2, $key=false)
	{
		if(empty($conditions)) return true;
		if($key!==false)
		{
			$arPath = explode('/', $xpath);
			$attr = false;
			if(strpos($arPath[count($arPath)-1], '@')===0)
			{
				$attr = substr(array_pop($arPath), 1);
			}
			//if(count($arPath) > 1 && ($cnt = count($simpleXmlObj->xpath(implode('/', $arPath)))) && $cnt > 1)
			if(count($arPath) > 1 && ($cnt = count($this->Xpath($simpleXmlObj, implode('/', $arPath)))) && $cnt > 1)
			{
				while(($lastElem = array_pop($arPath)) && (count($arPath) > 0) /*&& (count($this->Xpath($simpleXmlObj, implode('/', $arPath)))==$cnt)*/ && ($cnt2 = count($this->Xpath($simpleXmlObj, implode('/', $arPath)))) && $cnt2>=$cnt){$cnt3 = $cnt2;}
				/*Fix for missign tag*/
				$key2 = $key;
				if($cnt3 > $cnt)
				{
					$subpath = implode('/', $arPath).'/'.$lastElem;
					for($i=0; $i<min($key2+1, $cnt3); $i++)
					{
						$xpath2 = $subpath.'['.($i+1).']/'.substr($xpath, strlen($subpath) + 1);
						//if(count($simpleXmlObj->xpath($xpath2))==0) $key2++;
						if(count($this->Xpath($simpleXmlObj, $xpath2))==0) $key2++;
					}
				}
				/*/Fix for missign tag*/

				$xpathReplace = $this->xpathReplace;
				$this->xpathReplace = array(
					'FROM' => ltrim(implode('/', $arPath).'/'.$lastElem, '/'),
					'TO' => ltrim(implode('/', $arPath).'/'.$lastElem.'['.($key2+1).']', '/')
				);
				foreach($conditions as $k3=>$v3)
				{
					$conditions[$k3]['XPATH'] = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $conditions[$k3]['XPATH']);
					$conditions[$k3]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k3]['FROM']);
				}
				$this->xpathReplace = $xpathReplace;
			}
		}
		
		$k = 0;
		while(isset($conditions[$k]))
		{
			$v = $conditions[$k];
			$pattern = '/^\{(\S*)\}$/';
			if(preg_match($pattern, $v['FROM']))
			{
				$this->replaceXpath = $xpath;
				$this->replaceSimpleXmlObj = $simpleXmlObj;
				$this->replaceSimpleXmlObj2 = $simpleXmlObj2;
				$v['FROM'] = preg_replace_callback($pattern, array($this, 'ReplaceConditionXpathToValue'), $v['FROM']);
			}
			
			$xpath2 = $v['XPATH'];

			$generalXpath = $xpath;
			if(strpos($xpath, '@')!==false) $generalXpath = rtrim(substr($xpath, 0, strpos($xpath, '@')), '/');
			if(strpos($xpath2, $generalXpath)===0)
			{
				//$xpath2 = substr($xpath2, strlen($xpath) + 1);
				$xpath2 = substr($xpath2, strlen($generalXpath));
				$xpath2 = ltrim(preg_replace('/^\[\d*\]/', '', $xpath2), '/');
				$simpleXmlObj = $simpleXmlObj2;
			}
			$arPath = explode('/', $xpath2);
			$attr = false;
			if(strpos($arPath[count($arPath)-1], '@')===0)
			{
				$attr = substr(array_pop($arPath), 1);
			}
			if(count($arPath) > 0)
			{
				//$simpleXmlObj3 = $simpleXmlObj->xpath(implode('/', $arPath));
				$simpleXmlObj3 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
				if(count($simpleXmlObj3)==1) $simpleXmlObj3 = current($simpleXmlObj3);
			}
			else $simpleXmlObj3 = $simpleXmlObj;
			
			$condVal = '';
			if(is_array($simpleXmlObj3))
			{					
				$find = false;
				foreach($simpleXmlObj3 as $k2=>$curObj)
				{
					$condVal = (string)($attr!==false ? $curObj->attributes()->{$attr} : $curObj);
					if($this->CheckCondition($condVal, $v))
					{
						$find = true;
						
						$cnt = count($simpleXmlObj3);
						if($cnt > 1)
						{
							$arPath2 = $arPath;
							$lastElem = array_pop($arPath2);
							while(($lastElem = array_pop($arPath2)) && (count($arPath) > 0) 
								//&& (count($simpleXmlObj->xpath(implode('/', $arPath2)))==$cnt)){}
								&& (count($this->Xpath($simpleXmlObj, implode('/', $arPath2)))==$cnt)){}
							$xpathReplace = $this->xpathReplace;
							$this->xpathReplace = array(
								'FROM' => implode('/', $arPath2).'/'.$lastElem,
								'TO' => implode('/', $arPath2).'/'.$lastElem.'['.($k2+1).']'
							);
							foreach($conditions as $k3=>$v3)
							{
								if($k3 <= $k) continue;
								$conditions[$k3]['XPATH'] = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $conditions[$k3]['XPATH']);
								$conditions[$k3]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k3]['FROM']);
							}
							$this->xpathReplace = $xpathReplace;
						}
					}
				}
				if(!$find) return false;
			}
			else
			{
				$condVal = (string)(($attr!==false && is_callable(array($simpleXmlObj3, 'attributes'))) ? $simpleXmlObj3->attributes()->{$attr} : $simpleXmlObj3);
				if(!$this->CheckCondition($condVal, $v)) return false;
			}
			$k++;
		}
		return true;
	}
	
	public function CheckCondition($condVal, $v)
	{
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$condVal = \Bitrix\Main\Text\Encoding::convertEncodingArray($condVal, $this->fileEncoding, $this->siteEncoding);
		}
		if(!(($v['WHEN']=='EQ' && $condVal==$v['FROM'])
			|| ($v['WHEN']=='NEQ' && $condVal!=$v['FROM'])
			|| ($v['WHEN']=='GT' && $condVal > $v['FROM'])
			|| ($v['WHEN']=='LT' && $condVal < $v['FROM'])
			|| ($v['WHEN']=='GEQ' && $condVal >= $v['FROM'])
			|| ($v['WHEN']=='LEQ' && $condVal <= $v['FROM'])
			|| ($v['WHEN']=='CONTAIN' && strpos($condVal, $v['FROM'])!==false)
			|| ($v['WHEN']=='NOT_CONTAIN' && strpos($condVal, $v['FROM'])===false)
			|| ($v['WHEN']=='REGEXP' && preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
			|| ($v['WHEN']=='EMPTY' && strlen($condVal)==0)
			|| ($v['WHEN']=='NOT_EMPTY' && strlen($condVal) > 0)))
		{
			return false;
		}
		return true;
	}
	
	public function SaveSectionRecord($arItem, $parentSectionId=0)
	{
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
	
		$IBLOCK_ID = $this->params['IBLOCK_ID'];
		$SECTION_ID = $this->params['SECTION_ID'];
		$arParams = array();
		$sectionUid = $this->params['SECTION_UID'];
		
		$arFieldsSections = array();
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions) && !in_array($field, array('ISECT_PARENT_TMP_ID', 'ISECT_TMP_ID')))
			{
				$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				if($value===false) continue;
			}
			
			$prefix = ($parentSectionId > 0 ? 'ISUBSECT_' : 'ISECT_');
			if(strpos($field, $prefix)===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$fKey = substr($field, strlen($prefix));
				$arFieldsSections[$fKey] = $value;
				
				if(is_array($adata) && count($adata) > 1)
				{
					$arFieldsSections[$adata[0]] = $adata[1];
				}
				
				if($fKey==$sectionUid)
				{
					$arParams = $this->fparams[$key];
				}
			}
		}
		
		if($parentSectionId > 0)
		{
			$parentId = $parentSectionId;
		}
		else
		{
			$parentId = ($SECTION_ID ? (int)$SECTION_ID : 0);
			if(isset($arFieldsSections['PARENT_TMP_ID']))
			{
				if(isset($this->sectionIds[$arFieldsSections['PARENT_TMP_ID']]))
				{
					$parentId = $this->sectionIds[$arFieldsSections['PARENT_TMP_ID']];
				}
				unset($arFieldsSections['PARENT_TMP_ID']);
			}
		}
		$tmpId = 0;
		if(isset($arFieldsSections['TMP_ID']))
		{
			$tmpId = $arFieldsSections['TMP_ID'];
			unset($arFieldsSections['TMP_ID']);
		}
	
		$sectIds = $this->SaveSection($arFieldsSections, $IBLOCK_ID, $parentId, 0, $arParams);
		if(!empty($sectIds))
		{
			$sectionID = end($sectIds);
			$this->sectionIds[$tmpId] = $sectionID;
			
			$this->SaveSectionRecordAfter($sectionID, $arItem);
		}
		
		$this->SaveStatusImport();
	}
	
	public function SaveSectionRecordAfter($sectionID, $arItem)
	{
		if(!$sectionID) return;
		$currentXpath = $this->currentSectionXpath;
		
		if($this->subSectionInSection)
		{
			$xpath = trim(substr($this->params['GROUPS']['SUBSECTION'], strlen($this->params['GROUPS']['SECTION'])), '/');
			$this->currentSectionXpath = $currentSectionXpath = $this->currentSectionXpath.'/'.$xpath;
			$xpath2 = trim(substr($currentSectionXpath, strlen($this->params['GROUPS']['SECTION'])), '/');
			//if($this->currentXmlObj->xpath($xpath2))
			if($this->Xpath($this->currentXmlObj, $xpath2))
			{
				//$this->xmlSubsections = $xmlSubsections = $this->currentXmlObj->xpath($xpath);
				$this->xmlSubsections = $xmlSubsections = $this->Xpath($this->currentXmlObj, $xpath);
				$xmlSubsectionCurrentRow = 0;
				while($arSubsectionItem = $this->GetNextSubsection($sectionID, $arItem, $xmlSubsectionCurrentRow))
				{
					$this->SaveSectionRecord($arSubsectionItem, $sectionID);
					$this->currentSectionXpath = $currentSectionXpath;
					$this->xmlSubsections = $xmlSubsections;
					$xmlSubsectionCurrentRow++;
				}
				$this->currentSectionXpath = $currentSectionXpath;
				$this->xmlSubsections = $xmlSubsections;
			}
		}
		
		if($this->elementInSection)
		{
			$parentXpath = $this->xpath;
			$this->xpath = '/'.trim(preg_replace('/\[\d+\]/', '', $currentXpath), '/').'/'.$this->xpathElementInSection;
			
			$xpath = trim(substr($currentXpath, strlen($this->params['GROUPS']['SECTION'])), '/');
			if(strlen($xpath) > 0) $xpath .= '/';
			$xpath .= $this->xpathElementInSection;
			//$this->xmlElements = $this->currentXmlObj->xpath($xpath);
			$this->xmlElements = $this->Xpath($this->currentXmlObj, $xpath);
			$count = count($this->xmlElements);
			if($count > 0)
			{
				$this->xmlElementsCount += $count;
				$this->stepparams['total_file_line'] = $this->xmlElementsCount;
				$this->currentParentSectionXmlObj = $this->currentXmlObj;
				$this->xmlCurrentRow = 0;
				while($arItem = $this->GetNextRecord($time))
				{
					if(is_array($arItem)) $this->SaveRecord($arItem, $sectionID);
					/*if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}*/
				}
				//if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
				$this->currentXmlObj = $this->currentParentSectionXmlObj;
			}
			$this->xpath = $parentXpath;
		}
	}
	
	public function SaveRecord($arItem, $sectionID=0)
	{
		$this->stepparams['total_read_line']++;
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
		$this->stepparams['total_line']++;
		
		$IBLOCK_ID = $this->params['IBLOCK_ID'];
		$SECTION_ID = $this->params['SECTION_ID'];
		if($sectionID > 0) $SECTION_ID = $sectionID;
		
		$arFieldsDef = $this->fl->GetFields($IBLOCK_ID);
		$propsDef = $this->GetIblockProperties($IBLOCK_ID);

		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		$fieldList = preg_grep('/^[^~]/', array_keys($arItem));
		
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field!='VARIABLE') continue;

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				}
			}
			$arItem[$key] = $value;
			$arItem['~'.$key] = $origValue;
		}

		$arFieldsElement = array();
		$arFieldsElementOrig = array();
		$arFieldsPrices = array();
		$arFieldsProduct = array();
		$arFieldsProductStores = array();
		$arFieldsProductDiscount = array();
		$arFieldsProps = array();
		$arFieldsPropsOrig = array();
		$arFieldsSections = array();
		$arFieldsIpropTemp = array();
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field=='VARIABLE') continue;

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				}
				if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
			}
			
			if(strpos($field, 'IE_')===0)
			{
				$fieldKey = substr($field, 3);
				if($fieldKey=='IBLOCK_SECTION_TMP_ID')
				{
					$arSectionIds = array();
					if(!empty($value))
					{
						if(is_array($value))
						{
							foreach($value as $value2)
							{
								if(isset($this->sectionIds[$value2])) $arSectionIds[] = $this->sectionIds[$value2];
							}
						}
						elseif(isset($this->sectionIds[$value])) $arSectionIds[] = $this->sectionIds[$value];
					}
					if(!empty($arSectionIds))
					{
						$arFieldsElement['IBLOCK_SECTION'] = $arSectionIds;
					}
				}
				elseif($fieldKey=='SECTION_PATH')
				{
					$tmpSep = ($this->fparams[$key]['SECTION_PATH_SEPARATOR'] ? $this->fparams[$key]['SECTION_PATH_SEPARATOR'] : '/');
					if($this->fparams[$key]['SECTION_PATH_SEPARATED']=='Y')
						$arVals = explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $value);
					elseif(is_array($value)) $arVals = $value;
					else $arVals = array($value);
					foreach($arVals as $subvalue)
					{
						$tmpVal = array_map('trim', explode($tmpSep, $subvalue));
						$arFieldsElement[$fieldKey][] = $tmpVal;
						$arFieldsElementOrig[$fieldKey][] = $tmpVal;
					}
				}
				else
				{
					if(strpos($fieldKey, '|')!==false)
					{
						list($fieldKey, $adata) = explode('|', $fieldKey);
						$adata = explode('=', $adata);
						if(count($adata) > 1)
						{
							$arFieldsElement[$adata[0]] = $adata[1];
						}
					}
					if(isset($arFieldsElement[$fieldKey]) && in_array($field, $this->params['ELEMENT_UID']))
					{
						if(!is_array($arFieldsElement[$fieldKey]))
						{
							$arFieldsElement[$fieldKey] = array($arFieldsElement[$fieldKey]);
							$arFieldsElementOrig[$fieldKey] = array($arFieldsElementOrig[$fieldKey]);
						}
						$arFieldsElement[$fieldKey][] = $value;
						$arFieldsElementOrig[$fieldKey][] = $origValue;
					}
					else
					{
						$arFieldsElement[$fieldKey] = $value;
						$arFieldsElementOrig[$fieldKey] = $origValue;
					}
				}
			}
			elseif(strpos($field, 'ISECT')===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$arSect = explode('_', substr($field, 5), 2);
				$arFieldsSections[$arSect[0]][$arSect[1]] = $value;
				
				if(is_array($adata) && count($adata) > 1)
				{
					$arFieldsSections[$arSect[0]][$adata[0]] = $adata[1];
				}
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$val = $value;
				if(substr($field, -6)=='_PRICE')
				{
					if(!in_array($val, array('', '-')))
					{
						$val = $this->GetFloatVal($val);
					}
				}
				elseif(substr($field, -6)=='_EXTRA')
				{
					$val = $this->GetFloatVal($val);
				}
				
				$arPrice = explode('_', substr($field, 10), 2);
				$pkey = $arPrice[1];
				if($pkey=='PRICE' && $this->fparams[$key]['PRICE_USE_EXT']=='Y')
				{
					$pkey = $pkey.'|QUANTITY_FROM='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_FROM']).'|QUANTITY_TO='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_TO']);
				}
				$arFieldsPrices[$arPrice[0]][$pkey] = $val;
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				$arFieldsProductStores[$arStore[0]][$arStore[1]] = $value;
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsProductDiscount[$adata[0]] = $adata[1];
					}
				}
				$arFieldsProductDiscount[substr($field, 14)] = $value;
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				$val = $value;
				if($field=='ICAT_PURCHASING_PRICE')
				{
					if($val=='') continue;
					$val = $this->GetFloatVal($val);
				}
				elseif($field=='ICAT_MEASURE')
				{
					$val = $this->GetMeasureByStr($val);
				}
				$arFieldsProduct[substr($field, 5)] = $val;
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldName = substr($field, 7);
				if(substr($fieldName, -12)=='_DESCRIPTION') $currentPropDef = $propsDef[substr($fieldName, 0, -12)];
				else $currentPropDef = $propsDef[$fieldName];
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $currentPropDef, $fieldName, $value, $origValue, $this->params['ELEMENT_UID']);
			}
			elseif(strpos($field, 'IP_LIST_PROPS')===0)
			{
				$this->GetPropList($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $IBLOCK_ID, $value);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				$fieldName = substr($field, 11);
				$arFieldsIpropTemp[$fieldName] = $value;
			}
		}
		
		if($this->params['NOT_LOAD_ELEMENTS_WO_SECTION']=='Y' 
			&& (!isset($arFieldsElement['IBLOCK_SECTION']) || empty($arFieldsElement['IBLOCK_SECTION']))
			&& (!isset($arFieldsElement['SECTION_PATH']) || empty($arFieldsElement['SECTION_PATH']))
			) return false;
		
		$this->AddGroupsProperties($arFieldsProps, $arFieldsPropsOrig, $IBLOCK_ID);
		
		if($sectionID > 0 && !isset($arFieldsElement['IBLOCK_SECTION']))
		{
			$arFieldsElement['IBLOCK_SECTION'] = array($sectionID);
		}

		$arUid = array();
		if(!is_array($this->params['ELEMENT_UID'])) $this->params['ELEMENT_UID'] = array($this->params['ELEMENT_UID']);
		foreach($this->params['ELEMENT_UID'] as $tuid)
		{
			$uid = $valUid = $valUid2 = $nameUid = '';
			$canSubstring = true;
			if(strpos($tuid, 'IE_')===0)
			{
				$nameUid = $arFieldsDef['element']['items'][$tuid];
				$uid = substr($tuid, 3);
				if(strpos($uid, '|')!==false) $uid = current(explode('|', $uid));
				$valUid = $arFieldsElementOrig[$uid];
				$valUid2 = $arFieldsElement[$uid];
				
				if($uid == 'ACTIVE_FROM' || $uid == 'ACTIVE_TO')
				{
					$uid = 'DATE_'.$uid;
					$valUid = $this->GetDateVal($valUid);
					$valUid2 = $this->GetDateVal($valUid2);
				}
			}
			elseif(strpos($tuid, 'IP_PROP')===0)
			{
				$nameUid = $arFieldsDef['prop']['items'][$tuid];
				$uid = substr($tuid, 7);
				$valUid = $arFieldsPropsOrig[$uid];
				$valUid2 = $arFieldsProps[$uid];
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='N' && !is_numeric($valUid))
				{
					$valUid = $valUid2 = '';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
						$valUid2 = $this->GetHighloadBlockValue($propsDef[$uid], $valUid2);
						$canSubstring = false;
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, $this->fieldSettings[$tuid]);
						$valUid2 = $this->GetIblockElementValue($propsDef[$uid], $valUid2, $this->fieldSettings[$tuid]);
						$canSubstring = false;
					}
					$uid = 'PROPERTY_'.$uid;
				}
			}
			if($uid)
			{
				$arUid[] = array(
					'uid' => $uid,
					'nameUid' => $nameUid,
					'valUid' => $valUid,
					'valUid2' => $valUid2,
					'substring' => ($this->fieldSettings[$tuid]['UID_SEARCH_SUBSTRING']=='Y' && $canSubstring)
				);
			}
		}
		
		$emptyFields = array();
		foreach($arUid as $k=>$v)
		{
			if((is_array($v['valUid']) && count(array_diff($v['valUid'], array('')))==0)
				|| (!is_array($v['valUid']) && strlen(trim($v['valUid']))==0)) $emptyFields[] = $v['nameUid'];
		}
		
		if(!empty($emptyFields) || empty($arUid))
		{
			$bEmptyElemFields = (bool)(count(array_diff($arFieldsElement, array('')))==0 && count(array_diff($arFieldsProps, array('')))==0);
			$res = false;
			
			//$res = (bool)($res && $bEmptyElemFields);
			$res = (bool)($res);
			
			if(!$res)
			{
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_FIELD"), implode(', ', $emptyFields), $this->worksheetCurrentRow).(strlen($arFieldsElement['NAME']) > 0 ? ' ('.$arFieldsElement['NAME'].')' : '');
				$this->stepparams['error_line']++;
			}
			else
			{
				$this->stepparams['correct_line']++;
			}
			return false;
		}
		
		if($arFieldsElement['ACTIVE_FROM']) $arFieldsElement['ACTIVE_FROM'] = $this->GetDateVal($arFieldsElement['ACTIVE_FROM']);
		if($arFieldsElement['ACTIVE_TO']) $arFieldsElement['ACTIVE_TO'] = $this->GetDateVal($arFieldsElement['ACTIVE_TO']);
		
		$arTexts = array('PREVIEW_TEXT', 'DETAIL_TEXT');
		foreach($arTexts as $keyText)
		{
			if($arFieldsElement[$keyText])
			{
				if($this->fieldSettings['IE_'.$keyText]['LOAD_BY_EXTLINK']=='Y')
				{
					$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>5));
					$res = $client->get($arFieldsElement[$keyText]);
					$arFieldsElement[$keyText] = $res;
				}
				else
				{
					$textFile = $_SERVER["DOCUMENT_ROOT"].$arFieldsElement[$keyText];
					if(file_exists($textFile) && is_file($textFile) && is_readable($textFile))
					{
						$arFieldsElement[$keyText] = file_get_contents($textFile);
					}
				}
			}
		}
		
		if(isset($arFieldsElement['ACTIVE']))
		{
			$arFieldsElement['ACTIVE'] = $this->GetBoolValue($arFieldsElement['ACTIVE']);
		}
		elseif($this->params['ELEMENT_LOADING_ACTIVATE']=='Y')
		{
			$arFieldsElement['ACTIVE'] = 'Y';
		}

		if(($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' && isset($arFieldsProduct['QUANTITY']) && floatval($arFieldsProduct['QUANTITY'])==0)
			|| ($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y' && $this->IsEmptyPrice($arFieldsPrices)))
		{
			$arFieldsElement['ACTIVE'] = 'N';
		}
		
		$arKeys = array_merge(array('ID', 'NAME', 'IBLOCK_SECTION_ID'), array_keys($arFieldsElement));
		
		$arFilter = array('IBLOCK_ID'=>$IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N');
		foreach($arUid as $v)
		{
			if(!$v['substring'])
			{
				if(is_array($v['valUid'])) $arSubfilter = array_map('trim', $v['valUid']);
				else 
				{
					$arSubfilter = array(trim($v['valUid']));
					if(trim($v['valUid']) != $v['valUid2'])
					{
						$arSubfilter[] = trim($v['valUid2']);
						if(strlen($v['valUid2']) != strlen(trim($v['valUid2'])))
						{
							$arSubfilter[] = $v['valUid2'];
						}
					}
					if(strlen($v['valUid']) != strlen(trim($v['valUid'])))
					{
						$arSubfilter[] = $v['valUid'];
					}
				}
				
				if(count($arSubfilter) == 1)
				{
					$arSubfilter = $arSubfilter[0];
				}
				$arFilter['='.$v['uid']] = $arSubfilter;
			}
			else
			{
				$arFilter['%'.$v['uid']] = trim($v['valUid']);
			}
		}
		
		if(!empty($arFieldsIpropTemp))
		{
			$arFieldsElement['IPROPERTY_TEMPLATES'] = $arFieldsIpropTemp;
		}
		
		$elemName = '';
		$dbRes = \CIblockElement::GetList(array(), $arFilter, false, false, $arKeys);
		while($arElement = $dbRes->Fetch())
		{
			if($this->params['ONLY_DELETE_MODE']=='Y')
			{
				$ID = $arElement['ID'];
				$this->BeforeElementDelete($ID, $IBLOCK_ID);
				\CIblockElement::Delete($ID);
				$this->AfterElementDelete($ID, $IBLOCK_ID);
				unset($ID);
				continue;
			}
			
			$ID = $arElement['ID'];
			$arFieldsProps2 = $arFieldsProps;
			$arFieldsElement2 = $arFieldsElement;
			$arFieldsProduct2 = $arFieldsProduct;
			$arFieldsPrices2 = $arFieldsPrices;
			$arFieldsProductStores2 = $arFieldsProductStores;
			if($this->conv->UpdateProperties($arFieldsProps2, $ID)!==false
				&& $this->conv->UpdateElementFields($arFieldsElement2, $ID)!==false
				&& $this->conv->UpdateProduct($arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $ID)!==false)
			{
				$this->BeforeElementSave($ID, 'update');
				if($this->params['ONLY_CREATE_MODE']!='Y')
				{
					$this->UnsetUidFields($arFieldsElement2, $arFieldsProps2, $this->params['ELEMENT_UID']);
					if(!empty($this->fieldOnlyNew))
					{
						$this->UnsetExcessSectionFields($this->fieldOnlyNew, $arFieldsSections, $arFieldsElement2);
					}
					
					$arElementSections = false;
					if($this->params['ELEMENT_ADD_NEW_SECTIONS']=='Y')
					{
						$arElementSections = $this->GetElementSections($ID);
						if(!is_array($arElementSections)) $arElementSections = array();
						if(!is_array($arFieldsElement2['IBLOCK_SECTION'])) $arFieldsElement2['IBLOCK_SECTION'] = array();
						$arFieldsElement2['IBLOCK_SECTION'] = array_merge($arFieldsElement2['IBLOCK_SECTION'], $arElementSections);
					}
					$this->GetSections($arFieldsElement2, $IBLOCK_ID, $SECTION_ID, $arFieldsSections);
					
					foreach($arElement as $k=>$v)
					{
						$action = $this->fieldSettings['IE_'.$k]['LOADING_MODE'];
						if($action)
						{
							if($action=='ADD_BEFORE') $arFieldsElement2[$k] = $arFieldsElement2[$k].$v;
							elseif($action=='ADD_AFTER') $arFieldsElement2[$k] = $v.$arFieldsElement2[$k];
						}
					}
					
					if(!empty($this->fieldOnlyNew))
					{
						$this->UnsetExcessFields($this->fieldOnlyNew, $arFieldsElement2, $arFieldsProps2, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $arFieldsProductDiscount);
					}
					
					$this->RemoveProperties($ID, $IBLOCK_ID);
					$this->SaveProperties($ID, $IBLOCK_ID, $arFieldsProps2);
					$this->SaveProduct($ID, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2);
					
					$el = new \CIblockElement();
					if($this->UpdateElement($el, $ID, $IBLOCK_ID, $arFieldsElement2, $arElement, $arElementSections))
					{
						//$this->SetTimeBegin($ID);
					}
					else
					{
						$this->stepparams['error_line']++;
						$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_UPDATE_ELEMENT_ERROR"), $el->LAST_ERROR, 'ID = '.$ID);
					}
					
					$elemName = $arElement['NAME'];
					$this->SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $elemName);
					$this->stepparams['element_updated_line']++;
				}
			}
			
			$this->SaveElementId($ID);
			if($elemName && !$arFieldsElement2['NAME']) $arFieldsElement2['NAME'] = $elemName;
			$this->SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement2);
		}
		
		if($dbRes->SelectedRowsCount()==0 && $this->params['ONLY_DELETE_MODE']!='Y')
		{
			if($this->params['ONLY_UPDATE_MODE']!='Y')
			{
				$this->UnsetUidFields($arFieldsElement, $arFieldsProps, $this->params['ELEMENT_UID'], true);
				if(isset($arFieldsElement['ID']))
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NEW_ELEMENT_WITH_ID"), $arFieldsElement['ID'], $this->worksheetCurrentRow);
					return false;
				}
				if(strlen($arFieldsElement['NAME'])==0)
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_FIELD"), $arFieldsDef['element']['items']['IE_NAME']).($arFieldsElement['XML_ID'] ? ' ('.$arFieldsElement['XML_ID'].')' : '');
					return false;
				}
				if($this->params['ELEMENT_NEW_DEACTIVATE']=='Y')
				{
					$arFieldsElement['ACTIVE'] = 'N';
				}
				elseif(!$arFieldsElement['ACTIVE'])
				{
					$arFieldsElement['ACTIVE'] = 'Y';
				}
				$arFieldsElement['IBLOCK_ID'] = $IBLOCK_ID;
				$this->GenerateElementCode($arFieldsElement, $iblockFields);
				$this->PrepareElementPictures($arFieldsElement);
				$this->GetSections($arFieldsElement, $IBLOCK_ID, $SECTION_ID, $arFieldsSections);
				$el = new \CIblockElement();
				$ID = $el->Add($arFieldsElement, false, true, true);
				
				if($ID)
				{
					$this->BeforeElementSave($ID, 'add');
					$this->logger->AddElementChanges('IE_', $arFieldsElement);
					//$this->SetTimeBegin($ID);
					$this->SaveProperties($ID, $IBLOCK_ID, $arFieldsProps, true);
					$this->SaveProduct($ID, $arFieldsProduct, $arFieldsPrices, $arFieldsProductStores);
					$this->SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $arFieldsElement['NAME']);
					if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']) || $arFieldsElement['NAME'])
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $ID);
						$ipropValues->clearValues();
					}
					$this->stepparams['element_added_line']++;
					$this->SaveElementId($ID);
					$this->SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement);
				}
				else
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_ELEMENT_ERROR"), $el->LAST_ERROR, $arFieldsElement['NAME']);
					return false;
				}
			}
			else
			{
				$this->logger->SaveElementNotFound($arFilter);
			}
		}
		
		$this->stepparams['correct_line']++;
		$this->SaveStatusImport();
	}
	
	public function SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement)
	{
		if(!$ID) return;
		
		$arFieldsElement['ID'] = $ID;
		$this->stepparams['currentelement'] = $arFieldsElement;
		$this->stepparams['currentelementitem'] = $arItem;
		if($this->params['ELEMENT_UID_SKU']) 
		{
			if($this->skuInElement)
			{
				$this->currentParentXmlObj = $this->currentXmlObj;
				$xpath = trim(substr($this->params['GROUPS']['OFFER'], strlen($this->params['GROUPS']['ELEMENT'])), '/');
				//$this->xmlOffers = $this->currentParentXmlObj->xpath($xpath);
				$this->xmlOffers = $this->Xpath($this->currentParentXmlObj, $xpath);
				$this->xmlOfferCurrentRow = 0;
				while($arOfferItem = $this->GetNextOffer($ID, $arItem))
				{
					$this->SaveSKUWithGenerate($ID, $arFieldsElement['NAME'], $IBLOCK_ID, $arOfferItem);
				}
				$this->currentXmlObj = $this->currentParentXmlObj;
			}
			else
			{
				$this->SaveSKUWithGenerate($ID, $arFieldsElement['NAME'], $IBLOCK_ID, $arItem);
			}
		}
		
		if($this->params['ONAFTERSAVE_HANDLER'])
		{
			$this->ExecuteOnAfterSaveHandler($this->params['ONAFTERSAVE_HANDLER'], $ID);
		}
	}
	
	public function AddGroupsProperties(&$arFieldsProps, &$arFieldsPropsOrig, $IBLOCK_ID)
	{
		if($this->propertyInElement)
		{
			$this->currentParentXmlObj = $this->currentXmlObj;
			$xpath = trim(substr($this->params['GROUPS']['PROPERTY'], strlen($this->params['GROUPS']['ELEMENT'])), '/');
			$this->parentXpath = $this->xpath;
			$this->xpath = '/'.$this->params['GROUPS']['PROPERTY'];
			//$this->xmlProperties = $this->currentParentXmlObj->xpath($xpath);
			$this->xmlProperties = $this->Xpath($this->currentParentXmlObj, $xpath);
			$this->xmlPropertiesCurrentRow = 0;
			while($arProperty = $this->GetNextProperty())
			{
				$arPropertyFields = array();
				foreach($this->params['FIELDS'] as $key=>$fieldFull)
				{
					list($xpath, $field) = explode(';', $fieldFull, 2);
					if(strpos($field, 'PROPERTY_')!==0) continue;
					
					$value = $arProperty[$key];
					if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arProperty['~'.$key];
					$origValue = $arProperty['~'.$key];
					
					$conversions = $this->fparams[$key]['CONVERSION'];
					if(!empty($conversions))
					{
						if(is_array($value))
						{
							foreach($value as $k2=>$v2)
							{
								$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arProperty);
								$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arProperty);
							}
						}
						else
						{
							$value = $this->ApplyConversions($value, $conversions, $arProperty);
							$origValue = $this->ApplyConversions($origValue, $conversions, $arProperty);
						}
						if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
					}
					
					$arPropertyFields[substr($field, 9)] = $value;
				}

				if($arPropertyFields['NAME'])
				{
					$arProp = $this->GetIblockPropertyByName($arPropertyFields['NAME'], $IBLOCK_ID, true);
					if($arProp['PROPERTY_TYPE']=='E' && !isset($this->fieldSettings['IP_PROP'.$arProp['ID']]['REL_ELEMENT_FIELD'])) $this->fieldSettings['IP_PROP'.$arProp['ID']]['REL_ELEMENT_FIELD'] = 'IE_NAME';
					if(isset($arFieldsProps[$arProp['ID']]))
					{
						if(!is_array($arFieldsProps[$arProp['ID']])) $arFieldsProps[$arProp['ID']] = array($arFieldsProps[$arProp['ID']]);
						if(!is_array($arFieldsPropsOrig[$arProp['ID']])) $arFieldsPropsOrig[$arProp['ID']] = array($arFieldsPropsOrig[$arProp['ID']]);
						$arFieldsProps[$arProp['ID']][] = $arPropertyFields['VALUE'];
						$arFieldsPropsOrig[$arProp['ID']][] = $arPropertyFields['VALUE'];
					}
					else
					{
						$arFieldsProps[$arProp['ID']] = $arPropertyFields['VALUE'];
						$arFieldsPropsOrig[$arProp['ID']] = $arPropertyFields['VALUE'];
					}
				}
			}
			$this->xpath = $this->parentXpath;
			$this->parentXpath = '';
			$this->currentXmlObj = $this->currentParentXmlObj;
		}
	}
	
	public function UpdateElement(&$el, $ID, $IBLOCK_ID, $arFieldsElement, $arElement=array(), $arElementSections=array())
	{
		if(!empty($arFieldsElement))
		{
			$this->PrepareElementPictures($arFieldsElement);

			if($this->params['ELEMENT_NOT_CHANGE_SECTIONS']=='Y')
			{
				unset($arFieldsElement['IBLOCK_SECTION'], $arFieldsElement['IBLOCK_SECTION_ID']);
			}
			foreach($arFieldsElement as $k=>$v)
			{
				if($k=='IBLOCK_SECTION' && is_array($v))
				{
					if(!is_array($arElementSections)) $arElementSections = $this->GetElementSections($ID);
					if(count($v)==count($arElementSections) && count(array_diff($v, $arElementSections))==0)
					{
						unset($arFieldsElement[$k]);
					}
				}
				elseif($k=='PREVIEW_PICTURE' || $k=='DETAIL_PICTURE')
				{
					if(!$this->IsChangedImage($arElement[$k], $arFieldsElement[$k]))
					{
						unset($arFieldsElement[$k]);
					}
				}
				elseif($v==$arElement[$k])
				{
					unset($arFieldsElement[$k]);
				}
			}
			
			if((isset($arFieldsElement['DETAIL_PICTURE']) && is_array($arFieldsElement['DETAIL_PICTURE'])) && (!isset($arFieldsElement['PREVIEW_PICTURE']) || !is_array($arFieldsElement['PREVIEW_PICTURE'])))
			{
				$arFieldsElement['PREVIEW_PICTURE'] = array();
			}
		}
		
		if(empty($arFieldsElement) && $this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']=='Y') return true;
		if($el->Update($ID, $arFieldsElement, false, true, true))
		{
			$this->logger->AddElementChanges('IE_', $arFieldsElement, $arElement);
			if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']) || $arFieldsElement['NAME'])
			{
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $ID);
				$ipropValues->clearValues();
			}
			return true;
		}
		return false;
	}
	
	public function PrepareElementPictures(&$arFieldsElement)
	{
		$arPictures = array('PREVIEW_PICTURE', 'DETAIL_PICTURE');
		foreach($arPictures as $picName)
		{
			if($arFieldsElement[$picName])
			{
				if(is_array($arFieldsElement[$picName])) $arFieldsElement[$picName] = current($arFieldsElement[$picName]);
				$arFieldsElement[$picName] = $this->GetFileArray($arFieldsElement[$picName], array(), array('FILETYPE'=>'IMAGE'));
			}
			if(isset($arFieldsElement[$picName.'_DESCRIPTION']))
			{
				$arFieldsElement[$picName]['description'] = $arFieldsElement[$picName.'_DESCRIPTION'];
				unset($arFieldsElement[$picName.'_DESCRIPTION']);
			}
		}
		if((isset($arFieldsElement['DETAIL_PICTURE']) && is_array($arFieldsElement['DETAIL_PICTURE'])) && (!isset($arFieldsElement['PREVIEW_PICTURE']) || !is_array($arFieldsElement['PREVIEW_PICTURE'])))
		{
			$arFieldsElement['PREVIEW_PICTURE'] = array();
		}
	}
	
	public function SaveStatusImport($end = false)
	{
		if($this->procfile)
		{
			$writeParams = array_merge($this->stepparams, array(
				'worksheetNum' => intval($this->worksheetNum),
				'worksheetCurrentRow' => $this->worksheetCurrentRow
			));
			$writeParams['action'] = ($end ? 'finish' : 'continue');
			file_put_contents($this->procfile, \CUtil::PhpToJSObject($writeParams));
		}
	}
	
	public function SaveSKUWithGenerate($ID, $NAME, $IBLOCK_ID, $arItem)
	{
		$ret = false;
		$this->conv->SetSkuMode(true, $this->GetCachedOfferIblock($IBLOCK_ID));
		if(!empty($this->fieldsForSkuGen))
		{
			$convertedFields = array();
			$filedList = $this->params['FIELDS'];
			$arItemParams = array();
			foreach($this->fieldsForSkuGen as $key)
			{
				$conversions = $this->fparams[$key]['CONVERSION'];
				$arItem['~~'.$key] = $arItem[$key];
				if(is_array($arItem[$key]))
				{
					foreach($arItem[$key] as $k=>$v)
					{
						$arItem[$key][$k] = $this->ApplyConversions($v, $conversions, $arItem);	
					}
					$arItemParams[$key] = $arItem[$key];
				}
				else
				{
					$arItem[$key] = $this->ApplyConversions($arItem[$key], $conversions, $arItem);				
					$arItemParams[$key] = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arItem[$key]));
				}
				$convertedFields[] = $key;
			}
			$arItemSKUParams = array();
			$this->GenerateSKUParamsRecursion($arItemSKUParams, $arItemParams);
			
			$extraFields = array();
			foreach($filedList as $key=>$fieldFull)
			{
				if(in_array($key, $this->fieldsForSkuGen)) continue;
				list($xpath, $field) = explode(';', $fieldFull, 2);
				if(preg_match('/^OFFER_(ICAT_QUANTITY|ICAT_PURCHASING_PRICE|ICAT_PRICE\d+_PRICE|ICAT_STORE\d+_AMOUNT)$/', $field))
				{
					$val = $arItem[$key];
					$conversions = $this->fparams[$key]['CONVERSION'];
					if(!is_array($val)) $val = $this->ApplyConversions($val, $conversions, $arItem);
					if(is_array($val) || strpos($val, $this->params['ELEMENT_MULTIPLE_SEPARATOR'])!==false)
					{
						$arItem['~~'.$key] = $arItem[$key];
						if(is_array($val))
						{
							foreach($val as $k=>$v)
							{
								$arItem[$key][$k] = $this->ApplyConversions($v, $conversions, $arItem);	
							}
							$extraFields[$key] = $arItem[$key];
						}
						else
						{
							$arItem[$key] = $val;	
							$extraFields[$key] = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arItem[$key]));
						}
						$convertedFields[] = $key;
					}
				}
			}
			
			foreach($arItemSKUParams as $k=>$v)
			{
				$arSubItem = $arItem;
				foreach($v as $k2=>$v2) $arSubItem[$k2] = $v2;
				foreach($extraFields as $k2=>$v2)
				{
					if(isset($extraFields[$k2][$k])) $arSubItem[$k2] = $extraFields[$k2][$k];
					else $arSubItem[$k2] = current($extraFields[$k2]);
				}
				$ret = (bool)($this->SaveSKU($ID, $NAME, $IBLOCK_ID, $arSubItem, $convertedFields) || $ret);
			}
		}
		else
		{
			$ret = $this->SaveSKU($ID, $NAME, $IBLOCK_ID, $arItem);
		}
		if($ret)
		{
			\CIBlockElement::UpdateSearch($ID, true);
			if(class_exists('\Bitrix\Iblock\PropertyIndex\Manager'))
			{
				\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ID);
			}
		}
		$this->conv->SetSkuMode(false);
		return $ret;
	}
	
	public function GenerateSKUParamsRecursion(&$arItemSKUParams, $arItemParams, $arSubItem = array())
	{
		if(!empty($arItemParams))
		{
			$arKey = array_keys($arItemParams);
			$key = $arKey[0];
			$arCurParams = $arItemParams[$key];
			unset($arItemParams[$key]);
			foreach($arCurParams as $k=>$v)
			{
				$arSubItem[$key] = $v;
				$arSubItem['~'.$key] = $v;
				$this->GenerateSKUParamsRecursion($arItemSKUParams, $arItemParams, $arSubItem);
			}
		}
		else
		{
			$arItemSKUParams[] = $arSubItem;
		}
	}
	
	public function SaveSKU($ID, $NAME, $IBLOCK_ID, $arItem, $convertedFields=array())
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		$propsDef = $this->GetIblockProperties($OFFERS_IBLOCK_ID);

		$iblockFields = $this->GetIblockFields($OFFERS_IBLOCK_ID);
		
		$arFieldsElement = array();
		$arFieldsElementOrig = array();
		$arFieldsPrices = array();
		$arFieldsProduct = array();
		$arFieldsProductStores = array();
		$arFieldsProductDiscount = array();
		$arFieldsProps = array($OFFERS_PROPERTY_ID => $ID);
		$arFieldsPropsOrig = array($OFFERS_PROPERTY_ID => $ID);
		$arFieldsIpropTemp = array();
		$arFieldsForSkuGen = array_map('strval', $this->fieldsForSkuGen);
		//foreach($filedList as $key=>$field)
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);

			if(strpos($field, 'OFFER_')!==0) continue;
			$conversions = $this->fparams[$key]['CONVERSION'];
			$field = substr($field, 6);
			
			$k = $key;
			if(strpos($k, '_')!==false) $k = substr($k, 0, strpos($k, '_'));
			$value = $arItem[$k];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$k];
			$origValue = $arItem['~'.$k];

			if(!empty($conversions) && !in_array($key, $convertedFields))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				}
				if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
			}
			
			if(strpos($field, 'IE_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsElement[$adata[0]] = $adata[1];
					}
				}
				$arFieldsElement[substr($field, 3)] = $value;
				$arFieldsElementOrig[substr($field, 3)] = $origValue;
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$val = $value;
				if(substr($field, -6)=='_PRICE')
				{
					if(!in_array($val, array('', '-')))
					{
						$val = $this->GetFloatVal($val);
					}
				}
				elseif(substr($field, -6)=='_EXTRA')
				{
					$val = $this->GetFloatVal($val);
				}
				
				$arPrice = explode('_', substr($field, 10), 2);
				$pkey = $arPrice[1];
				if($pkey=='PRICE' && $this->fparams[$key]['PRICE_USE_EXT']=='Y')
				{
					$pkey = $pkey.'|QUANTITY_FROM='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_FROM']).'|QUANTITY_TO='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_TO']);
				}
				$arFieldsPrices[$arPrice[0]][$pkey] = $val;
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				$arFieldsProductStores[$arStore[0]][$arStore[1]] = $value;
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsProductDiscount[$adata[0]] = $adata[1];
					}
				}
				$arFieldsProductDiscount[substr($field, 14)] = $value;
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				$val = $value;
				if($field=='ICAT_PURCHASING_PRICE')
				{
					if($val=='') continue;
					$val = $this->GetFloatVal($val);
				}
				elseif($field=='ICAT_MEASURE')
				{
					$val = $this->GetMeasureByStr($val);
				}
				$arFieldsProduct[substr($field, 5)] = $val;
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldName = substr($field, 7);
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $propsDef[$fieldName], $fieldName, $value, $origValue);
			}
			elseif(strpos($field, 'IP_LIST_PROPS')===0)
			{
				$this->GetPropList($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $OFFERS_IBLOCK_ID, $value);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				$fieldName = substr($field, 11);
				$arFieldsIpropTemp[$fieldName] = $value;
			}
		}

		$arUid = array();
		if(!is_array($this->params['ELEMENT_UID_SKU'])) $this->params['ELEMENT_UID_SKU'] = array($this->params['ELEMENT_UID_SKU']);
		if(!in_array('OFFER_IP_PROP'.$OFFERS_PROPERTY_ID, $this->params['ELEMENT_UID_SKU'])) $this->params['ELEMENT_UID_SKU'][] = 'OFFER_IP_PROP'.$OFFERS_PROPERTY_ID;
		foreach($this->params['ELEMENT_UID_SKU'] as $tuid)
		{
			$tuid = substr($tuid, 6);
			$uid = $valUid = '';
			if(strpos($tuid, 'IE_')===0)
			{
				$uid = substr($tuid, 3);
				if(strpos($uid, '|')!==false) $uid = current(explode('|', $uid));
				$valUid = $arFieldsElementOrig[$uid];
			}
			elseif(strpos($tuid, 'IP_PROP')===0)
			{
				$uid = substr($tuid, 7);
				$valUid = $arFieldsPropsOrig[$uid];
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='N' && !is_numeric($valUid))
				{
					$valUid = '';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, $this->fieldSettings['OFFER_'.$tuid]);
					}
					$uid = 'PROPERTY_'.$uid;
				}
			}
			if($uid)
			{
				$arUid[] = array(
					'uid' => $uid,
					'valUid' => $valUid
				);
			}
		}

		$notEmptyFields = array();
		foreach($arUid as $k=>$v)
		{
			if(trim($v['valUid'])) $notEmptyFields[] = $v['uid'];
		}
		
		if(count($notEmptyFields) < 2)
		{
			return false;
		}
		
		if($arFieldsElement['ACTIVE_FROM']) $arFieldsElement['ACTIVE_FROM'] = $this->GetDateVal($arFieldsElement['ACTIVE_FROM']);
		if($arFieldsElement['ACTIVE_TO']) $arFieldsElement['ACTIVE_TO'] = $this->GetDateVal($arFieldsElement['ACTIVE_TO']);
		
		$arTexts = array('PREVIEW_TEXT', 'DETAIL_TEXT');
		foreach($arTexts as $keyText)
		{
			if($arFieldsElement[$keyText])
			{
				if($this->fieldSettings['OFFER_IE_'.$keyText]['LOAD_BY_EXTLINK']=='Y')
				{
					$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>5));
					$res = $client->get($arFieldsElement[$keyText]);
					$arFieldsElement[$keyText] = $res;
				}
				else
				{
					$textFile = $_SERVER["DOCUMENT_ROOT"].$arFieldsElement[$keyText];
					if(file_exists($textFile) && is_file($textFile) && is_readable($textFile))
					{
						$arFieldsElement[$keyText] = file_get_contents($textFile);
					}
				}
			}
		}
		
		if(isset($arFieldsElement['ACTIVE']))
		{
			$arFieldsElement['ACTIVE'] = $this->GetBoolValue($arFieldsElement['ACTIVE']);
		}
		elseif($this->params['ELEMENT_LOADING_ACTIVATE']=='Y')
		{
			$arFieldsElement['ACTIVE'] = 'Y';
		}

		if(($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' && isset($arFieldsProduct['QUANTITY']) && floatval($arFieldsProduct['QUANTITY'])==0)
			|| ($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y' && $this->IsEmptyPrice($arFieldsPrices)))
		{
			$arFieldsElement['ACTIVE'] = 'N';
		}
		
		$arKeys = array_merge(array('ID', 'NAME'), array_keys($arFieldsElement));
		
		$arFilter = array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N');
		foreach($arUid as $v)
		{
			if(strlen($v['valUid']) != strlen(trim($v['valUid'])))
			{
				$arFilter[] = array('LOGIC'=>'OR', array('='.$v['uid']=>trim($v['valUid'])), array('='.$v['uid']=>$v['valUid']));
			}
			else
			{
				$arFilter['='.$v['uid']] = trim($v['valUid']);
			}
		}
		
		if(!empty($arFieldsIpropTemp))
		{
			$arFieldsElement['IPROPERTY_TEMPLATES'] = $arFieldsIpropTemp;
		}

		$elemName = '';
		$dbRes = \CIblockElement::GetList(array(), $arFilter, false, false, $arKeys);
		while($arElement = $dbRes->Fetch())
		{
			$OFFER_ID = $arElement['ID'];
			$arFieldsProps2 = $arFieldsProps;
			$arFieldsElement2 = $arFieldsElement;
			$arFieldsProduct2 = $arFieldsProduct;
			$arFieldsPrices2 = $arFieldsPrices;
			$arFieldsProductStores2 = $arFieldsProductStores;
			if($this->conv->UpdateProperties($arFieldsProps2, $OFFER_ID)!==false
				&& $this->conv->UpdateElementFields($arFieldsElement2, $OFFER_ID)!==false
				&& $this->conv->UpdateProduct($arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $OFFER_ID)!==false)
			{
				$this->BeforeElementSave($OFFER_ID, 'update');
				if($this->params['ONLY_CREATE_MODE']!='Y')
				{				
					if(!empty($this->fieldOnlyNewOffer))
					{
						$this->UnsetExcessFields($this->fieldOnlyNewOffer, $arFieldsElement2, $arFieldsProps2, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $arFieldsProductDiscount);
					}
					
					$this->SaveProperties($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProps2);
					$this->SaveProduct($OFFER_ID, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $ID);
					
					$el = new \CIblockElement();
					if($this->UpdateElement($el, $OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsElement2, $arElement))
					{
						//$this->SetTimeBegin($OFFER_ID);
					}
					else
					{
						$this->stepparams['error_line']++;
						$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_UPDATE_OFFER_ERROR"), $el->LAST_ERROR, $this->worksheetCurrentRow);
					}
						
					$elemName = $arElement['NAME'];
					$this->SaveDiscount($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProductDiscount, $elemName, true);
					$this->stepparams['sku_updated_line']++;
				}
			}
			$this->SaveElementId($OFFER_ID, true);
		}
		if($elemName && !$arFieldsElement['NAME']) $arFieldsElement['NAME'] = $elemName;
		
		if($dbRes->SelectedRowsCount()==0)
		{
			if($this->params['ONLY_UPDATE_MODE']!='Y')
			{
				if(isset($arFieldsElement['ID']))
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NEW_OFFER_WITH_ID"), $arFieldsElement['ID'], $this->worksheetCurrentRow);
					return false;
				}
				if(strlen($arFieldsElement['NAME'])==0)
				{
					$arFieldsElement['NAME'] = $NAME;
				}
				if($this->params['ELEMENT_NEW_DEACTIVATE']=='Y')
				{
					$arFieldsElement['ACTIVE'] = 'N';
				}
				elseif(!$arFieldsElement['ACTIVE'])
				{
					$arFieldsElement['ACTIVE'] = 'Y';
				}
				$arFieldsElement['IBLOCK_ID'] = $OFFERS_IBLOCK_ID;
				$this->GenerateElementCode($arFieldsElement, $iblockFields);
				$this->PrepareElementPictures($arFieldsElement);
				$el = new \CIblockElement();
				$OFFER_ID = $el->Add($arFieldsElement, false, true, true);
				
				if($OFFER_ID)
				{
					$this->BeforeElementSave($OFFER_ID, 'add');
					$this->logger->AddElementChanges('IE_', $arFieldsElement);
					//$this->SetTimeBegin($OFFER_ID);
					$this->SaveProperties($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProps, true);
					$this->SaveProduct($OFFER_ID, $arFieldsProduct, $arFieldsPrices, $arFieldsProductStores, $ID);
					$this->SaveDiscount($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProductDiscount, $arFieldsElement['NAME'], true);
					if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']))
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($OFFERS_IBLOCK_ID, $OFFER_ID);
						$ipropValues->clearValues();
					}
					$this->stepparams['sku_added_line']++;
					$this->SaveElementId($OFFER_ID, true);
				}
				else
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_OFFER_ERROR"), $el->LAST_ERROR, $this->worksheetCurrentRow);
					return false;
				}
			}
			else
			{
				$this->logger->SaveElementNotFound($arFilter);
			}
		}

		if($OFFER_ID)
		{
			if($this->params['ONAFTERSAVE_HANDLER'])
			{
				$this->ExecuteOnAfterSaveHandler($this->params['ONAFTERSAVE_HANDLER'], $OFFER_ID);
			}
		}
		
		/*Update product*/
		if($ID && $OFFER_ID && ($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' || $this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y') && class_exists('\Bitrix\Catalog\ProductTable') && class_exists('\Bitrix\Catalog\PriceTable'))
		{
			$arOfferIds = array();
			$offersActive = false;
			$dbRes = \CIblockElement::GetList(array(), array(
				'IBLOCK_ID' => $OFFERS_IBLOCK_ID, 
				'PROPERTY_'.$OFFERS_PROPERTY_ID => $ID,
				'CHECK_PERMISSIONS' => 'N'), 
				false, false, array('ID', 'ACTIVE'));
			while($arr = $dbRes->Fetch())
			{
				$arOfferIds[] = $arr['ID'];
				$offersActive = (bool)($offersActive || ($arr['ACTIVE']=='Y'));
			}
			
			if(!empty($arOfferIds))
			{
				$active = false;
				if(!$offersActive) $active = 'N';
				else
				{
					if($this->params['ELEMENT_LOADING_ACTIVATE']=='Y') $active = 'Y';
					if($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y')
					{
						$existQuantity = \Bitrix\Catalog\ProductTable::getList(array(
							'select' => array('ID', 'QUANTITY'),
							'filter' => array('@ID' => $arOfferIds, '>QUANTITY' => 0),
							'limit' => 1
						))->fetch();
						if(!$existQuantity)  $active = 'N';
					}
					if($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y')
					{
						$existPrice = \Bitrix\Catalog\PriceTable::getList(array(
							'select' => array('ID', 'PRICE'),
							'filter' => array('@PRODUCT_ID' => $arOfferIds, '>PRICE' => 0),
							'limit' => 1
						))->fetch();
						if(!$existPrice)  $active = 'N';
					}
				}
				if($active!==false)
				{
					$arElem = \CIblockElement::GetList(array(), array('ID'=>$ID, 'CHECK_PERMISSIONS' => 'N'), false, false, array('ACTIVE'))->Fetch();
					if($arElem['ACTIVE']!=$active)
					{
						$el = new \CIblockElement();
						$el->Update($ID, array('ACTIVE'=>$active), false, true, true);
					}
				}
			}
		}
		if($ID && $OFFER_ID && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
		{
			$this->SaveProduct($ID, array('TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU), array(), array());
		}
		/*/Update product*/
		
		return true;
	}
	
	public function GetElementSections($ID)
	{
		$arSections = array();
		$dbRes = \CIBlockElement::GetElementGroups($ID, true, array('ID'));
		while($arr = $dbRes->Fetch())
		{
			$arSections[] = $arr['ID'];
		}
		return $arSections;
	}
	
	public function UnsetUidFields(&$arFieldsElement, &$arFieldsProps, $arUids, $saveVal=false)
	{
		foreach($arUids as $field)
		{
			if(strpos($field, 'IE_')===0)
			{
				$fieldKey = substr($field, 3);
				if(isset($arFieldsElement[$fieldKey]) && is_array($arFieldsElement[$fieldKey]))
				{
					if($saveVal)
					{
						$arFieldsElement[$fieldKey] = array_diff($arFieldsElement[$fieldKey], array(''));
						if(count($arFieldsElement[$fieldKey]) > 0) $arFieldsElement[$fieldKey] = end($arFieldsElement[$fieldKey]);
						else $arFieldsElement[$fieldKey] = '';
					}
					else unset($arFieldsElement[$fieldKey]);
				}
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldKey = substr($field, 7);
				if(isset($arFieldsProps[$fieldKey]) && is_array($arFieldsProps[$fieldKey]))
				{
					if($saveVal)
					{
						$arFieldsProps[$fieldKey] = array_diff($arFieldsProps[$fieldKey], array(''));
						if(count($arFieldsProps[$fieldKey]) > 0) $arFieldsProps[$fieldKey] = end($arFieldsProps[$fieldKey]);
						else $arFieldsProps[$fieldKey] = '';
					}
					else unset($arFieldsProps[$fieldKey]);
				}
			}
		}
	}
	
	public function UnsetExcessFields($fieldsList, &$arFieldsElement, &$arFieldsProps, &$arFieldsProduct, &$arFieldsPrices, &$arFieldsProductStores, &$arFieldsProductDiscount)
	{
		foreach($fieldsList as $field)
		{
			if(strpos($field, 'IE_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
				}
				unset($arFieldsElement[substr($field, 3)]);
			}
			elseif(strpos($field, 'ISECT')===0)
			{
				unset($arFieldsElement['IBLOCK_SECTION']);
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$arPrice = explode('_', substr($field, 10), 2);
				unset($arFieldsPrices[$arPrice[0]][$arPrice[1]]);
				if(empty($arFieldsPrices[$arPrice[0]])) unset($arFieldsPrices[$arPrice[0]]);
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				unset($arFieldsProductStores[$arStore[0]][$arStore[1]]);
				if(empty($arFieldsProductStores[$arStore[0]])) unset($arFieldsProductStores[$arStore[0]]);
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						unset($arFieldsProductDiscount[$adata[0]]);
					}
				}
				unset($arFieldsProductDiscount[substr($field, 14)]);
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				unset($arFieldsProduct[substr($field, 5)]);
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				unset($arFieldsProps[substr($field, 7)]);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				unset($arFieldsElement['IPROPERTY_TEMPLATES'][substr($field, 11)]);
			}
		}
	}
	
	public function UnsetExcessSectionFields($fieldsList, &$arFieldsSections, &$arFieldsElement)
	{
		foreach($fieldsList as $field)
		{
			if(strpos($field, 'ISECT')===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$arSect = explode('_', substr($field, 5), 2);
				unset($arFieldsSections[$arSect[0]][$arSect[1]]);
				
				if(is_array($adata) && count($adata) > 1)
				{
					unset($arFieldsSections[$arSect[0]][$adata[0]]);
				}
			}
			elseif($field=='IE_SECTION_PATH')
			{
				$field = substr($field, 3);
				unset($arFieldsElement[$field]);
			}
		}
	}
	
	public function GetPropField(&$arFieldsProps, &$arFieldsPropsOrig, $fieldSettingsExtra, $propDef, $fieldName, $value, $origValue, $arUids = array())
	{
		if(!isset($arFieldsProps[$fieldName])) $arFieldsProps[$fieldName] = null;
		if(!isset($arFieldsPropsOrig[$fieldName])) $arFieldsPropsOrig[$fieldName] = null;
		$arFieldsPropsItem = &$arFieldsProps[$fieldName];
		$arFieldsPropsOrigItem = &$arFieldsPropsOrig[$fieldName];
		
		if($propDef	&& $propDef['USER_TYPE']=='directory')
		{
			if($fieldSettingsExtra['HLBL_FIELD']) $key2 = $fieldSettingsExtra['HLBL_FIELD'];
			else $key2 = 'UF_NAME';
			if(!isset($arFieldsPropsItem[$key2])) $arFieldsPropsItem[$key2] = null;
			if(!isset($arFieldsPropsOrigItem[$key2])) $arFieldsPropsOrigItem[$key2] = null;
			$arFieldsPropsItem = &$arFieldsPropsItem[$key2];
			$arFieldsPropsOrigItem = &$arFieldsPropsOrigItem[$key2];
		}
		
		if(($propDef['MULTIPLE']=='Y' || in_array('IP_PROP'.$fieldName, $arUids)) && !is_null($arFieldsPropsItem))
		{
			if(is_array($arFieldsPropsItem))
			{
				$arFieldsPropsItem[] = $value;
				$arFieldsPropsOrigItem[] = $origValue;
			}
			else
			{
				$arFieldsPropsItem = array($arFieldsPropsItem, $value);
				$arFieldsPropsOrigItem = array($arFieldsPropsOrigItem, $origValue);
			}
		}
		else
		{
			$arFieldsPropsItem = $value;
			$arFieldsPropsOrigItem = $origValue;
		}
	}
	
	public function GetPropList(&$arFieldsProps, &$arFieldsPropsOrig, $fieldSettingsExtra, $IBLOCK_ID, $value)
	{
		if(strlen($fieldSettingsExtra['PROPLIST_PROPS_SEP'])==0 || strlen($fieldSettingsExtra['PROPLIST_PROPVALS_SEP'])==0) return;
		$arProps = explode($fieldSettingsExtra['PROPLIST_PROPS_SEP'], $value);
		foreach($arProps as $prop)
		{
			$arCurProp = explode($fieldSettingsExtra['PROPLIST_PROPVALS_SEP'], $prop, 2);
			if(count($arCurProp)!=2) continue;
			$arCurProp = array_map('trim', $arCurProp);
			if(strlen($arCurProp[0])==0) continue;
			$createNew = ($fieldSettingsExtra['PROPLIST_CREATE_NEW']=='Y');
			$propDef = $this->GetIblockPropertyByName($arCurProp[0], $IBLOCK_ID, $createNew);
			if($propDef!==false)
			{
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, array(), $propDef, $propDef['ID'], $arCurProp[1], $arCurProp[1]);
			}
		}
	}
	
	public function SaveElementId($ID, $offer=false)
	{
		$fn = ($offer ? $this->fileOffersId : $this->fileElementsId);
		$handle = fopen($fn, 'a');
		fwrite($handle, $ID."\r\n");
		fclose($handle);
		$this->logger->SaveElementChanges($ID);
	}
	
	public function BeforeElementSave($ID, $type="update")
	{
		$this->logger->SetNewElement($ID, $type);
	}
	
	public function BeforeElementDelete($ID, $IBLOCK_ID)
	{
		$this->logger->SetNewElement($ID, 'delete');
	}
	
	public function AfterElementDelete($ID, $IBLOCK_ID)
	{
		$this->logger->AddElementChanges('IE_', array('ID'=>$ID));
		$this->logger->SaveElementChanges($ID);
		$this->stepparams['element_removed_line']++;
	}
	
	public function ApplyMargins($val, $fieldKey)
	{
		$sval = $val;
		$margins = $this->fieldSettings[$fieldKey]['MARGINS'];
		if(is_array($margins) && count($margins) > 0)
		{
			foreach($margins as $margin)
			{
				if((strlen(trim($margin['PRICE_FROM']))==0 || $sval >= floatval($margin['PRICE_FROM']))
					&& (strlen(trim($margin['PRICE_TO']))==0 || $sval <= floatval($margin['PRICE_TO'])))
				{
					$val *= (1 + ($margin['TYPE'] > 0 ? 1 : -1)*floatval($margin['PERCENT'])/100);
				}
			}
		}
		
		/*Rounding*/
		$roundRule = $this->fieldSettings[$fieldKey]['PRICE_ROUND_RULE'];
		$roundRatio = $this->fieldSettings[$fieldKey]['PRICE_ROUND_COEFFICIENT'];
		$roundRatio = str_replace(',', '.', $roundRatio);
		if(!preg_match('/^[\d\.]+$/', $roundRatio)) $roundRatio = 1;
		
		if($roundRule=='ROUND')	$val = round($val / $roundRatio) * $roundRatio;
		elseif($roundRule=='CEIL') $val = ceil($val / $roundRatio) * $roundRatio;
		elseif($roundRule=='FLOOR') $val = floor($val / $roundRatio) * $roundRatio;
		/*/Rounding*/
		
		return $val;
	}
	
	function GetFilesByExt($path, $arExt=array())
	{
		$arFiles = array();
		$arDirFiles = array_diff(scandir($path), array('.', '..'));
		foreach($arDirFiles as $file)
		{
			if(is_file($path.$file) && (empty($arExt) || preg_match('/\.('.implode('|', $arExt).')$/i', ToLower($file))))
			{
				$arFiles[] = $path.$file;
			}
		}
		foreach($arDirFiles as $file)
		{
			if(is_dir($path.$file))
			{
				$arFiles = array_merge($arFiles, $this->GetFilesByExt($path.$file.'/', $arExt));
			}
		}
		return $arFiles;
	}
	
	public function CreateTmpImageDir()
	{
		$tmpsubdir = $this->imagedir.($this->filecnt++).'/';
		CheckDirPath($tmpsubdir);
		return $tmpsubdir;
	}
	
	public function GetFileArray($file, $arDef=array(), $arParams=array())
	{
		$file = trim($file);
		if($file=='-')
		{
			return array('del'=>'Y');
		}
		elseif(strpos($file, '/')===0)
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$arFile = \CFile::MakeFileArray($file);
			$file = $tmpsubdir.$arFile['name'];
			copy($arFile['tmp_name'], $file);
		}
		elseif(strpos($file, 'zip://')===0)
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$oldfile = $file;
			$file = $tmpsubdir.basename($oldfile);
			copy($oldfile, $file);
		}
		elseif(preg_match('/ftp(s)?:\/\//', $file))
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$arFile = $this->sftp->MakeFileArray($file);
			$file = $tmpsubdir.$arFile['name'];
			copy($arFile['tmp_name'], $file);
		}
		elseif($service = $this->cloud->GetService($file))
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			if($arFile = $this->cloud->MakeFileArray($service, $file))
			{
				$file = $tmpsubdir.$arFile['name'];
				copy($arFile['tmp_name'], $file);
			}
		}
		elseif(preg_match('/http(s)?:\/\//', $file))
		{
			$file = urldecode($file);
			$arUrl = parse_url($file);
			//Cyrillic domain
			if(preg_match('/[^A-Za-z0-9\-\.]/', $arUrl['host']))
			{
				if(!class_exists('idna_convert')) require_once(dirname(__FILE__).'/../../lib/idna_convert.class.php');
				if(class_exists('idna_convert'))
				{
					$idn = new \idna_convert();
					$oldHost = $arUrl['host'];
					if(!\CUtil::DetectUTF8($oldHost)) $oldHost = \Bitrix\EsolImportxml\Utils::Win1251Utf8($oldHost);
					$file = str_replace($arUrl['host'], $idn->encode($oldHost), $file);
				}
			}
			if(class_exists('\Bitrix\Main\Web\HttpClient'))
			{
				$tmpsubdir = $this->CreateTmpImageDir();
				$tempPath = $tmpsubdir.bx_basename($file);
				$tempPath2 = $tmpsubdir.(\Bitrix\Main\IO\Path::convertLogicalToPhysical(bx_basename($file)));
				$arOptions = array();
				if($this->useProxy) $arOptions = $this->proxySettings;
				$arOptions['disableSslVerification'] = true;
				$arOptions['socketTimeout'] = $arOptions['streamTimeout'] = 10;
				$ob = new \Bitrix\Main\Web\HttpClient($arOptions);
				$ob->setHeader('User-Agent', 'BitrixSM HttpClient class');
				try{
					if(!\CUtil::DetectUTF8($file)) $file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
					$file = preg_replace_callback('/[^:\/?=&#]+/', create_function('$m', 'return rawurlencode($m[0]);'), $file);
					if($ob->download($file, $tempPath) && $ob->getStatus()!=404) $file = $tempPath2;
					else return array();
				}catch(Exception $ex){}
			}
		}
		$arFile = \CFile::MakeFileArray($file);
		
		if(!$arFile['name'] && !\CUtil::DetectUTF8($file))
		{
			$file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
			$arFile = \CFile::MakeFileArray($file);
		}
		
		$fileTypes = array();
		$bNeedImage = (bool)($arParams['FILETYPE']=='IMAGE');
		if($bNeedImage) $fileTypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		elseif($arParams['FILE_TYPE']) $fileTypes = array_diff(array_map('trim', explode(',', ToLower($arParams['FILE_TYPE']))), array(''));
		if($arFile['type']=='application/zip' && !empty($fileTypes) && !in_array('zip', $fileTypes))
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$zipObj = \CBXArchive::GetArchive($arFile['tmp_name'], 'ZIP');
			$zipObj->Unpack($tmpsubdir);
			$arFile = array();
			$arFiles = $this->GetFilesByExt($tmpsubdir, $fileTypes);
			if($arParams['MULTIPLE']=='Y' && count($arFiles) > 1)
			{
				foreach($arFiles as $k=>$v)
				{
					$arFiles[$k] = \CFile::MakeFileArray($v);
				}
				$arFile = array('VALUES'=>$arFiles);
			}
			elseif(count($arFiles) > 0)
			{
				$tmpfile = current($arFiles);
				$arFile = \CFile::MakeFileArray($tmpfile);
			}
		}
		
		if(strpos($arFile['type'], 'image/')===0)
		{
			$ext = ToLower(str_replace('image/', '', $arFile['type']));
			if(substr($arFile['name'], -(strlen($ext) + 1))!='.'.$ext)
			{
				if($ext!='jpeg' || (($ext='jpg') && substr($arFile['name'], -(strlen($ext) + 1))!='.'.$ext))
				{
					$arFile['name'] = $arFile['name'].'.'.$ext;
				}
			}
		}
		elseif($bNeedImage) $arFile = array();

		if(!empty($arDef) && !empty($arFile))
		{
			if(isset($arFile['VALUES']))
			{
				foreach($arFile['VALUES'] as $k=>$v)
				{
					$arFile['VALUES'][$k] = $this->PictureProcessing($v, $arDef);
				}
			}
			else
			{
				$arFile = $this->PictureProcessing($arFile, $arDef);
			}
		}
		if(!empty($arFile) && strpos($arFile['type'], 'image/')===0)
		{
			$arCacheKeys = array('width'=>$width, 'height'=>$height, 'size'=>$arFile['size']);
			if($this->params['ELEMENT_NOT_CHECK_NAME_IMAGES']!='Y') $arCacheKeys['name'] = $arFile['name'];
			list($width, $height, $type, $attr) = getimagesize($arFile['tmp_name']);
			$arFile['external_id'] = 'i_'.md5(serialize($arCacheKeys));
		}
		if(!empty($arFile) && strpos($arFile['type'], 'html')!==false)
		{
			$arFile = array();
		}
		
		return $arFile;
	}	
	
	public function SetTimeBegin($ID)
	{
		if($this->stepparams['begin_time']) return;
		$dbRes = \CIblockElement::GetList(array(), array('ID'=>$ID, 'CHECK_PERMISSIONS' => 'N'), false, false, array('TIMESTAMP_X'));
		if($arr = $dbRes->Fetch())
		{
			$this->stepparams['begin_time'] = $arr['TIMESTAMP_X'];
		}
	}
	
	public function IsEmptyPrice($arPrices)
	{
		if(is_array($arPrices))
		{
			foreach($arPrices as $arPrice)
			{
				if($arPrice['PRICE'] > 0)
				{
					return false;
				}
			}
		}
		return true;
	}
	
	public function GetHLBoolValue($val)
	{
		$res = $this->GetBoolValue($val);
		if($res=='Y') return 1;
		else return 0;
	}
	
	public function GetBoolValue($val, $numReturn = false)
	{
		$trueVals = array_map('trim', explode(',', Loc::getMessage("ESOL_IX_FIELD_VAL_Y")));
		$falseVals = array_map('trim', explode(',', Loc::getMessage("ESOL_IX_FIELD_VAL_N")));
		if(in_array(ToLower($val), $trueVals))
		{
			return ($numReturn ? 1 : 'Y');
		}
		elseif(in_array(ToLower($val), $falseVals))
		{
			return ($numReturn ? 0 : 'N');
		}
		else
		{
			return false;
		}
	}
	
	public function SaveSection($arFields, $IBLOCK_ID, $parent=0, $level=0, $arParams=array())
	{
		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		$sectionFields = $this->GetIblockSectionFields($IBLOCK_ID);
		$sectId = false;
		$arPictures = array('PICTURE', 'DETAIL_PICTURE');
		foreach($arPictures as $picName)
		{
			if($arFields[$picName])
			{
				if(is_array($arFields[$picName])) $arFields[$picName] = current($arFields[$picName]);
				$arFields[$picName] = $this->GetFileArray($arFields[$picName]);
			}
		}
		
		if(isset($arFields['ACTIVE']))
		{
			$arFields['ACTIVE'] = $this->GetBoolValue($arFields['ACTIVE']);
		}
		
		$arTexts = array('DESCRIPTION');
		foreach($arTexts as $keyText)
		{
			if($arFields[$keyText])
			{
				$textFile = $_SERVER["DOCUMENT_ROOT"].$arFields[$keyText];
				if(file_exists($textFile) && is_file($textFile) && is_readable($textFile))
				{
					$arFields[$keyText] = file_get_contents($textFile);
				}
			}
		}
		
		foreach($arFields as $k=>$v)
		{
			if(isset($sectionFields[$k]))
			{
				$sParams = $sectionFields[$k];
				//$fieldSettings = $this->fieldSettings['ISECT'.$level.'_'.$k];
				$fieldSettings = $this->fieldSettings['ISECT_'.$k];
				if(!is_array($fieldSettings)) $fieldSettings = array();
				if($sParams['MULTIPLE']=='Y')
				{
					if(!is_array($arFields[$k]))
					{
						$separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
						if($fieldSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y')
						{
							$separator = $fieldSettings['MULTIPLE_SEPARATOR'];
						}
						$arFields[$k] = array_map('trim', explode($separator, $arFields[$k]));
					}
					foreach($arFields[$k] as $k2=>$v2)
					{
						$arFields[$k][$k2] = $this->GetSectionField($v2, $sParams, $fieldSettings);
					}
				}
				else
				{
					$arFields[$k] = $this->GetSectionField($arFields[$k], $sParams, $fieldSettings);
				}
			}
			if(strpos($k, 'IPROP_TEMP_')===0)
			{
				$arFields['IPROPERTY_TEMPLATES'][substr($k, 11)] = $v;
				unset($arFields[$k]);
			}
		}
		
		if($parent > 0) $arFields['IBLOCK_SECTION_ID'] = $parent;
		
		$sectionUid = $this->params['SECTION_UID'];
		if(!$arFields[$sectionUid]) $sectionUid = 'NAME';
		if(strlen(trim($arFields[$sectionUid]))==0) return false;
		$arFilter = array(
			$sectionUid=>$arFields[$sectionUid],
			'IBLOCK_ID'=>$IBLOCK_ID,
			'CHECK_PERMISSIONS' => 'N'
		);
		if(!isset($arFields['IGNORE_PARENT_SECTION']) || $arFields['IGNORE_PARENT_SECTION']!='Y') $arFilter['SECTION_ID'] = $parent;
		else unset($arFields['IGNORE_PARENT_SECTION']);
		
		if($arParams['SECTION_SEARCH_IN_SUBSECTIONS']=='Y')
		{
			if($parent && $arParams['SECTION_SEARCH_WITHOUT_PARENT']!='Y')
			{
				$dbRes2 = \CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID, 'ID'=>$parent, 'CHECK_PERMISSIONS' => 'N'), false, array('ID', 'LEFT_MARGIN', 'RIGHT_MARGIN'));
				if($arParentSection = $dbRes2->Fetch())
				{
					$arFilter['>LEFT_MARGIN'] = $arParentSection['LEFT_MARGIN'];
					$arFilter['<RIGHT_MARGIN'] = $arParentSection['RIGHT_MARGIN'];
				}
			}
			unset($arFilter['SECTION_ID']);
		}
		$dbRes = \CIBlockSection::GetList(array(), $arFilter, false, array_merge(array('ID'), array_keys($arFields)));
		$arSections = array();
		while($arSect = $dbRes->Fetch())
		{
			$sectId = $arSect['ID'];
			if($this->params['ONLY_CREATE_MODE']!='Y' && $this->conv->UpdateSectionFields($arFields, $sectId)!==false)
			{
				foreach($arSect as $k=>$v)
				{
					if(isset($arFields[$k]) && ($arFields[$k]==$v || ($k=='NAME' && ToLower($arFields[$k])==ToLower($v)))) unset($arFields[$k]);
				}
				if(!empty($arFields))
				{
					$bs = new \CIBlockSection;
					$bs->Update($sectId, $arFields, true, true, true);
					if(!empty($arFields['IPROPERTY_TEMPLATES']) || $arFields['NAME'])
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($IBLOCK_ID, $sectId);
						$ipropValues->clearValues();
					}
				}
				$this->stepparams['section_updated_line']++;
			}
			$arSections[] = $sectId;
		}
		if(empty($arSections) && $this->params['ONLY_UPDATE_MODE']!='Y')
		{
			if(strlen(trim($arFields['NAME']))==0) return false;
			if(!isset($arFields['ACTIVE'])) $arFields['ACTIVE'] = 'Y';
			$arFields['IBLOCK_ID'] = $IBLOCK_ID;

			if(($iblockFields['SECTION_CODE']['IS_REQUIRED']=='Y' || $iblockFields['SECTION_CODE']['DEFAULT_VALUE']['TRANSLITERATION']=='Y') && strlen($arFields['CODE'])==0)
			{
				$arFields['CODE'] = $this->Str2Url($arFields['NAME'], $iblockFields['SECTION_CODE']['DEFAULT_VALUE']);
				if($iblockFields['SECTION_CODE']['DEFAULT_VALUE']['UNIQUE']=='Y' && $sectionUid!='CODE')
				{
					$j = 0;
					$jmax = 1000;
					$code = $arFields['CODE'];
					while($j<$jmax && (\CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID, 'CODE'=>$arFields['CODE']), false, array('ID'))->Fetch()) && ($arFields['CODE'] = $code.strval(++$j))){}
				}
			}
			$bs = new \CIBlockSection;
			$sectId = $j = 0;
			$code = $arFields['CODE'];
			$jmax = ($sectionUid=='CODE' ? 1 : 1000);
			while($j<$jmax && !($sectId = $bs->Add($arFields, true, true, true)) && ($arFields['CODE'] = $code.strval(++$j))){}
			if($sectId)
			{
				$this->stepparams['section_added_line']++;
			}
			else
			{
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_SECTION_ERROR"), $arFields['NAME'], $bs->LAST_ERROR, $this->worksheetCurrentRow);
			}
			$arSections[] = $sectId;
		}
		return $arSections;
	}
	
	public function GetSectionField($val, $sParams, $fieldSettings)
	{
		$userType = $sParams['USER_TYPE_ID'];
		if($userType=='file')
		{
			$val = $this->GetFileArray($val);
		}
		elseif($userType=='boolean')
		{
			$val = $this->GetBoolValue($val, true);
		}
		elseif($userType=='iblock_element')
		{
			$arProp = array('LINK_IBLOCK_ID' => $sParams['SETTINGS']['IBLOCK_ID']);
			$val = $this->GetIblockElementValue($arProp, $val, $fieldSettings);
		}
		return $val;
	}
	
	public function GetSections(&$arElement, $IBLOCK_ID, $SECTION_ID, $arSections)
	{
		if(isset($arElement['IBLOCK_SECTION']) && !empty($arElement['IBLOCK_SECTION']) && $this->params['ELEMENT_ADD_NEW_SECTIONS']!='Y') return;
		//if(!isset($arElement['SECTION_PATH'])) return;
	
		$arMultiSections = array();
		if(is_array($arElement['SECTION_PATH']))
		{
			foreach($arElement['SECTION_PATH'] as $sectionPath)
			{
				if(is_array($sectionPath))
				{
					$tmpSections = array();
					foreach($sectionPath as $k=>$name)
					{
						$tmpSections[$k+1]['NAME'] = $name;
					}
					$arMultiSections[] = $tmpSections;
				}
			}
			unset($arElement['SECTION_PATH']);
		}

		/*if no 1st level*/
		if($SECTION_ID > 0 && !empty($arSections) && !isset($arSections[1]))
		{
			$minKey = min(array_keys($arSections));
			$arSectionsOld = $arSections;
			$arSections = array();
			foreach($arSectionsOld as $k=>$v)
			{
				$arSections[$k - $minKey + 1] = $v;
			}
		}
		/*/if no 1st level*/
		
		if((empty($arSections) || !isset($arSections[1]) || count(array_diff($arSections[1], array('')))==0) && empty($arMultiSections))
		{
			if($SECTION_ID > 0)
			{
				if($this->params['ELEMENT_ADD_NEW_SECTIONS']=='Y' && is_array($arElement['IBLOCK_SECTION']))
					$arElement['IBLOCK_SECTION'][] = $SECTION_ID;
				else
					$arElement['IBLOCK_SECTION'] = array($SECTION_ID);
				return true;
			}
			return false;
		}
		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		
		if(empty($arMultiSections))
		{
			$arMultiSections[] = $arSections;
			$fromSectionPath = false;
		}
		else
		{
			if(count($arMultiSections)==1 && !empty($arSections))
			{
				foreach($arMultiSections as $k=>$v)
				{
					foreach($arSections as $k2=>$v2)
					{
						if($v2[$this->params['SECTION_UID']])
						{
							$lkey = $k2;
							$fsKey = 'ISECT'.$k2.'_'.$this->params['SECTION_UID'];
							if($this->fieldSettings[$fsKey]['SECTION_SEARCH_IN_SUBSECTIONS'] == 'Y')
							{
								$lkey = max(array_keys($v));
								$v2['IGNORE_PARENT_SECTION'] = 'Y';
							}
							if(isset($v[$lkey]))
							{
								$arMultiSections[$k][$lkey] = array_merge($v[$lkey], $v2);
							}
						}
					}
				}
			}
			$fromSectionPath = true;
		}
			
		foreach($arMultiSections as $arSections)
		{
			$parent = $i = 0;
			$arParents = array();
			if($SECTION_ID)
			{
				$parent = $SECTION_ID;
				$arParents[] = $SECTION_ID;
			}
			while(++$i && !empty($arSections[$i]))
			{
				$sectionUid = $this->params['SECTION_UID'];
				if(!$arSections[$i][$sectionUid]) $sectionUid = 'NAME';
				if(!$arSections[$i][$sectionUid]) continue;

				if($fromSectionPath) $fsKey = 'IE_SECTION_PATH';
				else $fsKey = 'ISECT'.$i.'_'.$sectionUid;
				
				if($this->fieldSettings[$fsKey]['SECTION_UID_SEPARATED']=='Y' && empty($arSections[$i+1]))
				{
					$arNames = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arSections[$i][$sectionUid]));
					$arNames = array_diff($arNames, array(''));
				}
				else
				{
					$arNames = array($arSections[$i][$sectionUid]);
				}
				if(empty($arNames)) continue;
				$arParents = array();
				
				$parentLvl = array();
				foreach($arNames as $name)
				{
					if(isset($this->sections[$parent][$name]) && !empty($this->sections[$parent][$name]))
					{
						$parentLvl = $this->sections[$parent][$name];
					}
					else
					{				
						$arFields = $arSections[$i];
						$arFields[$sectionUid] = $name;
						$sectId = $this->SaveSection($arFields, $IBLOCK_ID, $parent, $i, $this->fieldSettings[$fsKey]);
						$this->sections[$parent][$name] = $sectId;
						if(!empty($sectId)) $parentLvl = $sectId;
					}
					$arParents = array_merge($arParents, $parentLvl);
				}
				$parent = current(array_diff($parentLvl, array(0)));
				if(!$parent)
				{
					$parent = 0;
					/*continue;*/ break;
				}
			}
			
			if(!empty($arParents))
			{
				if(!is_array($arElement['IBLOCK_SECTION'])) $arElement['IBLOCK_SECTION'] = array();
				$arElement['IBLOCK_SECTION'] = array_unique(array_merge($arElement['IBLOCK_SECTION'], $arParents));
				$arElement['IBLOCK_SECTION_ID'] = current($arElement['IBLOCK_SECTION']);
			}
		}
	}
	
	public function GetIblockProperties($IBLOCK_ID, $byName = false)
	{
		if(!$this->props[$IBLOCK_ID])
		{
			$this->props[$IBLOCK_ID] = array();
			$this->propsByNames[$IBLOCK_ID] = array();
			$dbRes = \CIBlockProperty::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID));
			while($arProp = $dbRes->Fetch())
			{
				$this->props[$IBLOCK_ID][$arProp['ID']] = $arProp;
				$this->propsByNames[$IBLOCK_ID][ToLower($arProp['NAME'])] = $arProp;
			}
		}
		if($byName) return $this->propsByNames[$IBLOCK_ID];
		else return $this->props[$IBLOCK_ID];
	}
	
	public function GetIblockPropertyByName($name, $IBLOCK_ID, $createNew = false)
	{
		$name = trim($name);
		$lowerName = ToLower($name);
		$arProps = $this->GetIblockProperties($IBLOCK_ID, true);
		if(isset($arProps[$lowerName])) return $arProps[$lowerName];
		if($createNew)
		{
			$arParams = array(
				'max_len' => 50,
				'change_case' => 'U',
				'replace_space' => '_',
				'replace_other' => '_',
				'delete_repeat_replace' => 'Y',
			);
			$code = \CUtil::translit($name, LANGUAGE_ID, $arParams);
			$code = preg_replace('/[^a-zA-Z0-9_]/', '', $code);
			$code = preg_replace('/^[0-9_]+/', '', $code);
			
			$arFields = Array(
				"NAME" => $name,
				"ACTIVE" => "Y",
				"CODE" => $code,
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $IBLOCK_ID
			);
			$ibp = new \CIBlockProperty;
			$propID = $ibp->Add($arFields);
			if(!$propID) return false;
			
			$dbRes = \CIBlockProperty::GetList(array(), array('ID'=>$propID));
			if($arProp = $dbRes->Fetch())
			{
				$this->props[$IBLOCK_ID][$arProp['ID']] = $arProp;
				$this->propsByNames[$IBLOCK_ID][ToLower($arProp['NAME'])] = $arProp;
				return $arProp;
			}
		}
		return false;
	}
	
	public function RemoveProperties($ID, $IBLOCK_ID)
	{
		if(is_array($this->params['ADDITIONAL_SETTINGS'][$this->worksheetNum]['ELEMENT_PROPERTIES_REMOVE']))
		{
			$arIds = $this->params['ADDITIONAL_SETTINGS'][$this->worksheetNum]['ELEMENT_PROPERTIES_REMOVE'];
		}
		else
		{
			$arIds = $this->params['ELEMENT_PROPERTIES_REMOVE'];
		}
		if(is_array($arIds) && !empty($arIds))
		{
			$arIblockProps = $this->GetIblockProperties($IBLOCK_ID);
			$arProps = array();
			foreach($arIds as $k=>$v)
			{
				if(strpos($v, 'IP_PROP')===0) $pid = (int)substr($v, strlen('IP_PROP'));
				else $pid = (int)$v;
				if($pid > 0)
				{
					if($arIblockProps[$pid]['PROPERTY_TYPE']=='F') $arProps[$pid] = array("del"=>"Y");
					else $arProps[$pid] = false;
				}
			}
			if(!empty($arProps))
			{
				\CIBlockElement::SetPropertyValuesEx($ID, $IBLOCK_ID, $arProps);
			}
		}
	}
	
	public function GetMultipleProperty($val, $k)
	{
		$separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
		$fsKey = 'IP_PROP'.$k;
		//$fsKey = ($this->conv->GetSkuMode() ? 'OFFER_' : '').'IP_PROP'.$k;
		if($this->fieldSettings[$fsKey]['CHANGE_MULTIPLE_SEPARATOR']=='Y')
		{
			$separator = $this->fieldSettings[$fsKey]['MULTIPLE_SEPARATOR'];
		}
		if(is_array($val))
		{
			$arVal = array();
			foreach($val as $subval)
			{
				if(is_array($subval)) $arVal[] = $subval;
				else $arVal = array_merge($arVal, explode($separator, $subval));
			}
		}
		else
		{
			if(is_array($val)) $arVal = $val;
			else $arVal = explode($separator, $val);
		}
		return $arVal;
	}
	
	public function SaveProperties($ID, $IBLOCK_ID, $arProps, $needUpdate = false)
	{
		if(empty($arProps)) return false;
		$propsDef = $this->GetIblockProperties($IBLOCK_ID);
		
		foreach($arProps as $k=>$prop)
		{
			if(!is_numeric($k)) continue;
			if($propsDef[$k]['USER_TYPE']=='directory' && $propsDef[$k]['MULTIPLE']=='Y' && is_array($prop))
			{
				$newProp = array();
				foreach($prop as $k2=>$v2)
				{
					$arVal = $this->GetMultipleProperty($v2, $k);
					foreach($arVal as $k3=>$v3)
					{
						$newProp[$k3][$k2] = $v3;
					}
				}
				$arProps[$k] = $newProp;
			}
		}
		
		foreach($arProps as $k=>$prop)
		{
			if(strpos($k, '_DESCRIPTION')!==false) continue;
			if($propsDef[$k]['MULTIPLE']=='Y')
			{
				if($propsDef[$k]['USER_TYPE']=='directory') $arVal = $prop;
				else $arVal = $this->GetMultipleProperty($prop, $k);
				
				$fromValue = $this->fieldSettings[$fsKey]['MULTIPLE_FROM_VALUE'];
				$toValue = $this->fieldSettings[$fsKey]['MULTIPLE_TO_VALUE'];
				if(is_numeric($fromValue) || is_numeric($toValue))
				{
					$from = (is_numeric($fromValue) ? ((int)$fromValue >= 0 ? ((int)$fromValue - 1) : (int)$fromValue) : 0);
					$to = (is_numeric($toValue) ? ((int)$toValue >= 0 ? ((int)$toValue - max(0, $from)) : (int)$toValue) : 0);
					if($to!=0) $arVal = array_slice($arVal, $from, $to);
					else $arVal = array_slice($arVal, $from);
				}
				
				$newVals = array();
				foreach($arVal as $k2=>$val)
				{
					$arVal[$k2] = $this->GetPropValue($propsDef[$k], (is_string($val) ? trim($val) : $val));
					if(is_array($arVal[$k2]) && isset($arVal[$k2]['VALUES']))
					{
						$newVals = array_merge($newVals, $arVal[$k2]['VALUES']);
						unset($arVal[$k2]);
					}
				}
				if(!empty($newVals)) $arVal = array_merge($arVal, $newVals);
				$arProps[$k] = $arVal;
			}
			else
			{
				$arProps[$k] = $this->GetPropValue($propsDef[$k], $prop);
			}
		}
		foreach($arProps as $k=>$prop)
		{
			if(strpos($k, '_DESCRIPTION')===false) continue;
			$pk = substr($k, 0, strpos($k, '_'));
			if(!isset($arProps[$pk]))
			{
				$dbRes = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array(), Array("ID"=>$pk));
				while($arPropValue = $dbRes->Fetch())
				{
					if($propsDef[$pk]['MULTIPLE']=='Y')
					{
						$arProps[$pk][] = $arPropValue['VALUE'];
					}
					else
					{
						$arProps[$pk] = $arPropValue['VALUE'];
					}
				}
				if(isset($arProps[$pk]))
				{
					if($propsDef[$pk]['PROPERTY_TYPE']=='F')
					{
						if(is_array($arProps[$pk]))
						{
							foreach($arProps[$pk] as $k2=>$v2)
							{
								$arProps[$pk][$k2] = \CFile::MakeFileArray($v2);
							}
						}
						else
						{
							$arProps[$pk] = \CFile::MakeFileArray($arProps[$pk]);
						}
					}
				}
			}
			if(isset($arProps[$pk]))
			{
				if($propsDef[$pk]['MULTIPLE']=='Y')
				{
					$arVal = $this->GetMultipleProperty($prop, $pk);
					foreach($arProps[$pk] as $k2=>$v2)
					{
						if(isset($arVal[$k2]))
						{
							if(is_array($v2) && isset($v2['VALUE']))
							{
								$v2['DESCRIPTION'] = $arVal[$k2];
								$arProps[$pk][$k2] = $v2;
							}
							else
							{
								$arProps[$pk][$k2] = array(
									'VALUE' => $v2,
									'DESCRIPTION' => $arVal[$k2]
								);
							}
						}
					}
				}
				else
				{
					if(is_array($arProps[$pk]) && isset($arProps[$pk]['VALUE']))
					{
						$arProps[$pk]['DESCRIPTION'] = $prop;
					}
					else
					{
						$arProps[$pk] = array(
							'VALUE' => $arProps[$pk],
							'DESCRIPTION' => $prop
						);
					}
				}
			}
			unset($arProps[$k]);
		}
		
		/*Delete unchanged props*/
		if(!empty($arProps))
		{
			$arOldProps = array();
			$dbRes = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array(), Array("ID"=>array_keys($arProps)));
			while($arr = $dbRes->Fetch())
			{
				if(isset($arProps[$arr['ID']]))
				{
					if($arr['MULTIPLE']=='Y')
					{
						if(!is_array($arOldProps[$arr['ID']])) $arOldProps[$arr['ID']] = array();
						$arOldProps[$arr['ID']][] = $arr['VALUE'];
					}
					else
					{
						$arOldProps[$arr['ID']] = $arr['VALUE'];
					}
				}
			}
			foreach($arOldProps as $pk=>$pv)
			{
				$fsKey = 'IP_PROP'.$pk;
				$saveOldVals = (bool)($this->fieldSettings[$fsKey]['MULTIPLE_SAVE_OLD_VALUES']=='Y');

				if($propsDef[$pk]['MULTIPLE']=='Y' && $propsDef[$pk]['PROPERTY_TYPE']!='F' && $saveOldVals)
				{
					foreach($arProps[$pk] as $fpk2=>$fpv2)
					{
						foreach($pv as $fpk=>$fpv)
						{
							if($fpv==$fpv2)
							{
								unset($arProps[$pk][$fpk2]);
								break;
							}
						}
					}
					$arProps[$pk] = array_merge($pv, $arProps[$pk]);
					$arProps[$pk] = array_diff($arProps[$pk], array(''));
				}
				
				if($arProps[$pk]==$pv && (is_array($arProps[$pk]) || is_array($pv) || strlen($arProps[$pk])==strlen($pv)))
				{
					unset($arProps[$pk]);
				}
				else
				{
					if($propsDef[$pk]['PROPERTY_TYPE']=='F')
					{
						if($propsDef[$pk]['MULTIPLE']=='Y')
						{
							if($saveOldVals)
							{
								foreach($arProps[$pk] as $fpk2=>$fpv2)
								{
									foreach($pv as $fpk=>$fpv)
									{
										if(!$this->IsChangedImage($fpv, $fpv2))
										{
											unset($arProps[$pk][$fpk2]);
											break;
										}
									}
								}
								$arProps[$pk] = array_merge($pv, $arProps[$pk]);
								foreach($arProps[$pk] as $fpk2=>$fpv2)
								{
									if(is_numeric($fpv2)) $arProps[$pk][$fpk2] = \CFile::MakeFileArray($fpv2);
								}
								$arProps[$pk] = array_diff($arProps[$pk], array(''));
							}
							
							if(count($pv)==count($arProps[$pk]))
							{
								$isChange = false;
								foreach($pv as $fpk=>$fpv)
								{
									if($this->IsChangedImage($fpv, $arProps[$pk][$fpk]))
									{
										$isChange = true;
									}
								}
								if(!$isChange)
								{
									unset($arProps[$pk]);
								}
							}
						}
						else
						{
							if(!$this->IsChangedImage($pv, $arProps[$pk]))
							{
								unset($arProps[$pk]);
							}
						}
					}
				}
			}
		}
		/*/Delete unchanged props*/
		
		if(!empty($arProps))
		{
			\CIBlockElement::SetPropertyValuesEx($ID, $IBLOCK_ID, $arProps);
			$this->logger->AddElementChanges('IP_PROP', $arProps, $arOldProps);
		}
		
		if($needUpdate)
		{
			$el = new \CIblockElement();
			$el->Update($ID, array(), false, true);
		}
	}
	
	public function GetPropValue($arProp, $val)
	{
		$fieldSettings = (isset($this->fieldSettings['OFFER_IP_PROP'.$arProp['ID']]) ? $this->fieldSettings['OFFER_IP_PROP'.$arProp['ID']] : $this->fieldSettings['IP_PROP'.$arProp['ID']]);
		if($arProp['PROPERTY_TYPE']=='F')
		{
			$picSettings = array();
			if($fieldSettings['PICTURE_PROCESSING'])
			{
				$picSettings = $fieldSettings['PICTURE_PROCESSING'];
			}
			$val = $this->GetFileArray($val, $picSettings, $arProp);
		}
		elseif($arProp['PROPERTY_TYPE']=='L')
		{
			if($val)
			{
				if(!isset($this->propVals[$arProp['ID']][$val]))
				{
					$dbRes = \CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID"=>$arProp['ID'], "VALUE"=>$val));
					if($arPropEnum = $dbRes->Fetch())
					{
						$this->propVals[$arProp['ID']][$val] = $arPropEnum['ID'];
					}
					else
					{
						$ibpenum = new \CIBlockPropertyEnum;
						if($propId = $ibpenum->Add(array('PROPERTY_ID'=>$arProp['ID'], 'VALUE'=>$val)))
						{
							$this->propVals[$arProp['ID']][$val] = $propId;
						}
						else
						{
							$this->propVals[$arProp['ID']][$val] = false;
						}
					}
				}
				$val = $this->propVals[$arProp['ID']][$val];
			}
		}
		elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
		{
			$val = $this->GetHighloadBlockValue($arProp, $val, true);
		}
		elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='HTML')
		{
			if($fieldSettings['TEXT_HTML']=='text') $val = array('VALUE'=>array('TEXT'=>$val, 'TYPE'=>'TEXT'));
			elseif($fieldSettings['TEXT_HTML']=='html') $val = array('VALUE'=>array('TEXT'=>$val, 'TYPE'=>'HTML'));
		}
		elseif($arProp['USER_TYPE']=='DateTime')
		{
			$val = $this->GetDateVal($val);
		}
		elseif($arProp['PROPERTY_TYPE']=='E')
		{
			$val = $this->GetIblockElementValue($arProp, $val, $fieldSettings, true);
		}
		elseif($arProp['PROPERTY_TYPE']=='G')
		{
			$relField = $fieldSettings['REL_SECTION_FIELD'];
			if((!$relField || $relField=='ID') && !is_numeric($val))
			{
				$relField = 'NAME';
			}
			if($relField && $relField!='ID' && $val && $arProp['LINK_IBLOCK_ID'])
			{
				$arFilter = array(
					'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
					$relField => $val,
					'CHECK_PERMISSIONS' => 'N'
				);
				$dbRes = \CIblockSection::GetList(array('ID'=>'ASC'), $arFilter, false, array('ID'), array('nTopCount'=>1));
				if($arElem = $dbRes->Fetch()) $val = $arElem['ID'];
				else $val = '';
			}
		}

		return $val;
	}
	
	public function GenerateElementCode(&$arElement, $iblockFields)
	{
		if(($iblockFields['CODE']['IS_REQUIRED']=='Y' || $iblockFields['CODE']['DEFAULT_VALUE']['TRANSLITERATION']=='Y') && strlen($arElement['CODE'])==0 && strlen($arElement['NAME'])>0)
		{
			$arElement['CODE'] = $this->Str2Url($arElement['NAME'], $iblockFields['CODE']['DEFAULT_VALUE']);
			if($iblockFields['CODE']['DEFAULT_VALUE']['UNIQUE']=='Y')
			{
				$i = 0;
				while(($tmpCode = $arElement['CODE'].($i ? '-'.mt_rand() : '')) && \CIblockElement::GetList(array(), array('IBLOCK_ID'=>$arElement['IBLOCK_ID'], 'CODE'=>$tmpCode, 'CHECK_PERMISSIONS' => 'N'), array()) > 0 && ++$i){}
				$arElement['CODE'] = $tmpCode;
			}
		}
	}
	
	public function GetIblockFields($IBLOCK_ID)
	{
		if(!$this->iblockFields[$IBLOCK_ID])
		{
			$this->iblockFields[$IBLOCK_ID] = \CIBlock::GetFields($IBLOCK_ID);
		}
		return $this->iblockFields[$IBLOCK_ID];
	}
	
	public function GetIblockSectionFields($IBLOCK_ID)
	{
		if(!isset($this->iblockSectionFields[$IBLOCK_ID]))
		{
			$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'IBLOCK_'.$IBLOCK_ID.'_SECTION'));
			$arProps = array();
			while($arr = $dbRes->Fetch())
			{
				$arProps[$arr['FIELD_NAME']] = $arr;
			}
			$this->iblockSectionFields[$IBLOCK_ID] = $arProps;
		}
		return $this->iblockSectionFields[$IBLOCK_ID];
	}
	
	public function GetIblockElementValue($arProp, $val, $fsettings, $bAdd = false)
	{
		if(strlen($val)==0) return $val;
		$relField = $fsettings['REL_ELEMENT_FIELD'];
		if((!$relField || $relField=='IE_ID') && !is_numeric($val))
		{
			$relField = 'IE_NAME';
			$bAdd = false;
		}
		if($relField && $relField!='IE_ID' && $arProp['LINK_IBLOCK_ID'])
		{
			$arFilter = array('IBLOCK_ID'=>$arProp['LINK_IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N');
			if(strpos($relField, 'IE_')===0)
			{
				$arFilter[substr($relField, 3)] = $val;
			}
			elseif(strpos($relField, 'IP_PROP')===0)
			{
				$uid = substr($relField, 7);
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$arFilter['PROPERTY_'.$uid.'_VALUE'] = $val;
				}
				else
				{
					/*if($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
					{
						$val = $this->GetHighloadBlockValue($arProp, $val);
					}*/
					$arFilter['PROPERTY_'.$uid] = $val;
				}
			}

			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFilter, false, array('nTopCount'=>1), array('ID'));
			if($arElem = $dbRes->Fetch())
			{
				$val = $arElem['ID'];
			}
			elseif($bAdd && $arFilter['NAME'] && $arFilter['IBLOCK_ID'])
			{
				$iblockFields = $this->GetIblockFields($arFilter['IBLOCK_ID']);
				$this->GenerateElementCode($arFilter, $iblockFields);
				$el = new \CIblockElement();
				$val = $el->Add($arFilter, false, true, true);
			}
		}

		return $val;
	}
	
	public function GetHighloadBlockValue($arProp, $val, $bAdd = false)
	{
		if($val && Loader::includeModule('highloadblock') && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])
		{
			$arFields = $val;
			if(!is_array($arFields))
			{
				$arFields = array('UF_NAME'=>$arFields);
			}

			$arItems = array();
			if(is_array($arFields['UF_NAME']) || is_array($arFields['UF_XML_ID']))
			{
				if(!is_array($arFields['UF_NAME'])) $arFields['UF_NAME'] = array($arFields['UF_NAME']);
				else $arFields['UF_NAME'] = array_values($arFields['UF_NAME']);
				if(!is_array($arFields['UF_XML_ID'])) $arFields['UF_XML_ID'] = array($arFields['UF_XML_ID']);
				else $arFields['UF_XML_ID'] = array_values($arFields['UF_XML_ID']);
				$cnt = max(count($arFields['UF_NAME']), count($arFields['UF_XML_ID']));
				for($i=0; $i<$cnt; $i++)
				{
					$arItem = array();
					foreach($arFields as $k=>$v)
					{
						if(is_array($v) && isset($v[$i])) $arItem[$k] = $v[$i];
						elseif(!is_array($v)) $arItem[$k] = $v;
					}
					$arItems[] = $arItem;
				}
			}
			else
			{
				$arItems[] = $arFields;
			}

			$arResult = array();
			foreach($arItems as $arFields)
			{
				if($arFields['UF_XML_ID']) $cacheKey = 'UF_XML_ID_'.$arFields['UF_XML_ID'];
				else $cacheKey = 'UF_NAME_'.$arFields['UF_NAME'];

				if(!isset($this->propVals[$arProp['ID']][$cacheKey]))
				{
					if(!$this->hlbl[$arProp['ID']] || !$this->hlblFields[$arProp['ID']])
					{
						$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
						if(!$hlblock) continue;
						if(!$this->hlbl[$arProp['ID']])
						{
							$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
							$this->hlbl[$arProp['ID']] = $entity->getDataClass();
						}
						if(!$this->hlblFields[$arProp['ID']])
						{
							$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'HLBLOCK_'.$hlblock['ID']));
							$arHLFields = array();
							while($arHLField = $dbRes->Fetch())
							{
								$arHLFields[$arHLField['FIELD_NAME']] = $arHLField;
							}
							$this->hlblFields[$arProp['ID']] = $arHLFields;
						}
					}
					$entityDataClass = $this->hlbl[$arProp['ID']];
					$arHLFields = $this->hlblFields[$arProp['ID']];
					
					if((!isset($arFields['UF_NAME']) || strlen(trim($arFields['UF_NAME']))==0) && (!isset($arFields['UF_XML_ID']) || strlen(trim($arFields['UF_XML_ID']))==0)) continue;
					$this->PrepareHighLoadBlockFields($arFields, $arHLFields);
					
					if($arFields['UF_XML_ID']) $arFilter = array("UF_XML_ID"=>$arFields['UF_XML_ID']);
					else $arFilter = array("UF_NAME"=>$arFields['UF_NAME']);
					$dbRes2 = $entityDataClass::GetList(array('filter'=>$arFilter, 'select'=>array('ID', 'UF_XML_ID'), 'limit'=>1));
					if($arr2 = $dbRes2->Fetch())
					{
						if(count($arFields) > 1 && $bAdd)
						{
							$entityDataClass::Update($arr2['ID'], $arFields);
						}
						$cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = $arr2['UF_XML_ID'];
					}
					else
					{
						if(!isset($arFields['UF_NAME']) || strlen(trim($arFields['UF_NAME']))==0) continue;
						if(!isset($arFields['UF_XML_ID']) || strlen(trim($arFields['UF_XML_ID']))==0) $arFields['UF_XML_ID'] = $this->Str2Url($arFields['UF_NAME']);
						if($bAdd)
						{
							if($entityDataClass::Add($arFields))
								$cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = $arFields['UF_XML_ID'];
							else $cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = false;
						}
						else $cacheVal = $arFields['UF_XML_ID'];
					}
				}
				else
				{
					$cacheVal = $this->propVals[$arProp['ID']][$cacheKey];
				}
				$arResult[] = $cacheVal;
			}

			if(empty($arResult)) return false;
			elseif(count($arResult)==1) return current($arResult);
			else return $arResult;
		}
		return $val;
	}
	
	public function PrepareHighLoadBlockFields(&$arFields, $arHLFields)
	{
		foreach($arFields as $k=>$v)
		{
			if(!isset($arHLFields[$k]))
			{
				unset($arFields[$k]);
			}
			$type = $arHLFields[$k]['USER_TYPE_ID'];
			if($type=='file')
			{
				$arFields[$k] = $this->GetFileArray($v);
			}
			elseif($type=='integer' || $type=='double')
			{
				$arFields[$k] = $this->GetFloatVal($v);
			}
			elseif($type=='datetime')
			{
				$arFields[$k] = $this->GetDateVal($v);
			}
			elseif($type=='date')
			{
				$arFields[$k] = $this->GetDateVal($v, 'PART');
			}
			elseif($type=='boolean')
			{
				$arFields[$k] = $this->GetHLBoolValue($v);
			}
			elseif($type=='hlblock')
			{
				$arFields[$k] = $this->GetHLHLValue($v, $arHLFields[$k]['SETTINGS']);
			}
			if($arHLFields[$k]['MULTIPLE']=='Y' && !is_array($arFields[$k]))
			{
				$arFields[$k] = array($arFields[$k]);
			}
		}		
	}
	
	public function GetHLHLValue($val, $arSettings)
	{
		if(!Loader::includeModule('highloadblock')) return $val;
		$hlblId = $arSettings['HLBLOCK_ID'];
		$fieldId = $arSettings['HLFIELD_ID'];
		if($val && $hlblId && $fieldId)
		{
			if(!is_array($this->hlhlbl)) $this->hlhlbl = array();
			if(!is_array($this->hlhlblFields)) $this->hlhlblFields = array();
			if(!is_array($this->hlPropVals)) $this->hlPropVals = array();

			if(!isset($this->hlPropVals[$fieldId][$val]))
			{
				if(!$this->hlhlbl[$hlblId] || !$this->hlhlblFields[$hlblId])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID'=>$hlblId)))->fetch();
					if(!$this->hlhlbl[$hlblId])
					{
						$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
						$this->hlhlbl[$hlblId] = $entity->getDataClass();
					}
					if(!$this->hlhlblFields[$hlblId])
					{
						$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'HLBLOCK_'.$hlblock['ID']));
						$arHLFields = array();
						while($arHLField = $dbRes->Fetch())
						{
							$arHLFields[$arHLField['ID']] = $arHLField;
						}
						$this->hlhlblFields[$hlblId] = $arHLFields;
					}
				}
				
				$entityDataClass = $this->hlhlbl[$hlblId];
				$arHLFields = $this->hlhlblFields[$hlblId];
				
				if(!$arHLFields[$fieldId]) return false;
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array($arHLFields[$fieldId]['FIELD_NAME']=>$val), 'select'=>array('ID'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					$this->hlPropVals[$fieldId][$val] = $arr2['ID'];
				}
				else
				{
					$arFields = array($arHLFields[$fieldId]['FIELD_NAME']=>$val);
					$dbRes2 = $entityDataClass::Add($arFields);
					$this->hlPropVals[$fieldId][$val] = $dbRes2->GetID();
				}
			}
			return $this->hlPropVals[$fieldId][$val];
		}
		return $val;
	}
	
	public function PictureProcessing($arFile, $arDef)
	{
		if($arDef["SCALE"] === "Y")
		{
			$arNewPicture = \CIBlock::ResizePicture($arFile, $arDef);
			if(is_array($arNewPicture))
			{
				$arFile = $arNewPicture;
			}
			/*elseif($arDef["IGNORE_ERRORS"] !== "Y")
			{
				unset($arFile);
				$strWarning .= Loc::getMessage("IBLOCK_FIELD_PREVIEW_PICTURE").": ".$arNewPicture."<br>";
			}*/
		}

		if($arDef["USE_WATERMARK_FILE"] === "Y")
		{
			\CIBLock::FilterPicture($arFile["tmp_name"], array(
				"name" => "watermark",
				"position" => $arDef["WATERMARK_FILE_POSITION"],
				"type" => "file",
				"size" => "real",
				"alpha_level" => 100 - min(max($arDef["WATERMARK_FILE_ALPHA"], 0), 100),
				"file" => $_SERVER["DOCUMENT_ROOT"].Rel2Abs("/", $arDef["WATERMARK_FILE"]),
			));
		}

		if($arDef["USE_WATERMARK_TEXT"] === "Y")
		{
			\CIBLock::FilterPicture($arFile["tmp_name"], array(
				"name" => "watermark",
				"position" => $arDef["WATERMARK_TEXT_POSITION"],
				"type" => "text",
				"coefficient" => $arDef["WATERMARK_TEXT_SIZE"],
				"text" => $arDef["WATERMARK_TEXT"],
				"font" => $_SERVER["DOCUMENT_ROOT"].Rel2Abs("/", $arDef["WATERMARK_TEXT_FONT"]),
				"color" => $arDef["WATERMARK_TEXT_COLOR"],
			));
		}
		return $arFile;
	}
	
	public function SaveProduct($ID, $arProduct, $arPrices, $arStores, $parentID=false)
	{
		if(!is_array($arProduct))
		{
			$arProduct = array();
		}
		if($parentID && defined('\Bitrix\Catalog\ProductTable::TYPE_OFFER'))
		{
			$arProduct['TYPE'] = \Bitrix\Catalog\ProductTable::TYPE_OFFER;
		}
		$isOffer = (bool)($parentID > 0);
			
		if((!empty($arProduct) || !empty($arPrices) || !empty($arStores)))
		{
			$arProduct['ID'] = $ID;
		}
		
		if(empty($arProduct)) return false;
		
		if(isset($arProduct['VAT_INCLUDED']))
		{
			$arProduct['VAT_INCLUDED'] = $this->GetBoolValue($arProduct['VAT_INCLUDED']);
		}
		if(isset($arProduct['CAN_BUY_ZERO']))
		{
			$arProduct['CAN_BUY_ZERO'] = $this->GetBoolValue($arProduct['CAN_BUY_ZERO']);
		}
		if(isset($arProduct['NEGATIVE_AMOUNT_TRACE']))
		{
			$arProduct['NEGATIVE_AMOUNT_TRACE'] = $this->GetBoolValue($arProduct['NEGATIVE_AMOUNT_TRACE']);
		}
		if(isset($arProduct['SUBSCRIBE']))
		{
			$arProduct['SUBSCRIBE'] = $this->GetBoolValue($arProduct['SUBSCRIBE']);
		}
		
		if(isset($arProduct['QUANTITY_TRACE']))
		{
			$arProduct['QUANTITY_TRACE'] = $this->GetBoolValue($arProduct['QUANTITY_TRACE']);
		}
		elseif($this->params['QUANTITY_TRACE']=='Y')
		{
			$arProduct['QUANTITY_TRACE'] = 'Y';
		}
		
		if(isset($arProduct['QUANTITY'])) $arProduct['QUANTITY'] = $this->GetFloatVal($arProduct['QUANTITY']);
		if(isset($arProduct['VAT_INCLUDED'])) $arProduct['VAT_INCLUDED'] = $this->GetBoolValue($arProduct['VAT_INCLUDED']);
		if(isset($arProduct['PURCHASING_PRICE']))
		{
			if(!isset($arProduct['PURCHASING_CURRENCY']) || (isset($arProduct['PURCHASING_CURRENCY']) && !trim($arProduct['PURCHASING_CURRENCY'])))
			{
				$arProduct['PURCHASING_CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
			}
			$arProduct['PURCHASING_CURRENCY'] = $this->GetCurrencyVal($arProduct['PURCHASING_CURRENCY']);
		}
		
		if(isset($arProduct['PURCHASING_PRICE']) && $arProduct['PURCHASING_PRICE']!=='')
		{
			$pKey = ($isOffer ? 'OFFER_' : '').'ICAT_PURCHASING_PRICE';
			$arProduct['PURCHASING_PRICE'] = $this->ApplyMargins($arProduct['PURCHASING_PRICE'], $pKey);
		}
		
		if(isset($arProduct['MEASURE_RATIO']))
		{
			$arProductMeasureRatio = array(
				'RATIO' => $arProduct['MEASURE_RATIO'],
				'PRODUCT_ID' => $ID,
				'IS_DEFAULT' => 'Y'
			);
			unset($arProduct['MEASURE_RATIO']);
			$dbRes = \CCatalogMeasureRatio::getList(array(), array('PRODUCT_ID' => $arProductMeasureRatio['PRODUCT_ID'], 'IS_DEFAULT'=>''), false, false, array_merge(array('ID'), array_keys($arProductMeasureRatio)));
			$cntRes = $dbRes->SelectedRowsCount();
			while(($cntRes > 1) && ($arRatio = $dbRes->Fetch()))
			{
				\CCatalogMeasureRatio::delete($arRatio['ID']);
				$cntRes--;
			}
			if($arRatio = $dbRes->Fetch())
			{
				foreach($arRatio as $k=>$v)
				{
					if($v==$arProductMeasureRatio[$k])
					{
						unset($arProductMeasureRatio[$k]);
					}
				}
				if(!empty($arProductMeasureRatio))
				{
					\CCatalogMeasureRatio::update($arRatio['ID'], $arProductMeasureRatio);
				}
			}
			else
			{
				\CCatalogMeasureRatio::add($arProductMeasureRatio);
			}
		}
		
		if(isset($arProduct['BARCODE']))
		{
			if(!is_array($arProduct['BARCODE'])) $arProduct['BARCODE'] = explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arProduct['BARCODE']);
			$arProduct['BARCODE'] = array_unique($arProduct['BARCODE']);
			$dbRes = \CCatalogStoreBarCode::getList(array(), array('PRODUCT_ID' => $ID), false, false, array('ID', 'BARCODE'));
			$arBarcodesDB = array();
			while($arr = $dbRes->Fetch())
			{
				if(in_array($arr['BARCODE'], $arProduct['BARCODE']))
				{
					unset($arProduct['BARCODE'][array_search($arr['BARCODE'], $arProduct['BARCODE'])]);
				}
				else
				{
					$arBarcodesDB[] = $arr['ID'];
				}
			}
			
			if(!empty($arBarcodesDB))
			{
				foreach($arBarcodesDB as $bid)
				{
					if(!empty($arProduct['BARCODE']))
					{
						$barcode = array_shift($arProduct['BARCODE']);
						\CCatalogStoreBarCode::Update($bid, array(
							'BARCODE' => $barcode,
							'STORE_ID' => 0,
							'ORDER_ID' => false
						));
					}
					else
					{
						\CCatalogStoreBarCode::Delete($bid);
					}
				}
			}
			
			if(!empty($arProduct['BARCODE']))
			{
				foreach($arProduct['BARCODE'] as $barcode)
				{
					$arProductBarcode = array(
						'BARCODE' => $barcode,
						'PRODUCT_ID' => $ID
					);
					\CCatalogStoreBarCode::add($arProductBarcode);
				}
			}
			unset($arProduct['BARCODE']);
		}
		
		if(isset($arProduct['VAT_ID']))
		{
			$vatName = ToLower($arProduct['VAT_ID']);
			if(!isset($this->catalogVats)) $this->catalogVats = array();
			if(!isset($this->catalogVats[$vatName]))
			{
				$dbRes = \CCatalogVat::GetList(array(), array('NAME'=>$arProduct['VAT_ID']), array('ID'));
				$arr = $dbRes->Fetch();
				if(!$arr && is_numeric($arProduct['VAT_ID']))
				{
					$dbRes = \CCatalogVat::GetList(array(), array('RATE'=>$arProduct['VAT_ID']), array('ID'));
					$arr = $dbRes->Fetch();					
				}
				if($arr)
				{
					$this->catalogVats[$vatName] = $arr['ID'];
				}
				else
				{
					$this->catalogVats[$vatName] = false;
				}
			}
			$arProduct['VAT_ID'] = $this->catalogVats[$vatName];
		}
		
		$arSet = array();
		if(isset($arProduct['SET_ITEM_ID']))
		{
			$arSetKeys = preg_grep('/^SET_/', array_keys($arProduct));
			foreach($arSetKeys as $setKey)
			{
				$arSet[substr($setKey, 4)] = $arProduct[$setKey];
				unset($arProduct[$setKey]);
			}
		}
		
		$arSet2 = array();
		if(isset($arProduct['SET2_ITEM_ID']))
		{
			$arSetKeys = preg_grep('/^SET2_/', array_keys($arProduct));
			foreach($arSetKeys as $setKey)
			{
				$arSet2[substr($setKey, 5)] = $arProduct[$setKey];
				unset($arProduct[$setKey]);
			}
		}
		
		$productChange = false;
		$dbRes = \CCatalogProduct::GetList(array(), array('ID'=>$ID), false, false, array_keys($arProduct));
		while($arCProduct = $dbRes->Fetch())
		{
			/*Delete unchanged data*/
			foreach($arProduct as $k=>$v)
			{
				if($v==$arCProduct[$k])
				{
					unset($arProduct[$k]);
				}
			}
			/*/Delete unchanged data*/
			if(!empty($arProduct))
			{
				\CCatalogProduct::Update($arCProduct['ID'], $arProduct);
				$this->logger->AddElementChanges('ICAT_', $arProduct, $arCProduct);
				$productChange = true;
			}
		}
		
		if($dbRes->SelectedRowsCount()==0)
		{
			\CCatalogProduct::Add($arProduct);
			$this->logger->AddElementChanges('ICAT_', $arProduct);
			$productChange = true;
		}
		
		if(!empty($arPrices))
		{
			$this->SavePrice($ID, $arPrices, $isOffer);
		}
		
		if(!empty($arStores))
		{
			$this->SaveStore($ID, $arStores);
		}
		
		if(!empty($arSet))
		{
			$this->SaveCatalogSet($ID, $arSet, \CCatalogProductSet::TYPE_GROUP);
		}
		
		if(!empty($arSet2))
		{
			$this->SaveCatalogSet($ID, $arSet2, \CCatalogProductSet::TYPE_SET);
		}
		
		/*Update offer parent*/
		if($parentID && $productChange)
		{
			if(class_exists('\Bitrix\Catalog\Product\Sku'))
			{
				\Bitrix\Catalog\Product\Sku::updateAvailable($parentID);
			}
		}
		/*/Update offer parent*/
	}
	
	public function SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false)
	{
		if(!isset($arFieldsProductDiscount['VALUE']) && !isset($arFieldsProductDiscount['BRGIFT'])) return;
		$brgift = false;
		if(isset($arFieldsProductDiscount['BRGIFT']) && Loader::includeModule('sale'))
		{
			$brgift = $arFieldsProductDiscount['BRGIFT'];
			unset($arFieldsProductDiscount['BRGIFT']);
		}
		
		$onlyVal = (bool)(count(array_diff(array_keys($arFieldsProductDiscount), array('VALUE', 'VALUE_TYPE')))==0);
		$arSites = $this->GetIblockSite($IBLOCK_ID);
		foreach($arSites as $siteId)
		{
			$this->SaveDiscountGift($ID, $IBLOCK_ID, $brgift, $siteId, $name, $isOffer);
			if(!isset($arFieldsProductDiscount['VALUE'])) continue;
			
			$arFieldsProductDiscount['SITE_ID'] = $siteId;
			$arFieldsProductDiscount['VALUE'] = $this->GetFloatVal($arFieldsProductDiscount['VALUE']);
			$xmlIdProductDiscount = 'PRODUCT_'.$ID.'_'.$arFieldsProductDiscount['SITE_ID'];
			
			if($onlyVal)
			{
				$arFieldsProductDiscount['XML_ID'] = 'IMPORT_'.$arFieldsProductDiscount['VALUE_TYPE'].'_'.$arFieldsProductDiscount['VALUE'].'_'.$arFieldsProductDiscount['SITE_ID'];
				
				$dbRes = \CCatalogDiscount::GetList(array(), array('XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID', 'CONDITIONS'));
				while($arDiscount = $dbRes->Fetch())
				{
					$arCond = unserialize($arDiscount['CONDITIONS']);
					$childrenKey = -1;
					if(is_array($arCond['CHILDREN']))
					{
						foreach($arCond['CHILDREN'] as $k=>$v)
						{
							if($v['CLASS_ID']=='CondIBElement' && ToLower($v['DATA']['logic'])=='equal')
							{
								$childrenKey = $k;
							}
						}
					}
					
					if($childrenKey >= 0)
					{
						$val = $arCond['CHILDREN'][$childrenKey]['DATA']['value'];
						if(!is_array($val)) $val = array($val);
						if(!in_array($ID, $val)) $val[] = $ID;
						$arCond['CHILDREN'][$childrenKey]['DATA']['value'] = $val;
					}
					else
					{
						$arCond = $this->GetDiscountProductCond($ID);
					}
					$arFieldsProductDiscount['CONDITIONS'] = $arCond;
					
					\CCatalogDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount);
				}
				
				if($dbRes->SelectedRowsCount()==0)
				{
					$arFieldsProductDiscount['CONDITIONS'] = $this->GetDiscountProductCond($ID);
					if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
					if(!$arFieldsProductDiscount['NAME'])
					{
						if($arFieldsProductDiscount['VALUE_TYPE']=='F') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_F").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
						elseif($arFieldsProductDiscount['VALUE_TYPE']=='S') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_S").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
						else $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_P").' '. $arFieldsProductDiscount['VALUE'].'%';
					}
					\CCatalogDiscount::Add($arFieldsProductDiscount);
				}
				
				//Delete old discount
				$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, 'XML_ID'=>$xmlIdProductDiscount), false, false, array('ID'));
				while($arDiscount = $dbRes->Fetch())
				{
					\CCatalogDiscount::Delete($arDiscount['ID']);
				}
				$this->DeleteProductDiscount($ID, $arFieldsProductDiscount);
			}
			else
			{
				$arFieldsProductDiscount['XML_ID'] = $xmlIdProductDiscount;
				if($arFieldsProductDiscount['ACTIVE_FROM']) $arFieldsProductDiscount['ACTIVE_FROM'] = $this->GetDateVal($arFieldsProductDiscount['ACTIVE_FROM']);
				if($arFieldsProductDiscount['ACTIVE_TO']) $arFieldsProductDiscount['ACTIVE_TO'] = $this->GetDateVal($arFieldsProductDiscount['ACTIVE_TO']);
				if(isset($arFieldsProductDiscount['RENEWAL'])) $arFieldsProductDiscount['RENEWAL'] = $this->GetBoolValue($arFieldsProductDiscount['RENEWAL']);
				if(isset($arFieldsProductDiscount['LAST_DISCOUNT'])) $arFieldsProductDiscount['LAST_DISCOUNT'] = $this->GetBoolValue($arFieldsProductDiscount['LAST_DISCOUNT']);
				$arFieldsProductDiscount['CONDITIONS'] = $this->GetDiscountProductCond($ID);
				
				$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, 'XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID'));
				while($arDiscount = $dbRes->Fetch())
				{
					if((float)$arFieldsProductDiscount['VALUE'] > 0) $arFieldsProductDiscount['ACTIVE'] = 'Y';
					else 
					{
						unset($arFieldsProductDiscount['VALUE']);
						$arFieldsProductDiscount['ACTIVE'] = 'N';
					}
					\CCatalogDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount);
				}
				
				if($dbRes->SelectedRowsCount()==0)
				{
					if(!$arFieldsProductDiscount['NAME']) $arFieldsProductDiscount['NAME'] = $name;
					if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
					\CCatalogDiscount::Add($arFieldsProductDiscount);
				}
				$this->DeleteProductDiscount($ID, $arFieldsProductDiscount, true);
			}
		}
	}
	
	public function GetDiscountProductCond($ID)
	{
		$arCond = \CCatalogCondTree::GetDefaultConditions();
		$arCond['CHILDREN'][] = array(
			'CLASS_ID' => 'CondIBElement',
			'DATA' => array(
				'logic' => 'Equal',
				'value' => $ID
			)
		);
		return $arCond;
	}
	
	public function DeleteProductDiscount($ID, $arFieldsProductDiscount, $all=false)
	{
		$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, '%XML_ID'=>'IMPORT_'), false, false, array('ID', 'VALUE', 'VALUE_TYPE', 'CONDITIONS'));
		while($arDiscount = $dbRes->Fetch())
		{
			if(!$all && $arFieldsProductDiscount['VALUE_TYPE']==$arDiscount['VALUE_TYPE'] && $arFieldsProductDiscount['VALUE']==$arDiscount['VALUE']) continue;
			$arCond = unserialize($arDiscount['CONDITIONS']);
			if(is_array($arCond['CHILDREN']))
			{
				foreach($arCond['CHILDREN'] as $k=>$v)
				{
					if($v['CLASS_ID']=='CondIBElement' && ToLower($v['DATA']['logic'])=='equal')
					{
						$val = $arCond['CHILDREN'][$k]['DATA']['value'];
						if(!is_array($val)) $val = array($val);
						$val = array_diff($val, array($ID));
						if(!empty($val)) $arCond['CHILDREN'][$k]['DATA']['value'] = $val;
						else unset($arCond['CHILDREN'][$k]);
					}
				}
			}
			if(empty($arCond['CHILDREN'])) \CCatalogDiscount::Delete($arDiscount['ID']);
			else \CCatalogDiscount::Update($arDiscount['ID'], array('CONDITIONS'=>$arCond));
		}
	}
	
	public function SaveDiscountGift($ID, $IBLOCK_ID, $giftId, $siteId, $name, $isOffer = false)
	{
		if($giftId===false || strlen($giftId)==0) return;

		$relField = $this->fieldSettings[($isOffer ? 'OFFER_' : '').'ICAT_DISCOUNT_BRGIFT']['REL_ELEMENT_FIELD'];
		if($relField && $relField!='IE_ID')
		{
			$arFilter = array('IBLOCK_ID'=>$IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N');
			if(strpos($relField, 'IE_')===0)
			{
				$key = substr($relField, 3);
				$arFilter[$key] = $giftId;
			}
			elseif(strpos($relField, 'IP_PROP')===0)
			{
				$key = substr($relField, 7);
				$arFilter['PROPERTY_'.$key] = $giftId;
			}
			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFilter, false, array('nTopCount'=>1), array('ID'));
			if($arElem = $dbRes->Fetch())
			{
				$giftId = $arElem['ID'];
			}
		}
		$giftId = (int)$giftId;
		
		if($giftId > 0)
		{
			$arDiscountFields = array(
				'ACTIVE' => 'Y',
				'XML_ID' => 'GIFT_'.$ID.'_'.$siteId,
				'CONDITIONS' => array(
					'CLASS_ID' => 'CondGroup',
					'DATA' => array(
							'All' => 'AND',
							'True' => 'True'
						),
					'CHILDREN' => array(
						0 => array(
							'CLASS_ID' => 'CondBsktProductGroup',
							'DATA' => array(
								'Found' => 'Found',
								'All' => 'AND'
							),
							'CHILDREN' => array(
								0 => array(
									'CLASS_ID' => 'CondIBElement',
									'DATA' => array(
										'logic' => 'Equal',
										'value' => $ID,
									)
								)
							)
						)
					)
				),
				'ACTIONS' => array(
					'CLASS_ID' => 'CondGroup',
					'DATA' => array(
							'All' => 'AND'
						),
					'CHILDREN' => array(
						0 => array(
							'CLASS_ID' => 'GiftCondGroup',
							'DATA' => array(
								'All' => 'AND'
							),
							'CHILDREN' => array(
								0 => array(
									'CLASS_ID' => 'GifterCondIBElement',
									'DATA' => array(
										'Type' => 'one',
										'Value' => $giftId
									)
								)
							)
						)
					)
				)
			);
			
			$dbRes = \CSaleDiscount::GetList(array(), array('XML_ID'=>$arDiscountFields['XML_ID']), false, false, array('ID'));
			while($arDiscount = $dbRes->Fetch())
			{
				\CSaleDiscount::Update($arDiscount['ID'], $arDiscountFields);
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arDiscountFields['LID'] = $siteId;
				$arDiscountFields['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_PRODUCT_GIFT").' '.$name;
				$arDiscountFields['PRIORITY'] = 1;
				$arDiscountFields['LAST_DISCOUNT'] = 'Y';
				$arDiscountFields['USER_GROUPS'] = array(2);
				\CSaleDiscount::Add($arDiscountFields);
			}
		}
	}
	
	public function GetIblockSite($IBLOCK_ID)
	{
		if(!isset($this->arIblockSites)) $this->arIblockSites = array();
		if(!$this->arIblockSites[$IBLOCK_ID])
		{
			$arSites = array();
			$dbRes = \CIBlock::GetSite($IBLOCK_ID);
			while($arSite = $dbRes->Fetch())
			{
				$arSites[] = $arSite['SITE_ID'];
			}
			$this->arIblockSites[$IBLOCK_ID] = $arSites;
		}
		return $this->arIblockSites[$IBLOCK_ID];
	}
	
	public function SavePrice($ID, $arPrices, $isOffer = false)
	{
		$basePriceId = $this->GetBasePriceId();
		if(count($arPrices) > 1 && isset($arPrices[$basePriceId]))
		{
			$arPricesOld = $arPrices;
			$arPrices = array($basePriceId => $arPricesOld[$basePriceId]);
			foreach($arPricesOld as $gid=>$arFieldsPrice)
			{
				if($gid!=$basePriceId)
				{
					$arPrices[$gid] = $arFieldsPrice;
				}
			}
		}
		
		foreach($arPrices as $gid=>$arFieldsPrice)
		{
			$arFieldsPriceExtra = array();
			foreach($arFieldsPrice as $k=>$v)
			{
				if(strpos($k, 'EXTRA')===0)
				{
					if($k=='EXTRA') $arFieldsPriceExtra['PERCENTAGE'] = $v;
					else $arFieldsPriceExtra[substr($k, 6)] = $v;
				}
			}
			if(!empty($arFieldsPriceExtra))
			{
				$arFilter = array();
				if($arFieldsPriceExtra['ID']) $arFilter = array('ID' => $arFieldsPriceExtra['ID']);
				else
				{
					if(!$arFieldsPriceExtra['NAME'] && $arFieldsPriceExtra['PERCENTAGE']) $arFieldsPriceExtra['NAME'] = $arFieldsPriceExtra['PERCENTAGE'].'%';
					if($arFieldsPriceExtra['NAME']) $arFilter = array('NAME' => $arFieldsPriceExtra['NAME']);
				}	
				if(!empty($arFilter))
				{
					if(!isset($this->arPriceExtras)) $this->arPriceExtras = array();
					$dbRes = \CExtra::GetList(array(), $arFilter, false, array('nTopCount'=>1), array('ID'));
					if($arExtra = $dbRes->Fetch())
					{
						if(count($arFieldsPriceExtra) > 0)
						{
							\CExtra::Update($arExtra['ID'], $arFieldsPriceExtra);
						}
						$arFieldsPrice['EXTRA_ID'] = $this->arPriceExtras[$arFieldsPrice['EXTRA']] = $arExtra['ID'];
					}
					else
					{
						$pid = \CExtra::Add($arFieldsPriceExtra);
						if($pid > 0)
						{
							$arFieldsPrice['EXTRA_ID'] = $this->arPriceExtras[$arFieldsPrice['EXTRA']] = $pid;
						}
					}
				}
			}
			
			$extKeys = preg_grep('/^PRICE\|.*QUANTITY_/', array_keys($arFieldsPrice));
			if((!isset($arFieldsPrice['PRICE']) || $arFieldsPrice['PRICE']==='') && (!isset($arFieldsPrice['EXTRA_ID'])) && empty($extKeys)) continue;
			if(!in_array($arFieldsPrice['PRICE'], array('', '-')))
			{
				$pKey = ($isOffer ? 'OFFER_' : '').'ICAT_PRICE'.$gid.'_PRICE';
				$arFieldsPrice['PRICE'] = $this->ApplyMargins($arFieldsPrice['PRICE'], $pKey);
			}
			$recalcPrice = (bool)($gid!=$basePriceId && isset($arFieldsPrice['EXTRA_ID']));
			if(!$arFieldsPrice['CURRENCY'])
			{
				$arFieldsPrice['CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
			}
			$arFieldsPrice['CURRENCY'] = $this->GetCurrencyVal($arFieldsPrice['CURRENCY']);
			
			$arSubPrices = array();
			if(!empty($extKeys))
			{
				foreach($extKeys as $extKey)
				{
					$arPriceKeys = explode('|', $extKey);
					$arSubPrice = array(array_shift($arPriceKeys) => $arFieldsPrice[$extKey]);
					foreach($arPriceKeys as $v)
					{
						$arVal = explode('=', $v);
						$arSubPrice[$arVal[0]] = $arVal[1];
					}
					$arSubPrices[] = $arSubPrice;
					unset($arFieldsPrice[$extKey]);
				}
			}
			if(isset($arFieldsPrice['PRICE'])) $arSubPrices[] = array('PRICE' => $arFieldsPrice['PRICE']);
			elseif(isset($arFieldsPrice['EXTRA_ID'])) $arSubPrices[] = array('EXTRA_ID' => $arFieldsPrice['EXTRA_ID']);
			
			$arFieldsPriceOrig = $arFieldsPrice;
			$arUpdatedIds = array();
			$bDeleteOld = true;
			foreach($arSubPrices as $arSubPrice)
			{
				$arFieldsPrice = array_merge($arFieldsPriceOrig, $arSubPrice);
				if(!isset($arFieldsPrice['QUANTITY_FROM'])) $arFieldsPrice['QUANTITY_FROM'] = false;
				if(!isset($arFieldsPrice['QUANTITY_TO'])) $arFieldsPrice['QUANTITY_TO'] = false;
				
				$arKeys = array_merge(array('ID'), array_keys($arFieldsPrice));
				$dbRes = \CPrice::GetList(array('ID'=>'ASC'), array('PRODUCT_ID'=>$ID, 'CATALOG_GROUP_ID'=>$gid, '!ID' => $arUpdatedIds), false, false, $arKeys);
				if($arPrice = $dbRes->Fetch())
				{
					if($arFieldsPrice['PRICE']!=='-')
					{
						if($recalcPrice)
						{
							$arFieldsPrice['PRODUCT_ID'] = $ID;
							$arFieldsPrice['CATALOG_GROUP_ID'] = $gid;
						}
						else
						{
							/*Delete unchanged data*/
							foreach($arFieldsPrice as $k=>$v)
							{
								if($v==$arPrice[$k])
								{
									unset($arFieldsPrice[$k]);
								}
							}
							/*/Delete unchanged data*/
						}
						if(!empty($arFieldsPrice))
						{
							\CPrice::Update($arPrice["ID"], $arFieldsPrice, $recalcPrice);
							$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice, $arPrice);
						}
					}
					else
					{
						\CPrice::Delete($arPrice["ID"]);
						$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice, $arPrice);
					}
					$arUpdatedIds[] = $arPrice["ID"];
				}
				
				if($dbRes->SelectedRowsCount()==0)
				{
					$bDeleteOld = false;
					if($arFieldsPrice['PRICE']!=='-')
					{
						$arFieldsPrice['PRODUCT_ID'] = $ID;
						$arFieldsPrice['CATALOG_GROUP_ID'] = $gid;
						$priceId = \CPrice::Add($arFieldsPrice, $recalcPrice);
						$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice);
						if($priceId) $arUpdatedIds[] = $priceId;
					}
				}
			}
			
			if($bDeleteOld)
			{
				$dbRes = \CPrice::GetList(array('ID'=>'ASC'), array('PRODUCT_ID'=>$ID, 'CATALOG_GROUP_ID'=>$gid, '!ID' => $arUpdatedIds), false, false, array('ID'));
				while($arPrice = $dbRes->Fetch())
				{
					\CPrice::Delete($arPrice["ID"]);
				}
			}
		}
	}
	
	public function SaveStore($ID, $arStores)
	{
		foreach($arStores as $sid=>$arFieldsStore)
		{
			if(isset($arFieldsStore['AMOUNT'])) $arFieldsStore['AMOUNT'] = $this->GetFloatVal($arFieldsStore['AMOUNT']);
			$dbRes = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$ID, 'STORE_ID'=>$sid), false, false, array_merge(array('ID'), (is_array($arFieldsStore) ? array_keys($arFieldsStore) : array())));
			while($arPrice = $dbRes->Fetch())
			{
				/*Delete unchanged data*/
				foreach($arFieldsStore as $k=>$v)
				{
					if($v==$arPrice[$k])
					{
						unset($arFieldsStore[$k]);
					}
				}
				/*/Delete unchanged data*/
				if(!empty($arFieldsStore))
				{
					\CCatalogStoreProduct::Update($arPrice["ID"], $arFieldsStore);
					$this->logger->AddElementChanges("ICAT_STORE".$sid."_", $arFieldsStore, $arPrice);
				}
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arFieldsStore['PRODUCT_ID'] = $ID;
				$arFieldsStore['STORE_ID'] = $sid;
				\CCatalogStoreProduct::Add($arFieldsStore);
				$this->logger->AddElementChanges("ICAT_STORE".$sid."_", $arFieldsStore);
			}
		}
	}
	
	public function SaveCatalogSet($ID, $arSet, $setType)
	{
		if($setType==\CCatalogProductSet::TYPE_GROUP) $fieldPrefix = 'ICAT_SET_';
		else $fieldPrefix = 'ICAT_SET2_';
		
		$arItems = array();
		foreach($arSet as $k=>$v)
		{
			$fieldSettings = $this->fieldSettings[$fieldPrefix.$k];
			if(!is_array($fieldSettings)) $fieldSettings = array();
			$sep = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
			if($fieldSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y') $sep = $fieldSettings['MULTIPLE_SEPARATOR'];
			$arVals = array_map('trim', explode($sep, $v));
			foreach($arVals as $k2=>$v2)
			{
				if(strlen($v2) > 0)
				{
					if($k=='ITEM_ID')
					{
						$arProp = array('LINK_IBLOCK_ID' => $this->iblockId);
						$v2 = $this->GetIblockElementValue($arProp, $v2, $fieldSettings);
					}
					$arItems[$k2][$k] = $v2;
				}
			}
		}
		$arItemIds = array();
		foreach($arItems as $k=>$v)
		{
			if(!isset($arItems[$k]['QUANTITY'])) $arItems[$k]['QUANTITY'] = 1;
			$arItems[$k]['QUANTITY'] = (int)$arItems[$k]['QUANTITY'];
			if($arItems[$k]['QUANTITY'] < 1) $arItems[$k]['QUANTITY'] = 1;
			
			$key = (isset($arItemIds[$arItems[$k]['ITEM_ID']]) ? $arItemIds[$arItems[$k]['ITEM_ID']] : false);
			if(!isset($arItems[$k]['ITEM_ID']) || $key!==false)
			{
				if($key!==false)
				{
					$arItems[$key]['QUANTITY'] += $arItems[$k]['QUANTITY'];
				}
				unset($arItems[$k]);
				continue;
			}
			$arItemIds[$arItems[$k]['ITEM_ID']] = $k;
		}
	
		$obSet = new \CCatalogProductSet;
		if(\CCatalogProductSet::isProductHaveSet($ID, $setType))
		{
			$arSets = \CCatalogProductSet::getAllSetsByProduct($ID, $setType);

			while(count($arSets) > 1)
			{
				$set = array_pop($arSets);
				$obSet->delete($set['SET_ID']);
			}
			
			$set = array_pop($arSets);
			if(empty($arItems))
			{
				$obSet->delete($set['SET_ID']);
			}
			else
			{
				$set['ITEMS'] = $arItems;
				$obSet->update($set['SET_ID'], $set);
			}
		}
		elseif(!empty($arItems))
		{
			$arFields = array(
				'TYPE' => $setType,
				'ITEM_ID' => $ID,
				'ITEMS' => $arItems
			);
			$obSet = new \CCatalogProductSet;
			$obSet->Add($arFields);
		}
	}
	
	public function GetMeasureByStr($val)
	{
		if(!$val) return $val;
		if(!isset($this->measureList) || !is_array($this->measureList))
		{
			$this->measureList = array();
			$dbRes = \CCatalogMeasure::getList(array(), array());
			while($arr = $dbRes->Fetch())
			{
				$this->measureList[$arr['ID']] = array_map('ToLower', $arr);
			}
		}
		$valCmp = trim(ToLower($val));
		foreach($this->measureList as $k=>$v)
		{
			if(in_array($valCmp, array($v['MEASURE_TITLE'], $v['SYMBOL_RUS'], $v['SYMBOL_INTL'], $v['SYMBOL_LETTER_INTL'])))
			{
				return $k;
			}
		}
	}
	
	public function GetCurrencyRates()
	{
		if(!isset($this->currencyRates))
		{
			$arRates = array();
			$currFile = $this->tmpdir.'/currencies.txt';
			if(file_exists($currFile))
			{
				$arRates = unserialize(file_get_contents($currFile));
			}
			else
			{
				$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>20));
				$res = $client->get('http://www.cbr.ru/scripts/XML_daily.asp');
				if($res)
				{
					$xml = simplexml_load_string($res);
					if($xml->Valute)
					{
						foreach($xml->Valute as $val)
						{
							$arRates[(string)$val->CharCode] = $this->GetFloatVal((string)$val->Value);
						}
					}
				}
				file_put_contents($currFile, serialize($arRates));
			}
			$this->currencyRates = $arRates;
		}
		return $this->currencyRates;
	}
	
	public function ConversionReplaceValues($m)
	{
		if(preg_match('/^\{(([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*)\}$/', $m[0], $m2))
		{
			return $this->GetValueByXpath($m2[1]);
		}
		elseif(in_array($m[0], $this->rcurrencies))
		{
			$arRates = $this->GetCurrencyRates();
			$k = trim($m[0], '#');
			return (isset($arRates[$k]) ? floatval($arRates[$k]) : 1);
		}
	}
	
	public function GetValueByXpath($xpath, $simpleXmlObj=null)
	{
		if(preg_match('/^\d+$/', $xpath) && isset($this->currentItemValues[$xpath]))
		{
			return $this->currentItemValues[$xpath];
		}
		if(preg_match('/^[\d,]*$/', $xpath))
		{
			return '{'.$xpath.'}';
		}
		
		$val = '';
		
		/*if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
		else $arPath = array();
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}*/
		$arXPath = $this->GetXPathParts($xpath);
		$curXpath2 = $arXPath['xpath'];
		$subXpath = $arXPath['subpath'];
		$attr = $arXPath['attr'];
		$currentXmlObj = $this->currentXmlObj;
		if(isset($simpleXmlObj)) $currentXmlObj = $simpleXmlObj;
		
		if(strlen($curXpath2) > 0)
		{
			$curXpath = '/'.ltrim($curXpath2, '/');
			if(isset($this->parentXpath) && strlen($this->parentXpath) > 0 && strpos($curXpath, $this->parentXpath)===0)
			{
				$tmpXpath = substr($curXpath, strlen($this->parentXpath) + 1);
				//$tmpXmlObj = $currentXmlObj->xpath($tmpXpath);
				$tmpXmlObj = $this->Xpath($currentXmlObj, $tmpXpath);
				if(!empty($tmpXmlObj))
				{
					$currentXmlObj = $tmpXmlObj;
					$curXpath = '';
				}
			}
			if(strlen($curXpath) > 0)
			{
				if(strpos($curXpath, $this->xpath)===0) $curXpath = substr($curXpath, strlen($this->xpath) + 1);
				elseif(isset($this->xmlPartObjects[$curXpath2]))
				{
					//$currentXmlObj = $this->xmlPartObjects[$curXpath2]->xpath($subXpath);
					$currentXmlObj = $this->Xpath($this->xmlPartObjects[$curXpath2], $subXpath);
					$curXpath = '';
				}
			}
			//if(strlen($curXpath) > 0) $simpleXmlObj2 = $currentXmlObj->xpath($curXpath);
			if(strlen($curXpath) > 0) $simpleXmlObj2 = $this->Xpath($currentXmlObj, ltrim($curXpath, '/'));
			else $simpleXmlObj2 = $currentXmlObj;
			if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
		}
		else $simpleXmlObj2 = $currentXmlObj;
		if(is_array($simpleXmlObj2)) $simpleXmlObj2 = current($simpleXmlObj2);
		
		if($attr!==false)
		{
			if(is_callable(array($simpleXmlObj2, 'attributes')))
			{
				$val = (string)$simpleXmlObj2->attributes()->{$attr};
			}
		}
		else
		{
			$val = (string)$simpleXmlObj2;					
		}
		
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
		}
		
		return $val;
	}
	
	public function Xpath($simpleXmlObj, $xpath)
	{
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$xpath = \Bitrix\Main\Text\Encoding::convertEncoding($xpath, $this->siteEncoding, $this->fileEncoding);
		}
		if(preg_match('/((^|\/)[^\/]+):/', $xpath, $m))
		{
			$nss = $simpleXmlObj->getNamespaces(true);
			$nsKey = $m[1];
			if(isset($nss[$nsKey]))
			{
				$simpleXmlObj->registerXPathNamespace($nsKey, $nss[$nsKey]);
			}
		}
		if(strlen(trim($xpath)) > 0) return $simpleXmlObj->xpath($xpath);
		else return $simpleXmlObj;
	}
	
	public function ApplyConversions($val, $arConv, $arItem, $field=false, $iblockFields=array())
	{
		$fieldName = $fieldKey = false;
		if(!is_array($field))
		{
			$fieldName = $field;
		}
		else
		{
			if($field['NAME']) $fieldName = $field['NAME'];
			if(strlen($field['KEY']) > 0) $fieldKey = $field['KEY'];
		}
		
		if(is_array($arConv))
		{
			$execConv = false;
			$this->currentItemValues = $arItem;
			$prefixPattern = '/(\{([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*\}|'.implode('|', $this->rcurrencies).')/';
			foreach($arConv as $k=>$v)
			{
				$condVal = $val;

				if(preg_match('/^\{(\S*)\}$/', $v['CELL'], $m))
				{
					$condVal = $this->GetValueByXpath($m[1]);
				}

				if(strlen($v['FROM']) > 0) $v['FROM'] = preg_replace_callback($prefixPattern, array($this, 'ConversionReplaceValues'), $v['FROM']);
			
				if($v['CELL']=='ELSE') $v['WHEN'] = '';
				if(($v['CELL']=='ELSE' && !$execConv)
					|| ($v['WHEN']=='EQ' && $condVal==$v['FROM'])
					|| ($v['WHEN']=='NEQ' && $condVal!=$v['FROM'])
					|| ($v['WHEN']=='GT' && $condVal > $v['FROM'])
					|| ($v['WHEN']=='LT' && $condVal < $v['FROM'])
					|| ($v['WHEN']=='GEQ' && $condVal >= $v['FROM'])
					|| ($v['WHEN']=='LEQ' && $condVal <= $v['FROM'])
					|| ($v['WHEN']=='CONTAIN' && strpos($condVal, $v['FROM'])!==false)
					|| ($v['WHEN']=='NOT_CONTAIN' && strpos($condVal, $v['FROM'])===false)
					|| ($v['WHEN']=='REGEXP' && preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='NOT_REGEXP' && !preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='EMPTY' && strlen($condVal)==0)
					|| ($v['WHEN']=='NOT_EMPTY' && strlen($condVal) > 0)
					|| ($v['WHEN']=='ANY'))
				{
					$this->currentFieldKey = $fieldKey;
					if(strlen($v['TO']) > 0) $v['TO'] = preg_replace_callback($prefixPattern, array($this, 'ConversionReplaceValues'), $v['TO']);
					if($v['THEN']=='REPLACE_TO') $val = $v['TO'];
					elseif($v['THEN']=='REMOVE_SUBSTRING' && strlen($v['TO']) > 0) $val = str_replace($v['TO'], '', $val);
					elseif($v['THEN']=='REPLACE_SUBSTRING_TO' && strlen($v['FROM']) > 0) $val = str_replace($v['FROM'], $v['TO'], $val);
					elseif($v['THEN']=='ADD_TO_BEGIN') $val = $v['TO'].$val;
					elseif($v['THEN']=='ADD_TO_END') $val = $val.$v['TO'];
					elseif($v['THEN']=='MATH_ROUND') $val = round($this->GetFloatVal($val));
					elseif($v['THEN']=='MATH_MULTIPLY') $val = $this->GetFloatVal($val) * $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_DIVIDE') $val = $this->GetFloatVal($val) / $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_ADD') $val = $this->GetFloatVal($val) + $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_SUBTRACT') $val = $this->GetFloatVal($val) - $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='NOT_LOAD') $val = false;
					elseif($v['THEN']=='EXPRESSION') $val = $this->ExecuteFilterExpression($val, $v['TO'], '');
					elseif($v['THEN']=='STRIP_TAGS') $val = strip_tags($val);
					elseif($v['THEN']=='CLEAR_TAGS') $val = preg_replace('/<([a-z][a-z0-9:]*)[^>]*(\/?)>/i','<$1$2>', $val);
					elseif($v['THEN']=='TRANSLIT')
					{
						$arParams = array();
						if($fieldName && !empty($iblockFields))
						{
							$paramName = '';
							if($fieldName=='IE_CODE') $paramName = 'CODE';
							if(preg_match('/^ISECT\d+_CODE$/', $fieldName)) $paramName = 'SECTION_CODE';
							if($paramName && $iblockFields[$paramName]['DEFAULT_VALUE']['TRANSLITERATION']=='Y')
							{
								$arParams = $iblockFields['SECTION_CODE']['DEFAULT_VALUE'];
							}
						}
						$val = $this->Str2Url($val, $arParams);
					}
					$execConv = true;
				}
			}
		}
		return $val;
	}
	
	public function GetCachedOfferIblock($IBLOCK_ID)
	{
		if(!$this->iblockoffers || !isset($this->iblockoffers[$IBLOCK_ID]))
		{
			$this->iblockoffers[$IBLOCK_ID] = \Bitrix\EsolImportxml\Utils::GetOfferIblock($IBLOCK_ID, true);
		}
		return $this->iblockoffers[$IBLOCK_ID];
	}
	
	public function GetBasePriceId()
	{
		if(!$this->catalogBasePriceId)
		{
			$arBasePrice = \CCatalogGroup::GetBaseGroup();
			$this->catalogBasePriceId = $arBasePrice['ID'];
		}
		return $this->catalogBasePriceId;
	}
	
	public function IsChangedImage($fileId, $arNewFile)
	{
		if($this->params['ELEMENT_IMAGES_FORCE_UPDATE']=='Y' || !$fileId) return true;
		$arFile = \CFile::GetFileArray($fileId);
		$arNewFileVal = $arNewFile;
		if(isset($arNewFileVal['VALUE'])) $arNewFileVal = $arNewFileVal['VALUE'];
		if(isset($arNewFileVal['DESCRIPTION'])) $arNewFile['description'] = $arNewFile['DESCRIPTION'];
		list($width, $height, $type, $attr) = getimagesize($arNewFileVal['tmp_name']);
		if(($arFile['EXTERNAL_ID']==$arNewFileVal['external_id']
			|| ($arFile['FILE_SIZE']==$arNewFileVal['size'] 
				&& $arFile['ORIGINAL_NAME']==$arNewFileVal['name'] 
				&& (!$arFile['WIDTH'] || !$arFile['WIDTH'] || ($arFile['WIDTH']==$width && $arFile['HEIGHT']==$height))))
			&& (!isset($arNewFile['description']) || $arNewFile['description']==$arFile['DESCRIPTION']))
		{
			return false;
		}
		return true;
	}
	
	public function GetCurrencyVal($val)
	{
		if(!isset($this->arCurrencies))
		{
			$this->arCurrencies = array();
			if(Loader::includeModule('currency'))
			{
				$dbRes = \CCurrency::GetList(($by="name"), ($order="asc"), LANGUAGE_ID);
				while($arr = $dbRes->Fetch())
				{
					$this->arCurrencies[$arr['CURRENCY']] = array(
						'FULL_NAME' => ToLower($arr['FULL_NAME']),
						'FORMAT_STRING' => ToLower(trim($arr['FORMAT_STRING'], '#. ')),
					);
				}
			}
		}
		if(!isset($this->arCurrencies[$val]))
		{
			if($val=='RUR' && isset($this->arCurrencies['RUB'])) $val = 'RUB';
			elseif($val=='€' && isset($this->arCurrencies['EUR'])) $val = 'EUR';
			else
			{
				$compVal = ToLower(trim($val, '#. '));
				foreach($this->arCurrencies as $k=>$v)
				{
					if(in_array($compVal, $v))
					{
						$val = $k;
						break;
					}
				}
			}
		}
		if(!isset($this->arCurrencies[$val]))
		{
			$val = $this->params['DEFAULT_CURRENCY'];
		}
		return $val;
	}
	
	public function GetFloatVal($val)
	{
		return floatval(preg_replace('/[^\d\.\-]+/', '', str_replace(',', '.', $val)));
	}
	
	public function GetDateVal($val, $format = 'FULL')
	{
		$time = strtotime($val);
		if($time > 0)
		{
			return ConvertTimeStamp($time, $format);
		}
		return false;
	}
	
	public function Str2Url($string, $arParams=array())
	{
		if(!is_array($arParams)) $arParams = array();
		if($arParams['TRANSLITERATION']=='Y')
		{
			if(isset($arParams['TRANS_LEN'])) $arParams['max_len'] = $arParams['TRANS_LEN'];
			if(isset($arParams['TRANS_CASE'])) $arParams['change_case'] = $arParams['TRANS_CASE'];
			if(isset($arParams['TRANS_SPACE'])) $arParams['replace_space'] = $arParams['TRANS_SPACE'];
			if(isset($arParams['TRANS_OTHER'])) $arParams['replace_other'] = $arParams['TRANS_OTHER'];
			if(isset($arParams['TRANS_EAT']) && $arParams['TRANS_EAT']=='N') $arParams['delete_repeat_replace'] = false;
		}
		return \CUtil::translit($string, LANGUAGE_ID, $arParams);
	}
	
	public function OnShutdown()
	{
		$arError = error_get_last();
		if(!is_array($arError) || !isset($arError['type']) || !in_array($arError['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR))) return;
		
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_FATAL_ERROR"), $arError['type'], $arError['message'], $arError['file'], $arError['line']);
		$this->SaveStatusImport();
		echo '<!--module_return_data-->'.\CUtil::PhpToJSObject($this->GetBreakParams());
		die();
	}
}