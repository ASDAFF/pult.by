<?
namespace Dm;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(dirname(__FILE__) . '/../install/index.php');

class Money
{
	protected $amount;
	protected $currency;

	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	public function getAmount()
	{
		$amount = ($this->currency == "BYR") ? $this->amount : $this->amount * 100;
    return intval(strval($amount));
	}

	public function setCurrency($currency)
	{
		$allowed_currency = array("BYR", "BYN");

		if(!in_array($currency,$allowed_currency))
			throw new \Exception(sprintf(Loc::getMessage("DEVTM_ERIP_PRICE_CURRENCY_ERROR"), $currency));

		$this->currency = $currency;
	}

	public function getCurrency()
	{
		return $this->currency;
	}
}
