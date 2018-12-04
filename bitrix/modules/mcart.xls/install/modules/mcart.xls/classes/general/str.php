<?
class CMcartXlsStrRef
{
function ConvertValueCharset($s, $direction)
	{
		if ("utf-8" == strtolower(LANG_CHARSET))
			return $s;

		if (is_numeric($s))
			return $s;

		if ($direction == BP_EI_DIRECTION_EXPORT)
			$s = $GLOBALS["APPLICATION"]->ConvertCharset($s, LANG_CHARSET, "UTF-8");
		else
			$s = $GLOBALS["APPLICATION"]->ConvertCharset($s, "UTF-8", LANG_CHARSET);

		return $s;
	}

	function ConvertArrayCharset($value, $direction = BP_EI_DIRECTION_EXPORT)
	{
		if (is_array($value))
		{
			$valueNew = array();
			foreach ($value as $k => $v)
			{
				$k = $this->ConvertValueCharset($k, $direction);
				$v = $this->ConvertArrayCharset($v, $direction);
				$valueNew[$k] = $v;
			}
			$value = $valueNew;
		}
		else
		{
			$value = $this->ConvertValueCharset($value, $direction);
		}

		return $value;
	}
	public static function MakeDiapazone()
	{
	$arrTmpLiteras = range("A","Z");
		$arrLiteras = $arrTmpLiteras;
		foreach ($arrTmpLiteras as $lit1)
			foreach ($arrTmpLiteras as $lit2)
				$arrLiteras[] = $lit1.$lit2;
			
		$arrFlip = array_flip($arrLiteras);
		return array("Literas"=>$arrLiteras, "Flip"=>$arrFlip);
	}
}
?>