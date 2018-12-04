<?php
include_once(dirname(__FILE__).'/install/demo.php');

$moduleId = 'esol.importxml';
$pathJS = '/bitrix/js/'.$moduleId;
$pathCSS = '/bitrix/panel/'.$moduleId;
$pathLang = BX_ROOT.'/modules/'.$moduleId.'/lang/'.LANGUAGE_ID;
CModule::AddAutoloadClasses(
	$moduleId,
	array(
		'\Bitrix\EsolImportxml\Profile' => "lib/profile.php",
		'\Bitrix\EsolImportxml\ProfileTable' => "lib/profile_table.php",
		'\Bitrix\EsolImportxml\Utils' => "lib/utils.php",
		'\Bitrix\EsolImportxml\Json2Xml' => "lib/json2xml.php",
		'\Bitrix\EsolImportxml\Sftp' => "lib/sftp.php",
		'\Bitrix\EsolImportxml\Conversion' => "lib/conversion.php",
		'\Bitrix\EsolImportxml\Cloud' => "lib/cloud.php",
		'\Bitrix\EsolImportxml\Cloud\MailRu' => "lib/cloud/mail_ru.php",
		'\Bitrix\EsolImportxml\ZipArchive' => "lib/zip_archive.php",
		'\Bitrix\EsolImportxml\XMLViewer' => "lib/xml_viewer.php",
		'\Bitrix\EsolImportxml\FieldList' => "lib/field_list.php",
		'\Bitrix\EsolImportxml\Importer' => "lib/importer.php",
		'\Bitrix\EsolImportxml\Logger' => "lib/logger.php",
		'\Bitrix\EsolImportxml\Extrasettings' => "lib/extrasettings.php",
		'\Bitrix\EsolImportxml\Imap' => "lib/mail/imap.php",
		'\Bitrix\EsolImportxml\SMail' => "lib/mail/mail.php",
		'\Bitrix\EsolImportxml\MailHeader' => "lib/mail/mail_header.php",
		'\Bitrix\EsolImportxml\MailMessage' => "lib/mail/mail_message.php",
		'\Bitrix\EsolImportxml\MailUtil' => "lib/mail/mail_util.php",
	)
);
define("esol_importxml_OLDSITEEXPIREDATE", 1517833404);
define("esol_importxml_DEMO", "Y");
$arJSEsolImportXmlConfig = array(
	'esol_importxml' => array(
		'js' => array($pathJS.'/chosen/chosen.jquery.min.js', $pathJS.'/script.js'),
		'css' => array($pathJS.'/chosen/chosen.min.css', $pathCSS.'/styles.css'),
		'rel' => array('jquery'),
		'lang' => $pathLang.'/js_admin.php',
	),
);

foreach ($arJSEsolImportXmlConfig as $ext => $arExt) {
	CJSCore::RegisterExt($ext, $arExt);
}
?>