<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"ADD_IN_CACHE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("ADD_IN_CACHE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
	),
	
);
?>