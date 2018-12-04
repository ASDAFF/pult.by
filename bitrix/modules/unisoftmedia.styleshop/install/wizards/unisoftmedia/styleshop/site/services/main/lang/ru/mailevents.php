<?php
$MESS['EVENT_NAME'] = 'Отправка сообщения через форму заказать звонок';
$MESS['EVENT_DESCRIPTION'] = '
#AUTHOR# - Автор сообщения
#AUTHOR_EMAIL# - Email автора сообщения
#AUTHOR_PHONE# - Телефон автора сообщения
#PRODUCT_NAME# - Наименование товара
#PRODUCT_LINK# - Ссылка на товар
#TEXT# - Текст сообщения
#EMAIL_FROM# - Email отправителя письма
#EMAIL_TO# - Email получателя письма';

$MESS['RECALL_SUBJECT'] = '#SITE_NAME#: Сообщение из формы купить в один клик';
$MESS['RECALL_MESSAGE'] = "
Информационное сообщение сайта #SITE_NAME#<br>
 ------------------------------------------<br>
 <br>
 Вам было отправлено сообщение через форму купить в один клик<br>
 <br>
 Автор: #AUTHOR#<br>
 E-mail автора: #AUTHOR_EMAIL#<br>
 Телефон автора: #AUTHOR_PHONE#<br>
 <br>
 Наименование товара:<br>
 <a href=\"http://#SERVER_NAME##PRODUCT_LINK#\">#PRODUCT_NAME#</a><br>
 <br>
 <br>
 Комментарий к заказу:<br>
 #TEXT#<br>
 <br>
Сообщение сгенерировано автоматически.";

$MESS['RECALL_BUY_ONE_CLICK_SUBJECT'] = '#SITE_NAME#: Сообщение из формы заказать звонок';
$MESS['RECALL_BUY_ONE_CLICK_MESSAGE'] = "
Информационное сообщение сайта #SITE_NAME#
 ------------------------------------------

Вам было отправлено сообщение через форму заказать звонок

Автор: #AUTHOR#
E-mail автора: #AUTHOR_EMAIL#
Телефон автора: #AUTHOR_PHONE#
 

Примечание:
 #TEXT#
 
Сообщение сгенерировано автоматически.";