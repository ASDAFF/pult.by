<?php
use Bitrix\Main,
		Bitrix\Main\Entity\ReferenceField,
		Bitrix\Main\Loader,
		Bitrix\Iblock;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CCatalogTabs extends CBitrixComponent
{
	private static $nextNumber = 0;
	protected $componentId = '';

	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->componentId = $this->request->isAjaxRequest()? randString(7) : $this->randString();
	}

	public function getComponentId()
	{
		return $this->componentId;
	}

	public function executeComponent()
	{
		global $APPLICATION;
		// output
		if ($this->isAjax()) {
			$APPLICATION->RestartBuffer();
			$this->getProperty();
			$this->includeComponentTemplate('ajax_template');
			die();
		}
		else if (!$this->extractDataFromCache())
		{
			$this->getProperties();
			$this->setResultCacheKeys(array());
			$this->includeComponentTemplate();
		}
	}

	private function getProperty()
	{
		if (!Loader::includeModule('iblock'))
			return;

		$this->arResult['ITEMS'] = array();

		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'CODE' => $this->arParams['CODE'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			if(in_array($arProp['CODE'], $this->arParams['TABS']))
			{
				$this->arResult['ITEMS'][$arProp['CODE']] = $arProp['NAME'];
			}
		}
	}

	private function getProperties()
	{
		if (!Loader::includeModule('iblock'))
			return;

		$this->arResult['ITEMS'] = array();

		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			if(in_array($arProp['CODE'], $this->arParams['TABS']))
			{
				$this->arResult['ITEMS'][$arProp['CODE']] = $arProp['NAME'];
			}
		}
	}

	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);
		if(!isset($params["CACHE_TIME"]))
			$params["CACHE_TIME"] = 86400;

		if ($this->isAjax()) {
			if(!isset($params["CODE"]))
				$params["CODE"] = $this->request->get('CODE')? $this->request->get('CODE') : false;
		}

		if(isset($params['IBLOCK_ID']))
			$params['IBLOCK_ID'] = (int)$params['IBLOCK_ID'];
		else
			$params['IBLOCK_ID'] = -1;

		return $params;
	}


	/**
	 * @override
	 *
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		if($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		global $USER;

		return !($this->StartResultCache(false, $USER->GetGroups(), '/'.$this->getSiteId().'/bitrix/catalog.tabs'));
	}

	public static function getNextNumber()
	{
		return ++self::$nextNumber;
	}

	/**
	 * Is AJAX Request?
	 * @return bool
	 */
	protected function isAjax()
	{
		return $this->request->isAjaxRequest() && $this->request->get('ajax_tabs') == 'Y';
	}
}