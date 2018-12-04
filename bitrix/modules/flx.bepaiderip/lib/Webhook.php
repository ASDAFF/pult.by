<?
namespace DM;

class Webhook
{
	protected $_json_in = "php://input";
	protected $login;
	protected $password;
	public $response;
	
	public function __construct( $login, $password )
	{
		$this->login = $login;
		$this->password = $password;
		$this->decodeReceivedJson();
	}

	public function isAuthorized() 
	{
		return $this->getLoginFromAuthorization() == $this->login
			   && $this->getPasswordFromAuthorization() == $this->password;
	}

	public function decodeReceivedJson() 
	{
		$this->response = json_decode(file_get_contents($this->_json_in));
	}

	private function getLoginFromAuthorization()
	{
		if (isset($_SERVER["PHP_AUTH_USER"]))
		  return $_SERVER["PHP_AUTH_USER"];
		return "";
	}

	private function getPasswordFromAuthorization()
	{
		if (isset($_SERVER["PHP_AUTH_PW"]))
		  return $_SERVER["PHP_AUTH_PW"];
		return "";
	}
}