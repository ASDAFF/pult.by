<?

$classes = array(
				"\DM\CurlJsonRequest" => "lib/CurlJsonRequest.php",
				"\DM\Costumer" => "lib/Costumer.php",
				"\DM\Erip" => "lib/Erip.php",
				"\DM\Money" => "lib/Money.php",
				"\DM\Webhook" => "lib/Webhook.php",
				"Handlers" => "lib/Handlers.php",
		   );

CModule::AddAutoloadClasses("flx.bepaiderip", $classes);