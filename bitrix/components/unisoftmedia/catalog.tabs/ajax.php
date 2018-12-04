<?php

define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);

if (!isset($_POST['siteId']) || !is_string($_POST['siteId']))
	die();

if (!isset($_POST['templateName']) || !is_string($_POST['templateName']))
	die();

if (!isset($_POST['CODE']))
	die();

if ($_SERVER['REQUEST_METHOD'] != 'POST' ||
	preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']) !== 1 ||
	preg_match('/^[.A-Za-z0-9_-]+$/', $_POST['templateName']) !== 1)
	die;

define('SITE_ID', $_POST['siteId']);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
if (!check_bitrix_sessid())
	die;

$_POST['arParams']['AJAX'] = 'Y';
$_POST['arParams']['CODE'] = $_POST['CODE'];
$arParams = $_POST['arParams'];

$APPLICATION->RestartBuffer();
header('Content-Type: text/html; charset='.LANG_CHARSET);

if (SITE_CHARSET != "utf-8")
{
	$arParams = $APPLICATION->ConvertCharset($arParams, "utf-8", SITE_CHARSET);
}

$APPLICATION->IncludeComponent('unisoftmedia:catalog.tabs', $_POST['templateName'], $arParams);
