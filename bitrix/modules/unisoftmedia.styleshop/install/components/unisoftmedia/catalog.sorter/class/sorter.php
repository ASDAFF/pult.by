<?php 
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
require_once('sorterInterface.php');

class Sorter implements sorterInterface
{
    public static $arParams = array(
        'TYPE'        => 'type',
        'SORT'        => 'sort',
        'SORTER_DEFAULT' => array(
            'TYPE' => 'desc',
            'SORT' => 'popular',
        ),
    );
    public static $type_sort = array(
        'DESC' => 'desc',
        'ASC'  => 'asc',
    );
    
    public static $getType = '';
    public static $getSort = '';
    private $arResult      = array();
    private $priceCode     = 'price';
    private $newsCode      = 'newproduct';
    private $popularCode   = 'popular';

    public function __construct($arParams)
    {
        global $APPLICATION;
        if(!self::$getType || !self::$getSort) {
            self::$getType = self::$arParams['SORTER_DEFAULT']['TYPE'];
            self::$getSort = self::$arParams['SORTER_DEFAULT']['SORT'];
        }
        $arResult['SORTER'] = array();
        foreach($arParams['SORTERED_ACCESS_OPTIONS'] as $k => $arSort)
        {
			if(!$arSort) continue;

            $urlParam      = '';
            $urlParam2     = '';
            $url           = '';
            $url2          = '';
            if(false !== stripos($arSort, $this->priceCode))
            {
                $name = $this->priceCode;
            } else if(false !== stripos($arSort, $this->newsCode))
            {
                $name = $this->newsCode;
            } else if(false !== stripos($arSort, $this->popularCode))
            {
                $name = $this->popularCode;
            }else
            {
                $name = $arSort;
            }
            if($name == self::$getSort){
                self::$getSort = $arSort;
            }
            $urlParam      = self::$arParams['SORT'].'='.$name.'&'.self::$arParams['TYPE'].'='.self::$type_sort['DESC'];
            $urlParam2     = self::$arParams['SORT'].'='.$name.'&'.self::$arParams['TYPE'].'='.self::$type_sort['ASC'];
            $url           = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['SORT'],self::$arParams['TYPE'],self::$type_sort['DESC']));
            $url2          = $APPLICATION->GetCurPageParam($urlParam2,array(self::$arParams['SORT'],self::$arParams['TYPE'],self::$type_sort['ASC']));
            $arResult['SORTER'][$k]['DESC'] = array(
                'NAME'     => GetMessage('SORTED_'.$name),
                'SELECTED' => (self::$type_sort['DESC'] == self::$getType && $arSort == self::$getSort)? 'Y' : '',
                'VALUE'    => $arSort,
                'URL'      => $url,
            );
            $arResult['SORTER'][$k]['ASC'] = array(
                'NAME'     => GetMessage('SORTED_'.$name),
                'SELECTED' => (self::$type_sort['ASC'] == self::$getType && $arSort == self::$getSort)? 'Y' : '',
                'VALUE'    => $arSort,
                'URL'      => $url2,
            );
            
            if('Y' == $arResult['SORTER'][$k]['ASC']['SELECTED'] || 'Y' == $arResult['SORTER'][$k]['DESC']['SELECTED']){
                $arResult['SORTER_SELECTED'] = array(
                    'NAME'     => GetMessage('SORTED_'.$name),
                    'TYPE'     => self::$getType,
                    'VALUE'    => $arSort,
                    'URL'      => $arResult['SORTER'][$k][strtoupper(self::$getType)]['URL'],
                    'URL2'     => ($arResult['SORTER'][$k]['ASC']['SELECTED'])? $arResult['SORTER'][$k]['DESC']['URL'] : $arResult['SORTER'][$k]['ASC']['URL'],
                );
            }
        }
        if(false !== stripos(self::$getSort, $this->popularCode))
        {
            self::$getSort = 'show_counter';
        }
        $this->arResult = $arResult;
    }
    public function sorterUrl()
    {
        return $this->arResult['SORTER'];
    }
    public function sorterUrlSelected()
    {
        return $this->arResult['SORTER_SELECTED'];
    }
    public static function getType()
    {
        return self::$getType;
    }
    public static function getSort()
    {
        return self::$getSort;
    }
}