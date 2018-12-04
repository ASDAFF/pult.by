<?php

use Bitrix\Main\Loader,
		Bitrix\Catalog,
		Bitrix\Iblock,
		Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
//CBitrixComponent::includeComponentClass("bitrix:sale.order.ajax");
class RecallComponent extends CBitrixComponent //SaleOrderAjax
{
	private $element = array();

	private $bxajaxid = false;

	private $idForm = false;

	private $isName = false;

	private $isPhone = false;

	private $isEmail = false;

	private $isMessage = false;

	private $isCaptcha = false;

	private $isOneClick = false;

	private $name;

	private $phone;

	private $email;

	private $message;

	private $productId;

	private $captchaCode;

	private $captchaWord;

	private $user = null;

	private $application = null;


	public function executeComponent()
	{
		if($this->isSubmit())
		{
			$this->getPost();
			//if($this->arParams['ONECLICK_ORDER'] == 'Y')
				//$this->addOrder();
			$this->send();

			$this->returnData();

			if($this->isAjax())
			{
				$this->application()->RestartBuffer();
				$this->includeComponentTemplate('ajax_template');
				die;
			}
		}
		elseif($this->requestSuccess())
		{
			$this->arResult['OK_MESSAGE'] = $this->arParams['OK_TEXT'];
		}

		$this->returnData();

		if($this->isAjax())
		{
			$this->application()->RestartBuffer();
		}

		if($this->requestSuccess() && $this->isAjax())
		{
			$this->includeComponentTemplate('ajax_template');
		}
		else
		{
			$this->IncludeComponentTemplate();
		}

		if($this->isAjax())
		{
			die;
		}
	}

	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);

		$this->arResult['PARAMS_HASH'] = md5(serialize($params).$this->GetTemplateName());

		$params['USE_CAPTCHA'] = (($params['USE_CAPTCHA'] != 'N' && !$this->user()->IsAuthorized()) ? 'Y' : 'N');

		$params['USE_ONECLICK'] = (!isset($params['USE_ONECLICK']))? 'N' : $params['USE_ONECLICK'];

		$params['ONECLICK_ORDER'] = (!isset($params['ONECLICK_ORDER']))? 'N' : $params['ONECLICK_ORDER'];

		$params['USE_MASK'] = (!isset($params['USE_MASK']))? 'N' : $params['USE_MASK'];

		$this->isOneClick = ($params['ONECLICK_ORDER'] == 'Y');

		if($params['ONECLICK_ORDER'] == 'Y')
		{
			$params['ONECLICK_ORDER'] == 'N';
		}

		$params['EVENT_NAME'] = trim($params['EVENT_NAME']);
		if($params['EVENT_NAME'] == '')
			$params['EVENT_NAME'] = 'RECALL_FORM';
		$params['EMAIL_TO'] = trim($params['EMAIL_TO']);
		if($params['EMAIL_TO'] == '')
			$params['EMAIL_TO'] = COption::GetOptionString('main', 'email_from');
		$params['OK_TEXT'] = trim($params['OK_TEXT']);
		if($params['OK_TEXT'] == '')
			$params['OK_TEXT'] = Loc::getMessage('MF_OK_MESSAGE');

		if(in_array('NAME', $params['INCLUDE_FIELDS']))
			$this->isName = true;

		if(in_array('PHONE', $params['INCLUDE_FIELDS']))
			$this->isPhone = true;

		if(in_array('EMAIL', $params['INCLUDE_FIELDS']))
			$this->isEmail = true;

		if(in_array('MESSAGE', $params['INCLUDE_FIELDS']))
			$this->isMessage = true;

		if($params['USE_CAPTCHA'] == 'Y')
			$this->isCaptcha = true;

		if($this->isAjax())
			$params['AJAX'] = 'Y';

		return $params;
	}

	private function getPost()
	{
		if($this->isName)
			$this->name = htmlspecialcharsbx($this->ConvertCharset($this->request->get('user_name')));
		if($this->isPhone)
			$this->phone = htmlspecialcharsbx($this->request->get('user_phone'));
		if($this->isEmail)
			$this->email = htmlspecialcharsbx($this->request->get('user_email'));
		if($this->isMessage)
			$this->message = htmlspecialcharsbx($this->ConvertCharset($this->request->get('MESSAGE')));
		if($this->isOneClick) {
			$this->productId = intval($this->request->get('ELEMENT_ID'));
		}

		if($this->isCaptcha)
		{
			$this->captchaCode = $this->request->get('captcha_sid');
			$this->captchaWord = $this->request->get('captcha_word');
		}
	}

	/*private function addOrder()
	{
		$this->application()->RestartBuffer();
		$res = $this->createOrder($this->user()->GetID() ? $this->user()->GetID() : CSaleUser::GetAnonymousUserID());
		$this->saveOrder(false);
	}*/

	private function ConvertCharset($field)
	{
		return ($this->isAjax() && SITE_CHARSET != 'utf-8')? $this->application()->ConvertCharset($field, 'utf-8', SITE_CHARSET) : $field;
	}

	private function send()
	{
		$this->arResult['ERROR_MESSAGE'] = array();
		if(check_bitrix_sessid())
		{
			if(empty($this->arParams['REQUIRED_FIELDS']) || !in_array('NONE', $this->arParams['REQUIRED_FIELDS']))
			{
				if((empty($this->arParams['REQUIRED_FIELDS']) || in_array('NAME', $this->arParams['REQUIRED_FIELDS'])) && strlen($this->name) <= 1)
					$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_REQ_NAME');
				if((empty($this->arParams['REQUIRED_FIELDS']) || in_array('PHONE', $this->arParams['REQUIRED_FIELDS'])) && strlen($this->phone) <= 1)
					$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_REQ_PHONE');
				if((empty($this->arParams['REQUIRED_FIELDS']) || in_array('EMAIL', $this->arParams['REQUIRED_FIELDS'])) && strlen($this->email) <= 1)
					$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_REQ_EMAIL');
				if((empty($this->arParams['REQUIRED_FIELDS']) || in_array('MESSAGE', $this->arParams['REQUIRED_FIELDS'])) && strlen($this->message) <= 3)
					$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_REQ_MESSAGE');
			}
			if(strlen($this->email) > 1 && !check_email($this->email))
				$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_EMAIL_NOT_VALID');
			if($this->isCaptcha)
			{
				include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/captcha.php');

				$cpt = new CCaptcha();
				$captchaPass = COption::GetOptionString('main', 'captcha_password', '');
				if (strlen($this->captchaWord) > 0 && strlen($this->captchaCode) > 0)
				{
					if (!$cpt->CheckCodeCrypt($this->captchaWord, $this->captchaCode, $captchaPass))
						$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_CAPTCHA_WRONG');
				}
				else
					$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_CAPTHCA_EMPTY');

			}			
			if(empty($this->arResult['ERROR_MESSAGE']))
			{
				$arFields = Array();
				if($this->isName)
					$arFields['AUTHOR'] = $this->name;
				if($this->isPhone)
					$arFields['AUTHOR_PHONE'] = $this->phone;
				if($this->isEmail)
					$arFields['AUTHOR_EMAIL'] = $this->email;
				if($this->isOneClick) {
					$this->setElementFields($arFields);
				}
				if($this->isMessage)
					$arFields['TEXT'] = $this->message;

				$arFields['EMAIL_TO'] = $this->arParams['EMAIL_TO'];

				if(!empty($this->arParams['EVENT_MESSAGE_ID']))
				{
					foreach($this->arParams['EVENT_MESSAGE_ID'] as $v)
						if(IntVal($v) > 0)
							CEvent::Send($this->arParams['EVENT_NAME'], SITE_ID, $arFields, 'N', IntVal($v));
				}
				else
					CEvent::Send($this->arParams['EVENT_NAME'], SITE_ID, $arFields);

				if($this->isName)
					$_SESSION['MF_NAME'] = $this->name;
				if($this->isPhone)
					$_SESSION['MF_PHONE'] = $this->phone;
				if($this->isEmail)
					$_SESSION['MF_EMAIL'] = $this->email;

				LocalRedirect($this->application()->GetCurPageParam('success='.$this->arResult['PARAMS_HASH'], Array('success')));
			}
			
			if($this->isName)
				$this->arResult['AUTHOR_NAME'] = $this->name;
			if($this->isPhone)
				$this->arResult['AUTHOR_PHONE'] = $this->phone;
			if($this->isEmail)
				$this->arResult['AUTHOR_EMAIL'] = $this->email;
			if($this->isMessage)
				$this->arResult['MESSAGE'] = $this->message;
		}
		else
			$this->arResult['ERROR_MESSAGE'][] = Loc::getMessage('MF_SESS_EXP');
	}

	private function getElement()
	{
		if(empty($this->element))
		{
			$res = CIBlockElement::GetByID($this->productId);
			if($item = $res->GetNext()) {
				$this->element = $item;
			}
		}
		return $this->element;
	}

	private function setElementFields(&$arFields)
	{
		$item = $this->getElement();
		if(!empty($item)) {
			$arFields['PRODUCT_NAME'] = $item['NAME'];
			$arFields['PRODUCT_LINK'] = $item['DETAIL_PAGE_URL'];
		}
	}

	private function returnData()
	{
		if(empty($this->arResult['ERROR_MESSAGE']))
		{
			if($this->user()->IsAuthorized())
			{
				if($this->isName)
					$this->arResult['AUTHOR_NAME'] = $this->user()->GetFormattedName(false);
				if($this->isEmail)
					$this->arResult['AUTHOR_EMAIL'] = htmlspecialcharsbx($this->user()->GetEmail());
			}
			else
			{
				if($this->isName && strlen($_SESSION['MF_NAME']) > 0)
					$arResult['AUTHOR_NAME'] = htmlspecialcharsbx($_SESSION['MF_NAME']);
				if($this->isEmail && strlen($_SESSION['MF_EMAIL']) > 0)
					$arResult['AUTHOR_EMAIL'] = htmlspecialcharsbx($_SESSION['MF_EMAIL']);
			}
			if($this->isPhone && strlen($_SESSION['MF_PHONE']) > 0)
					$this->arResult['AUTHOR_PHONE'] = htmlspecialcharsbx($_SESSION['MF_PHONE']);
		}

		if($this->isCaptcha)
			$this->arResult['capCode'] =  htmlspecialcharsbx($this->application()->CaptchaGetCode());
	}

	private function requestSuccess()
	{
		return $this->request->get('success') == $this->arResult['PARAMS_HASH'];
	}

	private function user()
	{
		if(is_null($this->user))
		{
			global $USER;
			$this->user = $USER;
		}
		return $this->user;
	}

	private function application()
	{
		if(is_null($this->application))
		{
			global $APPLICATION;
			$this->application = $APPLICATION;
		}
		return $this->application;
	}

	private function isSubmit()
	{
		return $this->request->isPost() && $this->request->get('action') == 'recall' && (!isset($_POST['PARAMS_HASH']) || $this->arResult['PARAMS_HASH'] === $this->request->get('PARAMS_HASH'));
	}

	public function getBxAjaxId()
	{
		if(!$this->bxajaxid)
		{
			$this->bxajaxid = CAjax::GetComponentID($this->getName(),$this->getTemplateName(), false);
		}
		return $this->bxajaxid;
	}

	public function getIdForm()
	{
		if(!$this->idForm)
		{
			$this->idForm = $this->randString();
		}
		return $this->idForm;
	}

	/**
	 * Is AJAX Request?
	 * @return bool
	 */
	protected function isAjax()
	{
		return $this->request->isAjaxRequest() && $this->getBxAjaxId() == $this->request->get('form_popup');
	}
}