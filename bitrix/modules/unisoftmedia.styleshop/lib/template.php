<?php

namespace Unisoftmedia\Styleshop;

use Bitrix\Main\Application;

final class Template {

	private $Options;

	private $folderTemplate;

    public function __construct(Options $Options)
    {
			$this->Options = $Options;
			$Options = $this->Options->toArray();
			$this->folderTemplate = Application::getDocumentRoot() . SITE_DIR . $Options['folderTemplate'] . '/' . $this->Options->get('template', '', SITE_ID) . '/';
    }

	public function requireTemplate()
	{
		if (file_exists($this->folderTemplate . 'index.php') && file_exists($this->folderTemplate . 'include_areas/')) {
			global $APPLICATION;
			require_once($this->folderTemplate . 'index.php');
		}
	}
}