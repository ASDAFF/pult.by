<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID") || !defined("WIZARD_SITE_DIR"))
	return;

$arSites = array();
$rsSites = CSite::GetList($by = "sort", $order = "desc", array());
while ($site = $rsSites->Fetch())
{
    $arSites[] = $site["LID"];
}

$event = array(
  'EVENT_NAME'    => 'RECALL_FORM',
  'NAME'          => GetMessage('EVENT_NAME'),
  'LID'          => 'ru',
  'DESCRIPTION'   => GetMessage('EVENT_DESCRIPTION')
);

$obEventType = new CEventType;
$obEventType->Add($event);

$templates['RECALL'] = array(
	'ACTIVE' => 'Y',
	'EVENT_NAME' => 'RECALL_FORM',
	'LID' => $arSites,
	'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
	'EMAIL_TO' => '#EMAIL_TO#',
	'BCC' => '',
	'SUBJECT' => GetMessage('RECALL_SUBJECT'),
	'BODY_TYPE' => 'text',
	'MESSAGE' => GetMessage('RECALL_MESSAGE'),
);

$templates['RECALL_BUY_ONE_CLICK'] = array(
	'ACTIVE' => 'Y',
	'EVENT_NAME' => 'RECALL_FORM',
	'LID' => $arSites,
	'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
	'EMAIL_TO' => '#EMAIL_TO#',
	'BCC' => '',
	'SUBJECT' => GetMessage('RECALL_BUY_ONE_CLICK_SUBJECT'),
	'BODY_TYPE' => 'text',
	'MESSAGE' => GetMessage('RECALL_BUY_ONE_CLICK_MESSAGE'),
);


$obTemplate = new CEventMessage;

foreach($templates as $template)
{
	$obTemplate->Add($template);
}