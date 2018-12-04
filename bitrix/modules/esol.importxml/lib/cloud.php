<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Cloud
{
	protected $services = array(
		'yadisk' => '/^https?:\/\/yadi\.sk\//i',
		'mailru' => '/^https?:\/\/cloud\.mail\.ru\/public\//i'
	);
	
	public function GetService($link)
	{
		foreach($this->services as $k=>$v)
		{
			if(preg_match($v, $link)) return $k;
		}
		return false;
	}
	
	public function MakeFileArray($service, $path)
	{
		$method = ucfirst($service).'GetFile';
		if(!is_callable(array($this, $method))) return false;
		
		$urlComponents = parse_url($path);
		if ($urlComponents && strlen($urlComponents["path"]) > 0)
		{
			$urlComponents["path"] = urldecode($urlComponents['path']);
			$tmpPath = \CFile::GetTempName('', bx_basename($urlComponents["path"]));
		}
		else
			$tmpPath = \CFile::GetTempName('', bx_basename($path));

		$dir = \Bitrix\Main\IO\Path::getDirectory($tmpPath);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		if(call_user_func(array($this, $method), $tmpPath, $path))
		{
			$arFile = \CFile::MakeFileArray($tmpPath);
			if(strlen($arFile["type"])<=0)
				$arFile["type"] = "unknown";
			return $arFile;
		}
		else
		{
			return false;
		}
	}
	
	public function YadiskGetFile($tmpPath, $path)
	{
		$token = \Bitrix\Main\Config\Option::get('esol.importxml', 'YANDEX_APIKEY', '');
		if(!$token) return false;
		
		$arUrl = parse_url($path);
		$subPath = '';
		if(strpos($arUrl['path'], '/d/')===0 && preg_match('/^\/d\/[^\/]*\/./', $arUrl['path']))
		{
			$subPath = preg_replace('/^\/d\/[^\/]*\//', '/', $arUrl['path']);
			if($subPath && strlen($subPath) < strlen($arUrl['path']))
			{
				$path = substr($path, 0, -strlen($subPath));
			}
		}
		
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10));
		$client->setHeader('Authorization', "OAuth ".$token);
		$res = $client->get('https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key='.urlencode($path).(strlen($subPath) > 0 ? '&path='.urlencode($subPath) : ''));
		$arRes = \CUtil::JsObjectToPhp($res);
		if(is_array($arRes) && $arRes['href'])
		{
			$res = $client->get($arRes['href']);
			if($res)
			{
				file_put_contents($tmpPath, $res);
				return true;
			}
		}
		return false;
	}
	
	public function MailruGetFile($tmpPath, $path)
	{
		$mr = \Bitrix\EsolImportxml\Cloud\MailRu::GetInstance();
		return $mr->download($tmpPath, $path);
	}
}