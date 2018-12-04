<?php 
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

class View
{
	public static $arParams = array(
		'VIEW' => 'view'
	);

	public static $view = null;

	public function __construct($arParams)
	{
		global $APPLICATION;

		if(is_null(self::$view))
			self::$view = $arParams['VIEW_DEFAULT'];

		$arResult['VIEW'] = array();
		foreach($arParams['LIST_VIEW'] as $view)
    {
  		if(empty($view)) continue;

  		$urlParam = '';
      $url      = '';
      $urlParam = self::$arParams['VIEW'].'='.$view;
      $url      = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['VIEW']));

			$arResult['VIEW'][] = array(
				'NAME'     => $view,
				'SELECTED' => (self::$view == $view)? 'Y' : '',
				'VALUE'    => $view,
				'URL'      => $url,
			);

			if(self::$view == $view) {
				$arResult['VIEW_SELECTED'] = array(
					'NAME'     => $view,
					'VALUE'    => $view,
					'URL'      => $url
				);
			}
    }

    if(!is_array($arResult['VIEW_SELECTED']))
		{
			$urlParam = self::$arParams['VIEW'].'='.$arParams['VIEW_DEFAULT'];
			$url      = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['VIEW']));
			$arResult['VIEW_SELECTED'] = array(
				'NAME'     => $arParams['VIEW_DEFAULT'],
				'VALUE'    => $arParams['VIEW_DEFAULT'],
				'URL'      => $url
			);
		}

    $this->arResult = $arResult;
	}

	public function viewUrl()
	{
		return $this->arResult['VIEW'];
	}
	public function viewUrlSelected()
	{
		return $this->arResult['VIEW_SELECTED'];
	}
	public static function getView()
	{
		return self::$view;
	}
}