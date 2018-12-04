<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Контакты");
$APPLICATION->SetTitle("Контакты");
?>
<div class="bx_page">
	<p><b>Телефон:</b> #PHONE#<br />
	<b>Адрес:</b> г. Москва, ул. 2-я Хуторская, д. 38</p>
    <br />
    <p><b>Схема проезда:</b></p>
	<iframe width="100%" height="490" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ru/maps?f=q&amp;source=s_q&amp;hl=ru&amp;geocode=&amp;q=%D0%B3.+%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0,+%D1%83%D0%BB.+2-%D1%8F+%D0%A5%D1%83%D1%82%D0%BE%D1%80%D1%81%D0%BA%D0%B0%D1%8F,+%D0%B4.+38%D0%90&amp;aq=&amp;sll=55,103&amp;sspn=90.84699,270.527344&amp;t=m&amp;ie=UTF8&amp;hq=&amp;hnear=2-%D1%8F+%D0%A5%D1%83%D1%82%D0%BE%D1%80%D1%81%D0%BA%D0%B0%D1%8F+%D1%83%D0%BB.,+38,+%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0,+127287&amp;ll=55.805478,37.569551&amp;spn=0.023154,0.054932&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
	<h2>Задать вопрос</h2>

	<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback", 
	".default", 
	array(
		"USE_CAPTCHA" => "N",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "sale@nyuta.bx",
		"REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "EMAIL",
			2 => "MESSAGE",
		),
		"EVENT_MESSAGE_ID" => array(
			0 => "7",
		),
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>