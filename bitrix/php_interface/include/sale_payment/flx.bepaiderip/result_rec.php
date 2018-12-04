<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$module_id = "flx.bepaiderip";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$login = \Bitrix\Main\Config\Option::get( $module_id, "shop_id" );
$password = \Bitrix\Main\Config\Option::get( $module_id, "shop_key" );

$webhook = new \DM\Webhook($login, $password);

if($webhook->isAuthorized()) 
{
	$response = $webhook->response;
	if(isset($response->transaction))
	{
		$transaction = $response->transaction;
		if($transaction->status == "successful")
		{
			$id = (int)$transaction->erip->account_number;
			$order = CSaleOrder::GetByID($id);
			if($order["ID"] > 0 && $order["PAYED"] != "Y")
			{
				$fields = array("COMMENTS" => "status: ". $transaction->status ."\n".
												"message: ".$transaction->message ."\n".
												"uid: ". $transaction->uid ."\n".
												"order_id: ". $transaction->order_id ."\n".
												"account_number: ". $id ."\n",
								"STATUS_ID" => "P",
								"PAYED" => "Y"
						  );
				CSaleOrder::Update($order["ID"], $fields);
				echo "OK";
			}
		}
	}
}
$GLOBALS["APPLICATION"]->RestartBuffer();
die();
?>
