<?
IncludeModuleLangFile(__FILE__);

function esol_importxml_demo_expired() {
	$DemoMode = CModule::IncludeModuleEx("esol.importxml");
	if ($DemoMode==MODULE_DEMO) {
		$now=time();
		if (defined("esol_importxml_OLDSITEEXPIREDATE")) {
			if ($now>=esol_importxml_OLDSITEEXPIREDATE || esol_importxml_OLDSITEEXPIREDATE>$now+3000000) {
				return true;
			}
		} else{ 
			return true;
		}
	} elseif ($DemoMode==MODULE_DEMO_EXPIRED) {
		return true;
	}
	return false;
}

function esol_importxml_show_demo($demoForce = false) {
	$DemoMode = CModule::IncludeModuleEx("esol.importxml");
	if ($DemoMode==MODULE_DEMO) {
		$now=time();
		if (defined("esol_importxml_OLDSITEEXPIREDATE")) {
			if ($now<esol_importxml_OLDSITEEXPIREDATE) {
				print BeginNote();
				$expire_arr = getdate(esol_importxml_OLDSITEEXPIREDATE);
				$expire_date = gmmktime($expire_arr["hours"],$expire_arr["minutes"],$expire_arr["seconds"],$expire_arr["mon"],$expire_arr["mday"],$expire_arr["year"]);
				$now_arr = getdate($now);
				$now_date = gmmktime($expire_arr["hours"],$expire_arr["minutes"],$expire_arr["seconds"],$now_arr["mon"],$now_arr["mday"],$now_arr["year"]);
				$days = ($expire_date-$now_date)/86400; 
				print GetMessage("ESOL_IMPORTXML_DEMO_MESSAGE_DAYS_REMAIN",array("#DAYS#"=>$days));
				print EndNote();
			} else {
				print BeginNote();
				print GetMessage("ESOL_IMPORTXML_DEMO_MESSAGE_EXPIRED");
				print EndNote();
			}
		} else{ 
			print BeginNote();
			print GetMessage("ESOL_IMPORTXML_DEMO_MESSAGE_EXPIRED");
			print EndNote();
		}
	} elseif ($DemoMode==MODULE_DEMO_EXPIRED || $demoForce) {
		print BeginNote();
		print GetMessage("ESOL_IMPORTXML_DEMO_MESSAGE_EXPIRED");
		print EndNote();
	}
}

?>