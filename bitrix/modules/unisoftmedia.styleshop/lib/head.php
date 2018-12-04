<?php
namespace Unisoftmedia\Styleshop;

use Bitrix\Main\Page\Asset;

class Head {

	private $Options;

    public function __construct(Options $Options)
    {
			$this->Options = $Options;
			$this->Css();
    }
	
	private function Css()
	{
		$this->GoogleFonts();
		$this->Fonts();
		$this->paramsScript();
		$this->Responsive();
	}

	private function Responsive()
	{
		$responsive = $this->Options->get('responsive');
		if($responsive) {
			Asset::getInstance()->addString('<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1" />');
		}
	}

	private function GoogleFonts()
	{
		$googlefonts = $this->Options->get('googlefonts');
		if(isset($googlefonts) && !empty($googlefonts)) {
			$googlefonts = @unserialize($googlefonts);
			foreach($googlefonts as $googlefont) {
				$fonts = str_replace(' ', '+', $googlefont). ':300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic|' . $fonts;
			}
			if(substr($fonts, -1) == '|')
				$fonts = substr($fonts, 0, -1);
			Asset::getInstance()->addString('<link rel="stylesheet" href="//fonts.googleapis.com/css?family=' . $fonts . '" type="text/css" media="screen" />');
		}
	}

	private function Fonts()
	{
		$fonts = $this->Options->get('fonts');
		if(isset($fonts) && !empty($fonts)) {
			$fonts = @unserialize($fonts);
			$font_family = '';
			foreach($fonts as $k => $font) {
				$font_family .= '' . $k . '{font:' . $font . '}';
			}
			Asset::getInstance()->addString('<style type="text/css">' . $font_family . '</style>');
		}
	}

	private function paramsScript()
	{
		$script = 'var phpConst = {\'SITE_TEMPLATE_PATH\':\''.SITE_TEMPLATE_PATH.'\',\'SITE_DIR\':\''.SITE_DIR.'\',\'SITE_ID\':\''.SITE_ID.'\'},'.
		'$in_basket = [],'.
		'$in_compare = [],'.
		'$in_favorites = [];';
		Asset::getInstance()->addString('<script type="text/javascript">' . $script . '</script>');
	}
}