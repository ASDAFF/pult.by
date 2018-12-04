<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class flx_bepaiderip extends CModule
{
	var $MODULE_ID = "flx.bepaiderip";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS = "N";

	protected $namespaceFolder = "flx";

	protected $lang_ids = array();

	protected $mail_event_name = "SALE_STATUS_CHANGED_ER";

    function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = Loc::getMessage("DEVTM_ERIP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("DEVTM_ERIP_MODULE_DESC");
    $this->PARTNER_NAME = "unt";
    $this->PARTNER_URI = "https://unt.by/";

		$this->setLangIds();

		\Bitrix\Main\Loader::includeModule("sale");
	}

	protected function setLangIds()
	{
		$db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
		while ($lang = $db_lang->Fetch())
			$this->lang_ids[] = $lang["LID"];
	}

	protected function addPaysys()
	{
		return CSalePaySystem::Add(
									array(
										"NAME" => Loc::getMessage("DEVTM_ERIP_PS_NAME"),
										"DESCRIPTION" => Loc::getMessage("DEVTM_ERIP_PS_DESC"),
										"ACTIVE" => "Y",
										"SORT" => 100,
									)
								);
	}

	protected function deletePaysys()
	{
		$ps_id = (int)\Bitrix\Main\Config\Option::get( $this->MODULE_ID, "payment_system_id");

		$order = CSaleOrder::GetList(array(), array("PAY_SYSTEM_ID" => $ps_id))->Fetch();
		if($order["ID"] > 0)
			throw new Exception(Loc::getMessage("DEVTM_ERIP_DELETE_PAMENT_ERROR"));

    // verify that there is a payment system to delete
    if ($arPaySys = CSalePaySystem::GetByID($ps_id))
    {
  		if(!CSalePaySystem::Delete($ps_id))
  			throw new Exception(Loc::getMessage("DEVTM_ERIP_DELETE_PAMENT2_ERROR"));
    }

		return true;
	}

	protected function copyHandlerFiles()
	{
		return CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/sale_payment/",
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment",
					true, true
				);
	}

	protected function deleteHandlerFiles()
	{
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/". $this->MODULE_ID);
		return true;
	}

	protected function addPaysysHandler( $psid )
	{
		$a_ps_act = array();
		$fields = array(
					"PAY_SYSTEM_ID" => $psid,
					"NAME" => Loc::getMessage("DEVTM_ERIP_PS_ACTION_NAME"),
          "DESCRIPTION" => Loc::getMessage("DEVTM_ERIP_PS_DESC"),
					"ACTION_FILE" => "/bitrix/php_interface/include/sale_payment/".$this->MODULE_ID,
					"NEW_WINDOW" => "N",
					"HAVE_PREPAY" => "N",
					"HAVE_RESULT" => "N",
					"HAVE_ACTION" => "N",
					"HAVE_PAYMENT" => "Y",
					"HAVE_RESULT_RECEIVE" => "Y",
					"ENCODING" => "windows-1251",
				 );
		$db_pt = CSalePersonType::GetList(
							array("SORT" => "ASC", "NAME" => "ASC"),
							array()
						);
		while($pt = $db_pt->Fetch())
		{
			$fields["PERSON_TYPE_ID"] = $pt["ID"];
			$id = CSalePaySystemAction::Add($fields);
			if($id != false)
				$a_ps_act[] = $id;

		}

		return $a_ps_act;
	}

	protected function deletePaysysHandler()
	{
		$a_ps_act = explode("|", \Bitrix\Main\Config\Option::get( $this->MODULE_ID, "pay_handler_ids"));
		if(!empty($a_ps_act))
			foreach($a_ps_act as $id)
				CSalePaySystemAction::Delete($id);
		return true;
	}

	protected function addOStatus()
	{
		$lang_er = array();
		foreach($this->lang_ids as $lang)
		{
			$lang_er[] = array("LID" => $lang, "NAME" => Loc::getMessage("DEVTM_ERIP_STATUS_ER_NAME"), "DESCRIPTION" => Loc::getMessage("DEVTM_ERIP_STATUS_ER_DESC"));
		}

		if(empty($lang_er)) return false;

		return CSaleStatus::Add(
								array(
									"ID" => "ER",
									"SORT" => 100,
									"LANG" => $lang_er,
									"NOTIFY" => "N"
								)
							);
	}

	protected function deleteOStatus()
	{
		$code_status = \Bitrix\Main\Config\Option::get( $this->MODULE_ID, "order_status_code_erip");

		$order = CSaleOrder::GetList(array(), array("STATUS_ID" => $code_status))->Fetch();
		if($order["ID"] > 0)
			throw new Exception(Loc::getMessage("DEVTM_ERIP_DELETE_STATUS_ERROR"));

		$o_s = new CSaleStatus;
		if(!$o_s->Delete($code_status))
			throw new Exception(Loc::getMessage("DEVTM_ERIP_DELETE_STATUS2_ERROR"));
		return true;
	}

	protected function addMailEvType()
	{
		foreach($this->lang_ids as $lang)
		{
			$f = array(
					"LID" => $lang,
					"EVENT_NAME" => $this->mail_event_name,
					"NAME" => Loc::getMessage("DEVTM_ERIP_MAIL_EVENT_NAME"),
					"DESCRIPTION" => Loc::getMessage("DEVTM_ERIP_MAIL_EVENT_DESC"),
				);

			$et = new CEventType;
			if($et->Add($f) === false)
				return false;
		}

		return true;
	}

	protected function deleteMailEvType()
	{
		$et = \Bitrix\Main\Config\Option::get( $this->MODULE_ID, "mail_event_name");
		CEventType::Delete($et);
		return true;
	}

	protected function addMailTemplate()
	{
		$ss = array();
		$db_sites = CSite::GetList($by="sort", $order="desc", array());
		while($s = $db_sites->Fetch())
			$ss[] = $s["ID"];

		$f = array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => $this->mail_event_name,
				"LID" => $ss,
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO" => "#EMAIL_TO#",
				"SUBJECT" => Loc::getMessage("DEVTM_ERIP_MAIL_TEMPLATE_THEMA"),
				"BODY_TYPE" => "html",
				"MESSAGE" => Loc::getMessage("DEVTM_ERIP_MAIL_TEMPLATE_MESS"),
			);

		$o_mt = new CEventMessage;
		return $o_mt->Add($f);
	}

	protected function deleteMailTemplate()
	{
		$mail_template_id = (int)\Bitrix\Main\Config\Option::get( $this->MODULE_ID, "mail_template_id");
		CEventMessage::Delete($mail_template_id);
		return true;
	}

	protected function addHandlers()
	{
		RegisterModuleDependences(
			"sale",
			"OnSaleOrderBeforeSaved",
			$this->MODULE_ID,
			"Handlers",
			"chStatusNew",
			200
	   );

	   //Совместимость со старым событием OnSaleBeforeStatusOrder
	   RegisterModuleDependences(
			"sale",
			"OnSaleBeforeStatusOrder",
			$this->MODULE_ID,
			"Handlers",
			"chStatusOld",
			200
	   );

		return true;
	}

	protected function deleteHandlers()
	{
		UnRegisterModuleDependences(
			"sale",
			"OnSaleOrderBeforeSaved",
			$this->MODULE_ID,
			"Handlers",
			"chStatusNew"
		);

		//Совместимость со старым событием OnSaleBeforeStatusOrder
		UnRegisterModuleDependences(
			"sale",
			"OnSaleBeforeStatusOrder",
			$this->MODULE_ID,
			"Handlers",
			"chStatusOld"
		);

		return true;
	}

    public function DoInstall()
    {
		try
		{
			//Проверка зависимостей модуля
			if( ! IsModuleInstalled("sale") )
				throw new Exception(Loc::getMessage("DEVTM_ERIP_SALE_MODULE_NOT_INSTALL_ERROR"));
			if( ! function_exists("curl_init") )
				throw new Exception(Loc::getMessage("DEVTM_ERIP_CURL_NOT_INSTALL_ERROR"));
			if( ! function_exists("json_decode") )
				throw new Exception(Loc::getMessage("DEVTM_ERIP_JSON_NOT_INSTALL_ERROR"));

			//регистраниция модуля
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

			//создание платёжной системы
			$psid = $this->addPaysys();
			if($psid === false)
				throw new Exception(Loc::getMessage("DEVTM_ERIP_PS_ERROR_MESS"));

			//сохранение ID пл. системы в настройках модуля
			\Bitrix\Main\Config\Option::set( $this->MODULE_ID, "payment_system_id",  $psid);

			//копируем файлы обработчика пл. системы
			if(!$this->copyHandlerFiles())
				throw new Exception(Loc::getMessage("DEVTM_ERIP_COPY_ERROR_MESS"));

			//регистрируем обработчик пл. системы
			$pay_handler_ids = $this->addPaysysHandler($psid);
			if(empty($pay_handler_ids))
				throw new Exception(Loc::getMessage("DEVTM_ERIP_PS_ACTION_ERROR_REG"));

			//сохраняем id обработчиков пл. системы
				\Bitrix\Main\Config\Option::set( $this->MODULE_ID, "pay_handler_ids",  implode("|", $pay_handler_ids));

			//создание статуса заказа [ЕРИП]Ожидание оплаты
			$o_status_code = $this->addOStatus();
			if($o_status_code === false)
				throw new Exception(Loc::getMessage("DEVTM_ERIP_ORDER_STATUS_ERROR_MESS"));

			//сохранение кода статуса заказа в настройках модуля
			\Bitrix\Main\Config\Option::set( $this->MODULE_ID, "order_status_code_erip",  $o_status_code);

			//Создание типа почтового события
			if($this->addMailEvType() === false)
				throw new Exception(Loc::getMessage("DEVTM_ERIP_MAIL_EVENT_ADD_ERROR"));

			//сохранение названия типа почтового события в настройках модуля
			\Bitrix\Main\Config\Option::set( $this->MODULE_ID, "mail_event_name",  $this->mail_event_name);

			//создание почтового шаблона
			$mail_temp_id = $this->addMailTemplate();
			if($mail_temp_id === false)
				throw new Exception(Loc::getMessage("DEVTM_ERIP_MAIL_TEMPLATE_ADD_ERROR"));

			//сохранение ID почтового шаблона в настройках модуля
			\Bitrix\Main\Config\Option::set( $this->MODULE_ID, "mail_template_id",  $mail_temp_id);

			//регистрация обработчика обновления заказа
			if($this->addHandlers() === false)
				throw new Exception(Loc::getMessage("DEVTM_ERIP_HANDLERS_ADD_ERROR"));

			return true;

		}catch(Exception $e){
			$this->DoUninstall();
			$GLOBALS["APPLICATION"]->ThrowException($e->getMessage());
			return false;
		}
		return true;
    }

    public function DoUninstall()
    {
		try
		{
			//удаление платёжной системы
			$this->deletePaysys();

			//удаление статуса заказа [ЕРИП]Ожидание оплаты
			$this->deleteOStatus();

			//удаление обработчика
			$this->deleteHandlers();

			//удаление почтового шаблона
			$this->deleteMailTemplate();

			//удаление почтового события
			$this->deleteMailEvType();

			//удаляем обработчики пл. системы
			$this->deletePaysysHandler();

			//удаления файлов обработчика пл. системы
			$this->deleteHandlerFiles();

			//удаление настроек модуля
			Bitrix\Main\Config\Option::delete( $this->MODULE_ID );

			//удаление модуля из системы
			Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
			return true;

		}catch(Exception $e){
			$GLOBALS["APPLICATION"]->ThrowException($e->getMessage());
			return false;
		}
    }
}
