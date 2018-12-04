<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Profile {
	protected static $dbIsPrepared = false;
	protected static $instance = null;
	private $errors = array();
	private $entity = false;
	
	function __construct($suffix='')
	{
		static::PrepareDB();
		$this->suffix = $suffix;
		$this->pathProfiles = dirname(__FILE__).'/../../profiles'.(strlen($suffix) > 0 ? '_'.$suffix : '').'/';
		$this->CheckStorage();
		
		$this->tmpdir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/esol.importxml/';
		CheckDirPath($this->tmpdir);
		$this->uploadDir = $_SERVER["DOCUMENT_ROOT"].'/upload/esol.importxml/';
		CheckDirPath($this->uploadDir);
		
		$this->tmpdir = realpath($this->tmpdir).'/';
		$this->uploadDir = realpath($this->uploadDir).'/';
		
		if(!is_writable($this->tmpdir)) $this->errors[] = sprintf(Loc::getMessage('ESOL_IX_DIR_NOT_WRITABLE'), $this->tmpdir);
		if(!is_writable($this->uploadDir)) $this->errors[] = sprintf(Loc::getMessage('ESOL_IX_DIR_NOT_WRITABLE'), $this->uploadDir);
	}
	
	public static function getInstance()
	{
		if (!isset(static::$instance))
			static::$instance = new static();

		return static::$instance;
	}
	
	public static function PrepareDB()
	{
		if(!static::$dbIsPrepared)
		{
			$profileDB = new \Bitrix\EsolImportxml\ProfileTable();
			$conn = $profileDB->getEntity()->getConnection();
			if(is_callable(array($conn, 'queryExecute')))
			{
				$conn->queryExecute('SET wait_timeout=900');
			}
			static::$dbIsPrepared = true;
		}
	}
	
	public function CheckStorage()
	{
		$profileEntity = $this->GetEntity();
		$tblName = $profileEntity->getTableName();
		$conn = $profileEntity->getEntity()->getConnection();
		if(!$conn->isTableExists($tblName))
		{
			$profileEntity->getEntity()->createDbTable();
			$conn->query('ALTER TABLE `'.$tblName.'` CHANGE COLUMN `PARAMS` `PARAMS` mediumtext DEFAULT NULL');
			$conn->query('ALTER TABLE `'.$tblName.'` CHANGE COLUMN `DATE_START` `DATE_START` datetime DEFAULT NULL');
			$conn->query('ALTER TABLE `'.$tblName.'` CHANGE COLUMN `SORT` `SORT` int(11) NOT NULL DEFAULT "500"');
			
			$this->CheckTableEncoding($conn, $tblName);
		}
		else
		{
			$isNewFields = false;
			$arDbFields = array();
			$dbRes = $conn->query("SHOW COLUMNS FROM `" . $tblName . "`");
			while($arr = $dbRes->Fetch())
			{
				$arDbFields[] = $arr['Field'];
			}
			$fields = $profileEntity->getEntity()->getScalarFields();
			$helper = $conn->getSqlHelper();
			$prevField = 'ID';
			foreach($fields as $columnName => $field)
			{
				$realColumnName = $field->getColumnName();
				if(!in_array($realColumnName, $arDbFields))
				{
					$conn->query('ALTER TABLE '.$helper->quote($tblName).' ADD COLUMN '.$helper->quote($realColumnName).' '.$helper->getColumnTypeByField($field).' DEFAULT NULL AFTER '.$helper->quote($prevField));
					if($field->getDefaultValue())
					{
						$conn->query('UPDATE '.$helper->quote($tblName).' SET '.$helper->quote($realColumnName).'="'.$helper->forSql($field->getDefaultValue()).'"');
					}
					$isNewFields = true;
				}
				$prevField = $realColumnName;
			}
			if($isNewFields) $this->CheckTableEncoding($conn, $tblName);
		}
	}
	
	private function CheckTableEncoding($conn, $tblName)
	{
		$res = $conn->query('SHOW VARIABLES LIKE "character_set_database"');
		$f = $res->fetch();
		$charset = trim($f['Value']);

		$res = $conn->query('SHOW VARIABLES LIKE "collation_database"');
		$f = $res->fetch();
		$collation = trim($f['Value']);
		
		$res0 = $conn->query('SHOW CREATE TABLE `' . $tblName . '`');
		$f0 = $res0->fetch();
		
		if (preg_match('/DEFAULT CHARSET=([a-z0-9\-_]+)/i', $f0['Create Table'], $regs))
		{
			$t_charset = $regs[1];
			if (preg_match('/COLLATE=([a-z0-9\-_]+)/i', $f0['Create Table'], $regs))
				$t_collation = $regs[1];
			else
			{
				$res0 = $conn->query('SHOW CHARSET LIKE "' . $t_charset . '"');
				$f0 = $res0->fetch();
				$t_collation = $f0['Default collation'];
			}
		}
		else
		{
			$res0 = $conn->query('SHOW TABLE STATUS LIKE "' . $tblName . '"');
			$f0 = $res0->fetch();
			if (!$t_collation = $f0['Collation'])
				return;
			$t_charset = $this->GetCharsetByCollation($conn, $t_collation);
		}
		
		if ($charset != $t_charset)
		{
			$conn->query('ALTER TABLE `' . $tblName . '` CHARACTER SET ' . $charset);
		}
		elseif ($t_collation != $collation)
		{	// table collation differs
			$conn->query('ALTER TABLE `' . $tblName . '` COLLATE ' . $collation);
		}
		
		$arFix = array();
		$res0 = $conn->query("SHOW FULL COLUMNS FROM `" . $tblName . "`");
		while($f0 = $res0->fetch())
		{
			$f_collation = $f0['Collation'];
			if ($f_collation === NULL || $f_collation === "NULL")
				continue;

			$f_charset = $this->GetCharsetByCollation($conn, $f_collation);
			if ($charset != $f_charset)
			{
				$arFix[] = ' MODIFY `'.$f0['Field'].'` '.$f0['Type'].' CHARACTER SET '.$charset.($f0['Null'] == 'YES' ? ' NULL' : ' NOT NULL').
						($f0['Default'] === NULL ? ($f0['Null'] == 'YES' ? ' DEFAULT NULL ' : '') : ' DEFAULT '.($f0['Type'] == 'timestamp' && $f0['Default'] == 'CURRENT_TIMESTAMP' ? $f0['Default'] : '"'.$conn->getSqlHelper()->forSql($f0['Default']).'"')).' '.$f0['Extra'];
			}
			elseif ($collation != $f_collation)
			{
				$arFix[] = ' MODIFY `'.$f0['Field'].'` '.$f0['Type'].' COLLATE '.$collation.($f0['Null'] == 'YES' ? ' NULL' : ' NOT NULL').
						($f0['Default'] === NULL ? ($f0['Null'] == 'YES' ? ' DEFAULT NULL ' : '') : ' DEFAULT '.($f0['Type'] == 'timestamp' && $f0['Default'] == 'CURRENT_TIMESTAMP' ? $f0['Default'] : '"'.$conn->getSqlHelper()->forSql($f0['Default']).'"')).' '.$f0['Extra'];
			}
		}

		if(count($arFix))
		{
			$conn->query('ALTER TABLE `'.$table.'` '.implode(",\n", $arFix));
		}
	}
	
	private function GetCharsetByCollation($conn, $collation)
	{
		static $CACHE;
		if (!$c = &$CACHE[$collation])
		{
			$res0 = $conn->query('SHOW COLLATION LIKE "' . $collation . '"');
			$f0 = $res0->Fetch();
			$c = $f0['Charset'];
		}
		return $c;
	}
	
	private function GetEntity()
	{
		if(!$this->entity)
		{
			$this->entity = new \Bitrix\EsolImportxml\ProfileTable();
		}
		return $this->entity;
	}
	
	public function GetList()
	{
		$arProfiles = array();
		$profileEntity = $this->GetEntity();
		$dbRes = $profileEntity::getList(array('select'=>array('ID', 'NAME'), 'order'=>array('SORT'=>'ASC', 'ID'=>'ASC')));
		while($arr = $dbRes->Fetch())
		{
			$arProfiles[$arr['ID'] - 1] = $arr['NAME'];
		}
		
		return $arProfiles;
	}
	
	public function GetByID($ID)
	{
		$profileEntity = $this->GetEntity();
		$arProfile = $profileEntity::getList(array('filter'=>array('ID'=>($ID + 1)), 'select'=>array('PARAMS')))->fetch();
		if($arProfile && $arProfile['PARAMS'])
		{
			$arProfile = unserialize($arProfile['PARAMS']);
		}
		if(!is_array($arProfile)) $arProfile = array();
		
		return $arProfile;
	}
	
	public function GetFieldsByID($ID)
	{
		$profileEntity = $this->GetEntity();
		$arProfile = $profileEntity::getList(array('filter'=>array('ID'=>($ID + 1))))->fetch();
		unset($arProfile['PARAMS']);
		
		return $arProfile;
	}
	
	public function Add($name)
	{
		global $APPLICATION;
		$APPLICATION->ResetException();
		
		$name = trim($name);
		if(strlen($name)==0)
		{
			$APPLICATION->throwException(Loc::getMessage("ESOL_IX_NOT_SET_PROFILE_NAME"));
			return false;
		}
		
		$profileEntity = $this->GetEntity();
		
		if($arProfile = $profileEntity::getList(array('filter'=>array('NAME'=>$name), 'select'=>array('ID')))->fetch())
		{
			$APPLICATION->throwException(Loc::getMessage("ESOL_IX_PROFILE_NAME_EXISTS"));
			return false;
		}
		
		$dbRes = $profileEntity::add(array('NAME'=>$name));
		if(!$dbRes->isSuccess())
		{
			$error = '';
			if($dbRes->getErrors())
			{
				foreach($dbRes->getErrors() as $errorObj)
				{
					$error .= $errorObj->getMessage().'. ';
				}
				$APPLICATION->throwException($error);
			}
			return false;
		}
		else
		{
			$ID = $dbRes->getId() - 1;			
			return $ID;
		}
	}
	
	public function Update($ID, $settigs_default, $settings)
	{
		$arProfile = $this->GetByID($ID);
		if(is_array($settigs_default))
		{
			$arProfile['SETTINGS_DEFAULT'] = $settigs_default;
		}
		if(is_array($settings))
		{
			$arProfile['SETTINGS'] = $settings;
		}
		$profileEntity = $this->GetEntity();
		$profileEntity::update(($ID+1), array('PARAMS'=>serialize($arProfile)));
	}
	
	public function UpdateExtra($ID, $extrasettings)
	{
		$arProfile = $this->GetByID($ID);
		if(!is_array($extrasettings)) $extrasettings = array();
		$arProfile['EXTRASETTINGS'] = $extrasettings;
		$profileEntity = $this->GetEntity();
		$profileEntity::update(($ID+1), array('PARAMS'=>serialize($arProfile)));
	}
	
	public function Delete($ID)
	{
		$profileEntity = $this->GetEntity();
		$profileEntity::delete($ID+1);
	}
	
	public function Copy($ID)
	{
		$profileEntity = $this->GetEntity();
		$arProfile = $profileEntity::getList(array('filter'=>array('ID'=>($ID + 1)), 'select'=>array('NAME', 'PARAMS')))->fetch();
		if(!$arProfile) return false;
		
		$newName = $arProfile['NAME'].Loc::getMessage("ESOL_IX_PROFILE_COPY");
		$dbRes = $profileEntity::add(array('NAME'=>$newName, 'PARAMS'=>$arProfile['PARAMS']));
		if(!$dbRes->isSuccess())
		{
			$error = '';
			if($dbRes->getErrors())
			{
				foreach($dbRes->getErrors() as $errorObj)
				{
					$error .= $errorObj->getMessage().'. ';
				}
				$APPLICATION->throwException($error);
			}
			return false;
		}
		else
		{
			$newId = $dbRes->getId() - 1;			
			return $newId;
		}
	}
	
	public function Rename($ID, $name)
	{
		global $APPLICATION;
		$APPLICATION->ResetException();
		
		$name = trim($name);
		if(strlen($name)==0)
		{
			$APPLICATION->throwException(Loc::getMessage("ESOL_IX_NOT_SET_PROFILE_NAME"));
			return false;
		}
		$profileEntity = $this->GetEntity();
		$profileEntity::update(($ID+1), array('NAME'=>$name));
	}
	
	public function UpdateFields($ID, $arFields)
	{
		$profileEntity = $this->GetEntity();
		$profileEntity::update(($ID+1), $arFields);
	}
	
	public function UpdateFileHash($ID, $file)
	{
		$hash = md5_file($file);
		$this->UpdateFields($ID, array('FILE_HASH'=>$hash));
	}
	
	public function ApplyToLists($ID, $listFrom, $listTo)
	{
		if(!is_numeric($listFrom) || !is_array($listTo) || count($listTo)==0) return;
		$listTo = preg_grep('/^\d+$/', $listTo);
		if(count($listTo)==0) return;
		
		$arParams = $this->GetByID($ID);
		foreach($listTo as $key)
		{
			$arParams['SETTINGS']['FIELDS_LIST'][$key] = $arParams['SETTINGS']['FIELDS_LIST'][$listFrom];
			$arParams['EXTRASETTINGS'][$key] = $arParams['EXTRASETTINGS'][$listFrom];
		}
		$profileEntity = $this->GetEntity();
		$profileEntity::update(($ID+1), array('PARAMS'=>serialize($arParams)));
	}
	
	public function GetProcessedProfiles()
	{
		$arProfiles = $this->GetList();
		foreach($arProfiles as $k=>$v)
		{
			$tmpfile = $this->tmpdir.$k.($this->suffix ? '_'.$this->suffix : '').'.txt';
			if(!file_exists($tmpfile) || filesize($tmpfile)>10*1024 || (time() - filemtime($tmpfile) < 4*60) || filemtime($tmpfile) < mktime(0, 0, 0, 12, 24, 2015))
			{
				unset($arProfiles[$k]);
				continue;
			}
			
			$arParams = \CUtil::JsObjectToPhp(file_get_contents($tmpfile));
			$percent = round(((int)$arParams['total_read_line'] / (int)$arParams['total_file_line']) * 100);
			$percent = min($percent, 99);
			$arProfiles[$k] = array(
				'key' => $k,
				'name' => $v,
				'percent' => $percent
			);
		}
		if(!is_array($arProfiles)) $arProfiles = array();
		return $arProfiles;
	}
	
	public function RemoveProcessedProfile($id)
	{
		$tmpfile = $this->tmpdir.$id.($this->suffix ? '_'.$this->suffix : '').'.txt';
		if(file_exists($tmpfile))
		{
			$arParams = \CUtil::JsObjectToPhp(file_get_contents($tmpfile));
			if($arParams['tmpdir'])
			{
				DeleteDirFilesEx(substr($arParams['tmpdir'], strlen($_SERVER['DOCUMENT_ROOT'])));
			}
			unlink($tmpfile);
		}
	}
	
	public function GetProccessParams($id)
	{
		$tmpfile = $this->tmpdir.$id.($this->suffix ? '_'.$this->suffix : '').'.txt';
		if(file_exists($tmpfile))
		{
			$arParams = \CUtil::JsObjectToPhp(file_get_contents($tmpfile));
			$paramFile = $arParams['tmpdir'].'params.txt';
			$arParams = unserialize(file_get_contents($paramFile));
			return $arParams;
		}
		return false;
	}
	
	public function GetProccessParamsFromPidFile($id)
	{
		$tmpfile = $this->tmpdir.$id.'.txt';
		if(file_exists($tmpfile))
		{
			if(time() - filemtime($tmpfile) < 3*60)
			{
				return false;
			}
			$arParams = \CUtil::JsObjectToPhp(file_get_contents($tmpfile));
			return $arParams;
		}
		return array();
	}
	
	public function GetErrors()
	{
		if(!isset($this->errors) || !is_array($this->errors)) $this->errors = array();
		return implode('<br>', array_unique($this->errors));
	}
	
	public function ShowProfileList($fname)
	{
		$arProfiles = $this->GetList();
		?><select name="<?echo $fname;?>" id="<?echo $fname;?>" onchange="EProfile.Choose(this)"><?
			?><option value=""><?echo Loc::getMessage("ESOL_IX_NO_PROFILE"); ?></option><?
			?><option value="new" <?if($_REQUEST[$fname]=='new'){echo 'selected';}?>><?echo Loc::getMessage("ESOL_IX_NEW_PROFILE"); ?></option><?
			foreach($arProfiles as $k=>$profile)
			{
				?><option value="<?echo $k;?>" <?if(strlen($_REQUEST[$fname])>0 && strval($_REQUEST[$fname])===strval($k)){echo 'selected';}?>><?echo $profile; ?></option><?
			}
		?></select><?
	}
	
	public function Apply(&$settigs_default, &$settings, $ID)
	{
		$arProfile = $this->GetByID($ID);
		if(!is_array($settigs_default) && is_array($arProfile['SETTINGS_DEFAULT']))
		{
			$settigs_default = $arProfile['SETTINGS_DEFAULT'];
		}
		if(!is_array($settings) && is_array($arProfile['SETTINGS']))
		{
			$settings = $arProfile['SETTINGS'];
		}
		if(is_array($settings))
		{
			if($settings['ADDITIONAL_SETTINGS'])
			{
				foreach($settings['ADDITIONAL_SETTINGS'] as $k=>$v)
				{
					if($v && !is_array($v))
					{
						$v = \CUtil::JsObjectToPhp($v);
					}
					if(!is_array($v)) $v = array();
					$settings['ADDITIONAL_SETTINGS'][$k] = $v;
				}
			}
		}
	}
	
	public function ApplyExtra(&$extrasettings, $ID)
	{
		$arProfile = $this->GetByID($ID);
		if(!is_array($extrasettings) && is_array($arProfile['EXTRASETTINGS']))
		{
			$extrasettings = $arProfile['EXTRASETTINGS'];
		}
	}
}