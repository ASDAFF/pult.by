<?
namespace Dm;

class CurlJsonRequest
{
	protected $login;
	protected $password;
	protected $address_for_send;
	public $t_req;

	public function setLogin($login)
	{
		$this->login = $login;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function setAddress4Send($domain = "api.bepaid.by")
	{
		$this->address_for_send = "https://".$domain."/beyag/payments";
	}

	public function getAddress4Send()
	{
		return $this->address_for_send;
	}

	public function submit()
	{
        $process = curl_init($this->getAddress4Send());
        $json = json_encode($this->t_req);

        if (!empty($this->t_req))
    		{
          curl_setopt($process, CURLOPT_HTTPHEADER, array("Accept: application/json", "Content-type: application/json"));
          curl_setopt($process, CURLOPT_POST, 1);
          curl_setopt($process, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($process, CURLOPT_URL, $this->getAddress4Send());
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
    		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($process);
        $error = curl_error($process);
        $error_no = curl_errno($process);
        curl_close($process);

        if ($response === FALSE || $error_no > 0)
    		{
          throw new \Exception("cURL error " . $error);
        }
        return $response;
    }
}
