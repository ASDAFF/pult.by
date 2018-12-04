<? $disableReferers = false;
if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";
$strReferer1 = htmlspecialchars($_GET["referer1"]);
if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";
$strReferer2 = htmlspecialchars($_GET["referer2"]);
header("Content-Type: text/xml; charset=windows-1251");
echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2018-11-17 12:01">
<shop>
<name>ПУЛЬТ.BY - Техника для вашего дома</name>
<company>ПУЛЬТ.BY - Техника для вашего дома</company>
<url>http://pult.by</url>
<platform>1C-Bitrix</platform>
<currencies>
<currency id="RUB" rate="1" />
<currency id="USD" rate="1" />
<currency id="EUR" rate="71.71" />
<currency id="UAH" rate="2.604" />
<currency id="BYN" rate="1" />
</currencies>
<categories>
<category id="4387">Готовые решения</category>
<category id="4388" parentId="4387">Комплекты стерео компонентов</category>
<category id="4400">Специальное предложение!</category>
<category id="4412">Умный дом, автоматизация</category>
<category id="4413" parentId="4412">Сенсорные панели управления</category>
<category id="4414" parentId="4412">Мультирум</category>
<category id="4421">Акустические системы Hi-Fi</category>
<category id="4422" parentId="4421">Акустика Hi-Fi напольная</category>
<category id="4423" parentId="4421">Акустика Hi-Fi полочная</category>
<category id="4426" parentId="4421">Акустика Hi-Fi центрального канала</category>
<category id="4427" parentId="4421">Сабвуферы</category>
<category id="4428" parentId="4421">Акустические комплекты Hi-Fi</category>
<category id="4429" parentId="4428">Акустические системы 2.0</category>
<category id="4430" parentId="4428">Акустические системы 2.1</category>
<category id="4431" parentId="4428">Акустические системы 3.0</category>
<category id="4432" parentId="4428">Акустические системы 3.1</category>
<category id="4433" parentId="4428">Акустические системы 5.0</category>
<category id="4434" parentId="4428">Акустические системы 5.1</category>
<category id="4435" parentId="4428">Акустические системы 7.0</category>
<category id="4436" parentId="4428">Акустические системы 7.1</category>
<category id="4425" parentId="4421">Акустика Hi-Fi настенная</category>
<category id="4424" parentId="4421">Акустика Hi-Fi окружающего звучания</category>
<category id="4437" parentId="4421">Звуковые проекторы/Звуковые панели (Саундбары)</category>
<category id="4441" parentId="4421">Акустика Hi-Fi активная</category>
<category id="4440" parentId="4421">Акустика Hi-Fi беспроводная</category>
<category id="4438" parentId="4421">Акустика Hi-Fi  встраиваемая</category>
<category id="4439" parentId="4438">Влагозащищенные</category>
<category id="4442" parentId="4421">Акустика Hi-Fi всепогодная</category>
<category id="4443" parentId="4421">Акустика Hi-Fi ландшафтная</category>
<category id="4444" parentId="4421">Аксессуары для акустики</category>
<category id="4479">Плееры / проигрыватели </category>
<category id="4480" parentId="4479">CD проигрыватели</category>
<category id="4481" parentId="4479">CD ресиверы</category>
<category id="4482" parentId="4479">Сетевые аудио-проигрыватели</category>
<category id="4483" parentId="4479">BLU-RAY плееры</category>
<category id="4484" parentId="4479">Blu-RAY ресиверы</category>
<category id="4485" parentId="4479">SACD проигрыватели</category>
<category id="4486" parentId="4479">Медиаплееры</category>
<category id="4498">Усилители </category>
<category id="4499" parentId="4498">Усилители мощности интегрированные</category>
<category id="4500" parentId="4498">Предварительные усилители</category>
<category id="4501" parentId="4498">Усилители мощности</category>
<category id="4502" parentId="4501">- 1 канальные</category>
<category id="4503" parentId="4501">- 2-4 канальные</category>
<category id="4504" parentId="4501">- 5 канальные</category>
<category id="4505" parentId="4501">- 7 и более каналов усиления</category>
<category id="4506" parentId="4498">Стерео ресиверы</category>
<category id="4507" parentId="4498">Усилители для наушников</category>
<category id="4508" parentId="4498">Усилители для сабвуферов</category>
<category id="4467">Винил</category>
<category id="4468" parentId="4467">Проигрыватели винила</category>
<category id="4469" parentId="4467">Фонокорректоры</category>
<category id="4470" parentId="4467">Звукосниматели МС типа</category>
<category id="4471" parentId="4467">Звукосниматели ММ типа</category>
<category id="4472" parentId="4467">Звукосниматели Grado</category>
<category id="4473" parentId="4467">Тонармы</category>
<category id="4474" parentId="4467">Аксессуары</category>
<category id="4459">AV ресиверы</category>
<category id="4460" parentId="4459">AV ресиверы</category>
<category id="4461" parentId="4459">AV процессоры</category>
<category id="4462" parentId="4459">Blu-Ray ресиверы</category>
<category id="4525">ЦАП / DAC</category>
<category id="4419">Тюнеры</category>
<category id="4389">Телевизоры и панели</category>
<category id="4390" parentId="4389">Телевизоры с диагональю от 32&quot; до 43&quot;</category>
<category id="4391" parentId="4389">Телевизоры с диагональю от 46&quot; до 58&quot;</category>
<category id="4392" parentId="4389">Телевизоры с диагональю от 60&quot; и более</category>
<category id="4393" parentId="4389"> Телевизоры с OLED матрицей</category>
<category id="4394" parentId="4389">3D очки для телевизоров</category>
<category id="4395" parentId="4389">Крепления для телевизоров</category>
<category id="4396" parentId="4389">Телевизоры коммерческие / гостиничные</category>
<category id="4397" parentId="4389">Интерактивные доски</category>
<category id="4402">Проекторы </category>
<category id="4403" parentId="4402">Проекторы домашние</category>
<category id="4404" parentId="4402">Проекторы интерактивные</category>
<category id="4405" parentId="4402">Проекторы короткофокусные</category>
<category id="4406" parentId="4402">Проекторы офисные / мобильные</category>
<category id="4407" parentId="4402">Документ-камеры</category>
<category id="4408" parentId="4402">Очки / передатчики</category>
<category id="4409" parentId="4402">Аксессуары Sim 2</category>
<category id="4509">Экраны</category>
<category id="4510" parentId="4509">Экраны для проекторов моторизованные</category>
<category id="4511" parentId="4509">Экраны для проекторов офисные</category>
<category id="4512" parentId="4509">Экраны для проекторов встраиваемые</category>
<category id="4513" parentId="4509">Экраны для проекторов с ручным приводом</category>
<category id="4514" parentId="4509">Экраны для проекторов постоянного натяжения</category>
<category id="4515" parentId="4509">Интерактивные доски</category>
<category id="4463">Кресла для кинотеатра</category>
<category id="4398">Музыкальные центры / Док станции </category>
<category id="4410">Радиоприемники / радиочасы</category>
<category id="4415">3D очки</category>
<category id="4416" parentId="4415">3D очки для телевизоров</category>
<category id="4417" parentId="4415">3D очки для проекторов </category>
<category id="4516">Кабели межблочные</category>
<category id="4517" parentId="4516">Кабели межблочные Аналоговые ( 2 RCA - 2 RCA )</category>
<category id="4518" parentId="4516">Кабели межблочные Аналоговые балансные ( 2 XLR - 2 XLR )</category>
<category id="4519" parentId="4516">Кабели межблочные HDMI</category>
<category id="4520" parentId="4516">Кабели межблочные Цифровые коаксиальные ( RCA - RCA )</category>
<category id="4521" parentId="4516">Кабели межблочные Цифровые оптические / USB</category>
<category id="4522" parentId="4516">Кабели межблочные Сабвуферные</category>
<category id="4523" parentId="4516">Кабели межблочные в разделку</category>
<category id="4524" parentId="4516">Аксессуары Siltech</category>
<category id="4494">Кабели акустические</category>
<category id="4495" parentId="4494">Кабели акустические в нарезку</category>
<category id="4496" parentId="4494">Кабели акустические готовые (Заводская разделка)</category>
<category id="4497" parentId="4494">Коннекторы</category>
<category id="4532">Кабели силовые</category>
<category id="4533" parentId="4532">Кабели силовые готовые (Заводская разделка)</category>
<category id="4534" parentId="4532">Кабели силовые в нарезку</category>
<category id="4535" parentId="4532">Коннекторные разъёмы</category>
<category id="4487">Стойки под TV и Hi-Fi</category>
<category id="4488" parentId="4487">Стойки под TV / AV компоненты</category>
<category id="4489" parentId="4487">Стойки под TV / AV компоненты с кронштейном</category>
<category id="4490" parentId="4487">Стойки под Hi-Fi компоненты</category>
<category id="4491" parentId="4487">Стойки для CD / DVD дисков</category>
<category id="4492" parentId="4487">Столы журнальные</category>
<category id="4493" parentId="4487">Допольнительные аксессуары Solid Tech</category>
<category id="4475">Стойки под акустику</category>
<category id="4445">Кронштейны</category>
<category id="4446" parentId="4445">Кронштейны для акустики</category>
<category id="4453" parentId="4445">Кронштейны для проекторов</category>
<category id="4447" parentId="4445">Кронштейны для TV</category>
<category id="4448" parentId="4447">Кронштейны для TV фиксированные</category>
<category id="4449" parentId="4447">Кронштейны для TV с возможностью поворота и наклона</category>
<category id="4450" parentId="4447">Кронштейны для TV моторизованные</category>
<category id="4451" parentId="4447">Кронштейны для TV потолочные</category>
<category id="4452" parentId="4447">Кронштейны для TV настольные</category>
<category id="4477">Сетевые фильтры / регенераторы</category>
<category id="4454">Наушники</category>
<category id="4455" parentId="4454">Наушники закрытого типа</category>
<category id="4456" parentId="4454">Наушники открытого типа</category>
<category id="4457" parentId="4454">Наушники внутриканальные (Вставные)</category>
<category id="4458" parentId="4454">Аксессуары для наушников</category>
<category id="4465">Пульты ДУ</category>
<category id="4527">Аксессуары</category>
<category id="4528" parentId="4527">Для акустических систем</category>
<category id="4529" parentId="4527">Для расширения возможности ресиверов, усилителей, проигрывателей</category>
<category id="4530" parentId="4527">Разное</category>
<category id="4531" parentId="4527">Кабель-каналы</category>
