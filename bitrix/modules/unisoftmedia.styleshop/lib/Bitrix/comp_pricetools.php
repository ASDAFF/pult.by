<?
namespace Unisoftmedia\Styleshop\Bitrix;

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Highloadblock\HighloadBlockTable,
	Bitrix\Iblock,
	Bitrix\Main;

class CIBlockPriceTools
{
	protected static $highLoadInclude = null;

	public static function getTreePropertyValues(&$propList, &$propNeedValues)
	{
		$result = array();
		if (!empty($propList) && is_array($propList))
		{
			foreach ($propList as $oneProperty)
			{
				$values = array();
				$valuesExist = false;
				$isPict = false;
				$isDesc = false;
				$pictMode = ('PICT' == $oneProperty['SHOW_MODE']);
				$needValuesExist = !empty($propNeedValues[$oneProperty['ID']]) && is_array($propNeedValues[$oneProperty['ID']]);
				$filterValuesExist = ($needValuesExist && count($propNeedValues[$oneProperty['ID']]) <= 500);
				$needValues = array();
				if ($needValuesExist)
					$needValues = array_fill_keys($propNeedValues[$oneProperty['ID']], true);
				switch($oneProperty['PROPERTY_TYPE'])
				{
					case Iblock\PropertyTable::TYPE_LIST:
						$propEnums = \CIBlockProperty::GetPropertyEnum(
							$oneProperty['ID'],
							array('SORT' => 'ASC', 'VALUE' => 'ASC')
						);
						while ($oneEnum = $propEnums->Fetch())
						{
							$oneEnum['ID'] = (int)$oneEnum['ID'];
							if ($needValuesExist && !isset($needValues[$oneEnum['ID']]))
								continue;
							$values[$oneEnum['ID']] = array(
								'ID' => $oneEnum['ID'],
								'NAME' => $oneEnum['VALUE'],
								'SORT' => (int)$oneEnum['SORT'],
								'PICT' => false
							);
							$valuesExist = true;
						}
						$values[0] = array(
							'ID' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true,
							'NAME' => $oneProperty['DEFAULT_VALUES']['NAME'],
							'PICT' => $oneProperty['DEFAULT_VALUES']['PICT']
						);
						break;
					case Iblock\PropertyTable::TYPE_ELEMENT:
						$selectFields = array('ID', 'NAME');
						if ($pictMode)
							$selectFields[] = 'PREVIEW_PICTURE';
						$filterValues = (
							$filterValuesExist
							? array('ID' => array_values($propNeedValues[$oneProperty['ID']]), 'IBLOCK_ID' => $oneProperty['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y')
							: array('IBLOCK_ID' => $oneProperty['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y')
						);
						$propEnums = \CIBlockElement::GetList(
							array('SORT' => 'ASC', 'NAME' => 'ASC'),
							$filterValues,
							false,
							false,
							$selectFields
						);
						while ($oneEnum = $propEnums->Fetch())
						{
							if ($needValuesExist && !$filterValuesExist)
							{
								if (!isset($needValues[$oneEnum['ID']]))
									continue;
							}
							if ($pictMode)
							{
								$oneEnum['PICT'] = false;
								if (!empty($oneEnum['PREVIEW_PICTURE']))
								{
									$previewPict = \CFile::GetFileArray($oneEnum['PREVIEW_PICTURE']);
									if (!empty($previewPict))
									{
										$oneEnum['PICT'] = array(
											'SRC' => $previewPict['SRC'],
											'WIDTH' => (int)$previewPict['WIDTH'],
											'HEIGHT' => (int)$previewPict['HEIGHT']
										);
									}
								}
								if (empty($oneEnum['PICT']))
								{
									$oneEnum['PICT'] = $oneProperty['DEFAULT_VALUES']['PICT'];
								}
							}
							$oneEnum['ID'] = (int)$oneEnum['ID'];
							$values[$oneEnum['ID']] = array(
								'ID' => $oneEnum['ID'],
								'NAME' => $oneEnum['NAME'],
								'SORT' => (int)$oneEnum['SORT'],
								'PICT' => ($pictMode ? $oneEnum['PICT'] : false)
							);
							$valuesExist = true;
						}
						$values[0] = array(
							'ID' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true,
							'NAME' => $oneProperty['DEFAULT_VALUES']['NAME'],
							'PICT' => ($pictMode ? $oneProperty['DEFAULT_VALUES']['PICT'] : false)
						);
						break;
					case Iblock\PropertyTable::TYPE_STRING:
						if (self::$highLoadInclude === null)
							self::$highLoadInclude = Loader::includeModule('highloadblock');
						if (!self::$highLoadInclude)
							continue;
						$xmlMap = array();
						$sortExist = isset($oneProperty['USER_TYPE_SETTINGS']['FIELDS_MAP']['UF_SORT']);

						$directorySelect = array('ID', 'UF_NAME', 'UF_XML_ID', 'UF_DESCRIPTION');
						$directoryOrder = array();
						if ($pictMode)
						{
							$directorySelect[] = 'UF_FILE';
						}
						if ($sortExist)
						{
							$directorySelect[] = 'UF_SORT';
							$directoryOrder['UF_SORT'] = 'ASC';
						}
						$directoryOrder['UF_NAME'] = 'ASC';
						$sortValue = 100;

						$entityDataClass = $oneProperty['USER_TYPE_SETTINGS']['ENTITY']->getDataClass();
						$entityGetList = array(
							'select' => $directorySelect,
							'order' => $directoryOrder
						);
						if ($filterValuesExist)
							$entityGetList['filter'] = array('=UF_XML_ID' => array_values($propNeedValues[$oneProperty['ID']]));
						$propEnums = $entityDataClass::getList($entityGetList);
						
						while ($oneEnum = $propEnums->fetch())
						{
							if ($needValuesExist && !$filterValuesExist)
							{
								if (!isset($needValues[$oneEnum['UF_XML_ID']]))
									continue;
							}

							$oneEnum['ID'] = (int)$oneEnum['ID'];
							$oneEnum['UF_SORT'] = ($sortExist ? (int)$oneEnum['UF_SORT'] : $sortValue);
							$sortValue += 100;

							if ($pictMode)
							{
								if (!empty($oneEnum['UF_FILE']))
								{
									$arFile = \CFile::GetFileArray($oneEnum['UF_FILE']);
									if (!empty($arFile))
									{
										$oneEnum['PICT'] = array(
											'SRC' => $arFile['SRC'],
											'WIDTH' => (int)$arFile['WIDTH'],
											'HEIGHT' => (int)$arFile['HEIGHT']
										);
										$isPict = true;
									}
								}

								if (empty($oneEnum['PICT']))
									$oneEnum['PICT'] = $oneProperty['DEFAULT_VALUES']['PICT'];
							}
							if($oneEnum['UF_DESCRIPTION'])
								$isDesc = true;

							$values[$oneEnum['ID']] = array(
								'ID' => $oneEnum['ID'],
								'NAME' => $oneEnum['UF_NAME'],
								'SORT' => (int)$oneEnum['UF_SORT'],
								'XML_ID' => $oneEnum['UF_XML_ID'],
								'PICT' => ($pictMode ? $oneEnum['PICT'] : false),
								'DESCRIPTION' => ($oneEnum['UF_DESCRIPTION'])? $oneEnum['UF_DESCRIPTION']: false
							);

							$valuesExist = true;
							$xmlMap[$oneEnum['UF_XML_ID']] = $oneEnum['ID'];
						}
						$values[0] = array(
							'ID' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true,
							'NAME' => $oneProperty['DEFAULT_VALUES']['NAME'],
							'XML_ID' => '',
							'PICT' => ($pictMode ? $oneProperty['DEFAULT_VALUES']['PICT'] : false)
						);
						if ($valuesExist)
							$oneProperty['XML_MAP'] = $xmlMap;
					break;
				}
				if (!$valuesExist)
					continue;
				$oneProperty['VALUES'] = $values;
				$oneProperty['VALUES_COUNT'] = count($values);

				if(!$isPict && $isDesc)
					$oneProperty['SHOW_MODE'] = 'TEXT';

				$result[$oneProperty['CODE']] = $oneProperty;
			}
		}
		$propList = $result;
		unset($arFilterProp);
	}
}