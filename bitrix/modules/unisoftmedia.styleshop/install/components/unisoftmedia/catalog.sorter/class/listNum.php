<?php 
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

require_once('listNumInterface.php');
class ListNum implements listNumInterface
{
    public static $arParams = array(
        'LIST_NUM'    => 'list_num',
        'NAME'        => 'name',
        'OUTPUT_LIST_NUM_DEFAULT' => 20,
    );
    public static $list_num     = 0;
    public static $val_show_all = 5000;
    private $arResult           = array();

    public function __construct($arParams)
    {
        global $APPLICATION;
        if(!self::$list_num && !intval($arParams['OUTPUT_LIST_NUM_DEFAULT'])){
            self::$list_num = self::$arParams['OUTPUT_LIST_NUM_DEFAULT'];
        } else if(!self::$list_num && intval($arParams['OUTPUT_LIST_NUM_DEFAULT'])){
            self::$list_num = intval($arParams['OUTPUT_LIST_NUM_DEFAULT']);
        }
        $arResult['OUTPUT_LIST_NUM'] = array();
        foreach($arParams['OUTPUT_LIST_NUM'] as $val)
        {
            if(empty($val)) continue;

            $urlParam = '';
            $url      = '';
            $urlParam = self::$arParams['LIST_NUM'].'='.$val;
            $url      = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['LIST_NUM']));
            $arResult['OUTPUT_LIST_NUM'][] = array(
                'NAME'     => $val,
                'SELECTED' => (self::$list_num == $val)? 'Y' : '',
                'VALUE'    => $val,
                'URL'      => $url,
            );
            if(self::$list_num == $val) {
                $arResult['OUTPUT_LIST_NUM_SELECTED'] = array(
                    'NAME'     => $val,
                    'VALUE'    => $val,
                    'URL'      => $url
                );
            }
        }
        if($arParams['OUTPUT_LIST_NUM_SHOW_ALL'] == 'Y')
    	{
    	    $val      = self::$val_show_all;
    	    $urlParam = self::$arParams['LIST_NUM'].'='.$val;
    		$url      = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['LIST_NUM']));
    		$arResult['OUTPUT_LIST_NUM'][] = array(
    			'NAME'  => GetMessage('OUTPUT_LIST_NUM_SHOW_ALL'),
                'SELECTED' => (self::$list_num == $val)? 'Y' : '',
    			'VALUE' => $val,
    			'URL'   => $url,
    		);
            if(self::$list_num == $val){
                $arResult['OUTPUT_LIST_NUM_SELECTED'] = array(
                    'NAME'     => GetMessage('OUTPUT_LIST_NUM_SHOW_ALL'),
                    'VALUE'    => $val,
                    'URL'      => $url
                );
            }
    	}
        if(!is_array($arResult['OUTPUT_LIST_NUM_SELECTED']))
        {
            if(!intval($arParams['OUTPUT_LIST_NUM_DEFAULT'])){
                $arParams['OUTPUT_LIST_NUM_DEFAULT'] = self::$arParams['OUTPUT_LIST_NUM_DEFAULT'];
            }
            $urlParam = self::$arParams['LIST_NUM'].'='.$arParams['OUTPUT_LIST_NUM_DEFAULT'];
            $url      = $APPLICATION->GetCurPageParam($urlParam,array(self::$arParams['LIST_NUM']));
            $arResult['OUTPUT_LIST_NUM_SELECTED'] = array(
                'NAME'     => $arParams['OUTPUT_LIST_NUM_DEFAULT'],
                'VALUE'    => $arParams['OUTPUT_LIST_NUM_DEFAULT'],
                'URL'      => $url
            );
        }
         $this->arResult = $arResult;
    }
    
    public function listNumUrl()
    {
        return $this->arResult['OUTPUT_LIST_NUM'];
    }
    public function listNumUrlSelected()
    {
        return $this->arResult['OUTPUT_LIST_NUM_SELECTED'];
    }
    public static function getListNum()
    {
        return self::$list_num;
    }
}