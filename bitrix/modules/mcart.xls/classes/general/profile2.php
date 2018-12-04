<?
class CMcartXlsProfile2
{
	public static function GetRows(  
			$tableName, // имя таблицы
			$arFields, // набор полей (в том порядке, в котором будут выведены
			$arrWhere = array(),
			$count=0,  // количество запрашиваемых строк. 
			$order = "RAND()" // сортировка. ПО умолчанию - случайная
			
			)
	{
		global $DB;
		$arrRows = array();
		$strFields = implode(",", $arFields);
		$strWhere = "";
		if (!empty($arrWhere))
			{
			
				foreach ($arrWhere as $n=>$val)
					{
						
						if (empty($strWhere))
							{	if (is_array($val))
									$strWhere = " WHERE ".$n." in (".implode(",",$val).")";
								else
									$strWhere = " WHERE ".$n."='".$val."'"; 
							}
						else	
							{	if (is_array($val))
									$strWhere.= " AND ".$n." in (".implode(",",$val).")";
								else
									$strWhere.= " AND ".$n."='".$val."'"; 
							}
					}
			}
		if (intval($count)>0)	
			$limit = " LIMIT ".$count;
		else
			$limit = "";
		$res = $DB->query("SELECT ".$strFields." FROM ".$tableName.$strWhere." ORDER BY ".$order.$limit, true);
		if ($res)
		{
		while ($ar = $res->GetNext())
				$arrRows[] = $ar;
		return $arrRows;
		}
		else return array();
	}
	//=========================================================================================================
		public static function UpdateTable(
			$TableName, //имя таблицы
			$arrFields, //массив названий полей
			$arrValues, //массив значений полей (порядок и кол-во должно совпадать с маиссивом названий
			$id) // индекс строки, которую обновляем
	{
		global $DB;
		foreach ($arrFields as $key=>$field)
			$arrVal[] = $field."=".$arrValues[$key];
		
		$str="UPDATE ".$TableName." SET ".implode(",", $arrVal)." WHERE id=".$id; 
		$SQL = $DB->query($str, true);
		return $SQL;
	}
	//=========================================================================================================
	
	public static function AddRows(
			$TableName, //имя таблицы
			$arrFields, //массив названий полей
			$arrValues //массив значений полей (порядок и кол-во должно совпадать с маиссивом названий
	)
	{
	global $DB;
		$str="INSERT INTO ".$TableName." (".implode(",", $arrFields).") VALUES (".implode(",", $arrValues).")"; 
		$SQL = $DB->query($str, true);
		if ($SQL)
			return $DB->lastID();	
		else 
			return 0;
	}
	public static function DelRows($tableName, $arrWhere)
	{
	global $DB;
	$strWhere = "";
		if (!empty($arrWhere))
			{
			
				foreach ($arrWhere as $n=>$val)
					{
						
						if (empty($strWhere))
							{	if (is_array($val))
									$strWhere = " WHERE ".$n." in (".implode(",",$val).")";
								else
									$strWhere = " WHERE ".$n."='".$val."'"; 
							}
						else	
							{	if (is_array($val))
									$strWhere.= " AND ".$n." in (".implode(",",$val).")";
								else
									$strWhere.= " AND ".$n."='".$val."'"; 
							}
					}
			}
	
		$str = "DELETE FROM ".$tableName.$strWhere;
		//$this->err_str = "DELETE FROM ".$tableName.$strWhere;
		$DB->query($str, true);
		return true;	
	}
}
?>