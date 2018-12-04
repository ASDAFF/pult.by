<?
IncludeModuleLangFile(__FILE__);
global $DB,$MESS,$USER,$APPLICATION;

CModule::AddAutoloadClasses(
	"unisoftmedia.styleshop",
	array(
		'\Unisoftmedia\Styleshop\Theme' => "lib/theme.php",
		'\Unisoftmedia\Styleshop\Library\SimpleHtml' => "lib/Library/SimpleHtml.php",
		'\Unisoftmedia\Styleshop\Library\SimpleConst' => "lib/Library/SimpleConst.php",
		'\Unisoftmedia\Styleshop\Library\SimpleHtmlDom' => "lib/Library/SimpleHtmlDom.php",
		'\Unisoftmedia\Styleshop\Library\SimpleHtmlDomNode' => "lib/Library/SimpleHtmlDomNode.php",
		'\Unisoftmedia\Styleshop\Bitrix\CIBlockPriceTools' => "lib/Bitrix/comp_pricetools.php"
	)
);