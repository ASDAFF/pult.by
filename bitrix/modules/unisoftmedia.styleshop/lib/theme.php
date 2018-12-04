<?php

namespace Unisoftmedia\Styleshop;

final class Theme {

	private $module_id;
	private $Option = null;

    public function __construct($module_id)
    {
		if (empty($module_id)) {
      throw new \RuntimeException('No module_id');
    }

      $this->module_id = $module_id;
    }

	public function getFonts()
	{
		$Googlefont = new Googlefont();
		return $Googlefont->getFonts();
	}

	public function Option()
	{
		if(is_null($this->Option)) {
			$this->Option = new Options($this->module_id);
		}
		return $this->Option;
	}
	
	public function Head()
	{
		return new Head($this->Option());
	}

	public function Template()
	{
		return new Template($this->Option());
	}

}