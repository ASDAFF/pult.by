<?php

use Bitrix\Main,
Bitrix\Iblock,
Bitrix\Main\Loader,
Bitrix\Catalog,
Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class WishlistComponent extends CBitrixComponent
{
	private $application = null;

	private $error = null;

	private $successful = null;

	private $successfulAdd = true;

	private $productID = 0;

	private static $nextNumber = 0;

	protected $componentId = '';

	private $arId = [];

	private $arSelect = [
		"ID",
		"IBLOCK_ID",
		"CODE",
		"NAME",
		"SORT",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"DATE_CREATE",
		"CREATED_BY",
		"TIMESTAMP_X",
		"MODIFIED_BY",
		"TAGS",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_PICTURE",
		"PREVIEW_PICTURE",
		"CATALOG_GROUP_BASE"
	];

	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->componentId = $this->request->isAjaxRequest()? randString(7) : $this->randString();
	}

	public function executeComponent()
	{
		if(!Loader::includeModule("iblock"))
				return;

		if($this->isAdd())
			$this->add();

		$this->GetList();
		if ($this->isAjax() && $this->arParams['AJAX'] == 'Y') {
			$this->includeComponentTemplate('ajax_template');
		}
		else {
			$this->IncludeComponentTemplate();
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

		if ($params['AJAX'] != 'Y')
			$params['AJAX'] = 'N';

		if (!isset($params['MAX_WIDTH_WISHLIST']) || intval($params["MAX_WIDTH_WISHLIST"]) <= 0)
			$params['MAX_WIDTH_WISHLIST'] = 70;

		if (!isset($params['MAX_HEIGHT_WISHLIST']) || intval($params["MAX_HEIGHT_WISHLIST"]) <= 0)
			$params['MAX_HEIGHT_WISHLIST'] = 70;

		return $params;
	}

	private function add()
	{
		if ($this->isAjax() && $this->isAjaxAdd())
			CUtil::JSPostUnescape();

		$intProductIBlockID = (int)CIBlockElement::GetIBlockByID($this->productID);
		if (0 < $intProductIBlockID)
		{
			if(!is_array($_SESSION['WISHLIST'][SITE_ID]))
		  {
		      $_SESSION['WISHLIST'][SITE_ID] = array();
		  }
			if(in_array($this->productID, $_SESSION['WISHLIST'][SITE_ID]))
      {
      		$key = array_search($this->productID, $_SESSION['WISHLIST'][SITE_ID]);
          unset($_SESSION['WISHLIST'][SITE_ID][$key]);
          $this->application()->set_cookie('WISHLIST['.SITE_ID.']['.$key.']', '', time()-1, "/", SITE_SERVER_NAME);
          $this->successful = Loc::getMessage('CATALOG_SUCCESSFUL_DEL_TO_WISHLIST');
      }
      else
      {
          $_SESSION['WISHLIST'][SITE_ID][] = $this->productID;

          foreach($_SESSION['WISHLIST'][SITE_ID] as $k => $v)
          {
            $this->application()->set_cookie('WISHLIST['.SITE_ID.']['.$k.']', $v, time()+7*24*60*60, "/", SITE_SERVER_NAME);
          }
          $this->successful = Loc::getMessage('CATALOG_SUCCESSFUL_ADD_TO_WISHLIST');
      }
		}
		else
		{
			$this->error = Loc::getMessage('CATALOG_PRODUCT_NOT_FOUND');
			$this->successfulAdd = false;
		}
		if ($this->isAjax() && $this->isAjaxAdd())
		{
			if ($this->successfulAdd)
			{
				$addResult = array('STATUS' => 'OK', 'MESSAGE' => $this->successful);
			}
			else
			{
				$addResult = array('STATUS' => 'ERROR', 'MESSAGE' => $this->error);
			}
			$this->application()->RestartBuffer();
			echo CUtil::PhpToJSObject($addResult);
			die();
		}
		else
		{
			if ($this->successfulAdd && !$this->request->isPost())
			{
				LocalRedirect($this->application()->GetCurPage());
			}
		}
	}

	private function getCookie()
	{
		if(!isset($_SESSION['WISHLIST'][SITE_ID]) || empty($_SESSION['WISHLIST'][SITE_ID]))
		{
			$_SESSION['WISHLIST'][SITE_ID] = $this->application()->get_cookie('WISHLIST['.SITE_ID.']');
		}

		return $this->arId = (!empty($_SESSION['WISHLIST'][SITE_ID])) ? $_SESSION['WISHLIST'][SITE_ID] : array();
	}

	private function GetList()
	{
		$this->getCookie();

		$arResult['ITEMS'] = [];
		if(!empty($this->arId) && $this->arParams['SHOW_PRODUCTS'] == 'Y')
		{
			$arFilter = ["ID"=>$this->arId];
			$rsElements = CIBlockElement::GetList([], $arFilter, false, ["nPageSize"=>50], $this->arSelect);

			$intKey = 0;
			while($arItem = $rsElements->GetNext())
			{
				$arItem['ID'] = (int)$arItem['ID'];

				$arResult['arID'][$intKey] = $arItem['ID'];

				Iblock\Component\Tools::getFieldImageData(
					$arItem,
					['PREVIEW_PICTURE', 'DETAIL_PICTURE'],
					Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
					'IPROPERTY_VALUES'
				);

					$arItem["PICTURE_SRC"] = '';
					$arImage = null;
					if (is_array($arItem["PREVIEW_PICTURE"]))
						$arImage = $arItem["PREVIEW_PICTURE"];
					elseif (is_array($arItem["DETAIL_PICTURE"]))
						$arImage = $arItem["DETAIL_PICTURE"];
					if ($arImage)
					{
						$arFileTmp = CFile::ResizeImageGet(
							$arImage,
							array('width' => $this->arParams['MAX_WIDTH_WISHLIST'], 'height' => $this->arParams['MAX_HEIGHT_WISHLIST']),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);
						$arItem['PICTURE_SRC'] = $arFileTmp['src'];
					}

				$arResult['ITEMS'][$intKey] = $arItem;

				$intKey++;
			}
		}
		if($this->arParams['SHOW_PRODUCTS'] == 'Y') {
			$arResult['NUM_PRODUCTS'] = count($arResult['ITEMS']);
		}
		else {
			$arResult['arID'] = $this->arId;
			$arResult['NUM_PRODUCTS'] = count($this->arId);
		}

		$this->arResult = $arResult;
	}

	private function isAdd()
	{
		return $this->request->get('action') == 'ADD2LIKED' && 0 < $this->productID = intval($this->request->get('id'));
	}

	private function isAjaxAdd()
	{
		return $this->isAjax() && $this->request->get('ajax_liked') == 'Y';
	}

	private function application()
	{
		if(is_null($this->application))
		{
			global $APPLICATION;
			$this->application = $APPLICATION;
		}
		return $this->application;
	}

	/**
	 * Is AJAX Request?
	 * @return bool
	 */
	protected function isAjax()
	{
		return $this->request->isAjaxRequest();
	}
}