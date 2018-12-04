<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();
require_once('class/listNum.php');
require_once('class/sorter.php');
require_once('class/view.php');

$arResult = array();
$sort = false;
if('Y' == $arParams['OUTPUT_LIST_NUM_SHOW'] && 0 < $val = intval($_REQUEST[ListNum::$arParams['LIST_NUM']]))
{
    $sort = true;
    if('Y' == $arParams['OUTPUT_LIST_NUM_SHOW_ALL']){
        $arParams['OUTPUT_LIST_NUM'][] = ListNum::$val_show_all;
    }
    if(in_array($val, $arParams['OUTPUT_LIST_NUM']))
    {
        ListNum::$list_num = $val;
        $_SESSION['SORTER']['OUTPUT_LIST_NUM']= ListNum::getListNum();
    }
    if('Y' == $arParams['OUTPUT_LIST_NUM_SHOW_ALL']){
        array_pop($arParams['OUTPUT_LIST_NUM']);
    }
}
if('Y' == $arParams['SORTERED_SHOW'] &&
    isset($_REQUEST[Sorter::$arParams['SORT']]) && 
    0 < strlen($_REQUEST[Sorter::$arParams['SORT']]) && 
    isset($_REQUEST[Sorter::$arParams['TYPE']]) && 
    0 < strlen($_REQUEST[Sorter::$arParams['TYPE']]))
{
    if((in_array($_REQUEST[Sorter::$arParams['TYPE']], Sorter::$type_sort) && in_array($_REQUEST[Sorter::$arParams['SORT']], $arParams['SORTERED_ACCESS_OPTIONS'])) 
    || ($_REQUEST[Sorter::$arParams['SORT']] == 'price') && in_array($_REQUEST[Sorter::$arParams['TYPE']], Sorter::$type_sort)
    || ($_REQUEST[Sorter::$arParams['SORT']] == 'newproduct') && in_array($_REQUEST[Sorter::$arParams['TYPE']], Sorter::$type_sort)
    || ($_REQUEST[Sorter::$arParams['SORT']] == 'popular') && in_array($_REQUEST[Sorter::$arParams['TYPE']], Sorter::$type_sort))
    {
        $sort = true;
        Sorter::$getType = $_REQUEST[Sorter::$arParams['TYPE']];
        Sorter::$getSort = $_REQUEST[Sorter::$arParams['SORT']];
        unset($_SESSION['SORTER']['TYPE']);
        unset($_SESSION['SORTER']['SORT']);
        $_SESSION['SORTER']['TYPE'] = Sorter::getType();
        $_SESSION['SORTER']['SORT'] = Sorter::getSort();
    }
}

if('Y' == $arParams['USE_VIEW'] && isset($_REQUEST[View::$arParams['VIEW']]) && 0 < strlen($_REQUEST[View::$arParams['VIEW']]))
{
    $sort = true;
    View::$view = $_REQUEST[View::$arParams['VIEW']];
    $_SESSION['SORTER']['VIEW'] = View::getView();
}

if(!$sort && isset($_SESSION['SORTER']) && !empty($_SESSION['SORTER']))
{
    if(isset($_SESSION['SORTER']['TYPE']) &&
    !empty($_SESSION['SORTER']['TYPE']) &&
    'Y' == $arParams['SORTERED_SHOW'])
        Sorter::$getType = $_SESSION['SORTER']['TYPE'];
    if(isset($_SESSION['SORTER']['SORT']) &&
    !empty($_SESSION['SORTER']['SORT']) &&
    'Y' == $arParams['SORTERED_SHOW'])
        Sorter::$getSort = $_SESSION['SORTER']['SORT'];
    if(isset($_SESSION['SORTER']['OUTPUT_LIST_NUM']) &&
    !empty($_SESSION['SORTER']['OUTPUT_LIST_NUM']) &&
    'Y' == $arParams['OUTPUT_LIST_NUM_SHOW'])
        ListNum::$list_num = $_SESSION['SORTER']['OUTPUT_LIST_NUM'];
    if(isset($_SESSION['SORTER']['VIEW']) &&
    !empty($_SESSION['SORTER']['VIEW']) &&
    'Y' == $arParams['USE_VIEW'])
        View::$view = $_SESSION['SORTER']['VIEW'];
}

//////////////////////////////////////////////////////////////
if('Y' == $arParams['OUTPUT_LIST_NUM_SHOW'])
{
    if(is_array($arParams['OUTPUT_LIST_NUM']) && !empty($arParams['OUTPUT_LIST_NUM']))
    {
        $arResult['OUTPUT_LIST_NUM']          = array();
        $arResult['OUTPUT_LIST_NUM_SELECTED'] = array();
        $ListNum = new ListNum($arParams);
        $arResult['OUTPUT_LIST_NUM']          = $ListNum->listNumUrl();
        $arResult['OUTPUT_LIST_NUM_SELECTED'] = $ListNum->listNumUrlSelected();
    }
}

if('Y' == $arParams['SORTERED_SHOW'])
{
    if(is_array($arParams['SORTERED_ACCESS_OPTIONS']) && !empty($arParams['SORTERED_ACCESS_OPTIONS']))
    {
        $arResult['SORTER']          = array();
        $arResult['SORTER_SELECTED'] = array();
        $Sorter = new Sorter($arParams);
        $arResult['SORTER']          = $Sorter->sorterUrl();
        $arResult['SORTER_SELECTED'] = $Sorter->sorterUrlSelected();
    }
}

if('Y' == $arParams['USE_VIEW'])
{
    if(is_array($arParams['LIST_VIEW']) && !empty($arParams['LIST_VIEW']))
    {
        $arResult['VIEW']          = array();
        $arResult['VIEW_SELECTED'] = array();
        $Sorter = new View($arParams);
        $arResult['VIEW']          = $Sorter->viewUrl();
        $arResult['VIEW_SELECTED'] = $Sorter->viewUrlSelected();
    }
}

$this->IncludeComponentTemplate();