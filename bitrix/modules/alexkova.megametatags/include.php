<?
$module_id = 'alexkova.megametatags';

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	$module_id,
	array(
		"CMegaMetaRules"=> "classes/general/CMegaMetaRules.php",
		"CBIblockMegaMetaTags"=> "classes/general/CBIblockMegaMetaTags.php",
		"CMegaMetaKeys"=> "classes/general/CMegaMetaKeys.php",
		)
	);
?>