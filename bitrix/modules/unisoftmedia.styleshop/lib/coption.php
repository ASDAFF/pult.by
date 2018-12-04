<?php

namespace Unisoftmedia\Styleshop;

use Bitrix\Main\Config\Option;

abstract class COption {

	private $module_id;
	private $Option = null;

    public function __construct($module_id)
    {
        $this->module_id = $module_id;
		if(is_null($this->Option)) {
			$this->Option = new Option();
		}
    }

	public function get($name, $default = '', $siteId = false)
	{
		return $this->Option->get($this->module_id, $name, $default, $siteId);
	}

	public function set($name, $value = '', $siteId = '')
	{
		return $this->Option->set($this->module_id, $name, $value, $siteId);
	}

	public function delete($filter = array())
	{
		return $this->Option->delete($this->module_id, $filter);
	}
}