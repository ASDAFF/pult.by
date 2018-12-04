<?
namespace Dm;

class Erip extends CurlJsonRequest
{
	public $costumer;
	public $money;
	public $description;
	public $notification_url;
	public $account_number;
	public $service_number;
	public $service_info;
	public $receipt;
	public $order_id;
	public $expired_at;

	public function __construct()
	{
		$this->costumer = new \Dm\Costumer;
		$this->money = new \Dm\Money;
	}

	public function orderGenerate( $oid )
	{
		$oid = (int)$oid;
		$n = 100000000000;
		$this->order_id = $oid + $n;
	}

	protected function buildRequest()
	{
		return array(
					"request" => array(
									"amount" => $this->money->getAmount(),
									"currency" => $this->money->getCurrency(),
									"description" => \Dm\Erip::to_utf8($this->description),
									"email" => $this->costumer->email,
									"ip" => $this->costumer->ip,
									"order_id" => $this->order_id,
									"notification_url" => $this->notification_url,
									"expired_at" => \Dm\Erip::to_utf8($this->expired_at),
									"customer" => array(
													"first_name" => \Dm\Erip::to_utf8($this->costumer->first_name, 30),
													"last_name" => \Dm\Erip::to_utf8($this->costumer->last_name, 30),
													"country" => \Dm\Erip::to_utf8($this->costumer->getCountry()),
													"city" => \Dm\Erip::to_utf8($this->costumer->city),
													"zip" => \Dm\Erip::to_utf8($this->costumer->zip),
													"address" => \Dm\Erip::to_utf8($this->costumer->address, 255),
													"phone" => \Dm\Erip::to_utf8($this->costumer->phone),
												  ),
									"payment_method" => array(
															"type" => "erip",
															"account_number" => $this->account_number,
															"service_no" => $this->service_number,
															"service_info" => array(\Dm\Erip::to_utf8(str_replace('#INVOICE#', $this->account_number, $this->service_info))),
															"receipt" => array(\Dm\Erip::to_utf8(str_replace('#INVOICE#', $this->account_number, $this->receipt))),
														)
								)
				);
	}

	public function submit()
	{
		$this->t_req = $this->buildRequest();
		$p = parent::submit();
		return $p;
	}

  public static function to_utf8($param, $size = 0)
  {
    $in = $param;
    if ($size > 0) {
      $in = substr($in, 0, $size);
    }
    if (strtolower(LANG_CHARSET) == 'windows-1251') {
      $in = mb_convert_encoding($in, 'UTF-8', 'WINDOWS-1251');
    }
    return $in;
  }
}
