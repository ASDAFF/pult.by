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
<name>�����.BY - ������� ��� ������ ����</name>
<company>�����.BY - ������� ��� ������ ����</company>
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
<category id="4387">������� �������</category>
<category id="4388" parentId="4387">��������� ������ �����������</category>
<category id="4400">����������� �����������!</category>
<category id="4412">����� ���, �������������</category>
<category id="4413" parentId="4412">��������� ������ ����������</category>
<category id="4414" parentId="4412">���������</category>
<category id="4421">������������ ������� Hi-Fi</category>
<category id="4422" parentId="4421">�������� Hi-Fi ���������</category>
<category id="4423" parentId="4421">�������� Hi-Fi ��������</category>
<category id="4426" parentId="4421">�������� Hi-Fi ������������ ������</category>
<category id="4427" parentId="4421">���������</category>
<category id="4428" parentId="4421">������������ ��������� Hi-Fi</category>
<category id="4429" parentId="4428">������������ ������� 2.0</category>
<category id="4430" parentId="4428">������������ ������� 2.1</category>
<category id="4431" parentId="4428">������������ ������� 3.0</category>
<category id="4432" parentId="4428">������������ ������� 3.1</category>
<category id="4433" parentId="4428">������������ ������� 5.0</category>
<category id="4434" parentId="4428">������������ ������� 5.1</category>
<category id="4435" parentId="4428">������������ ������� 7.0</category>
<category id="4436" parentId="4428">������������ ������� 7.1</category>
<category id="4425" parentId="4421">�������� Hi-Fi ���������</category>
<category id="4424" parentId="4421">�������� Hi-Fi ����������� ��������</category>
<category id="4437" parentId="4421">�������� ���������/�������� ������ (���������)</category>
<category id="4441" parentId="4421">�������� Hi-Fi ��������</category>
<category id="4440" parentId="4421">�������� Hi-Fi ������������</category>
<category id="4438" parentId="4421">�������� Hi-Fi  ������������</category>
<category id="4439" parentId="4438">���������������</category>
<category id="4442" parentId="4421">�������� Hi-Fi �����������</category>
<category id="4443" parentId="4421">�������� Hi-Fi �����������</category>
<category id="4444" parentId="4421">���������� ��� ��������</category>
<category id="4479">������ / ������������� </category>
<category id="4480" parentId="4479">CD �������������</category>
<category id="4481" parentId="4479">CD ��������</category>
<category id="4482" parentId="4479">������� �����-�������������</category>
<category id="4483" parentId="4479">BLU-RAY ������</category>
<category id="4484" parentId="4479">Blu-RAY ��������</category>
<category id="4485" parentId="4479">SACD �������������</category>
<category id="4486" parentId="4479">�����������</category>
<category id="4498">��������� </category>
<category id="4499" parentId="4498">��������� �������� ���������������</category>
<category id="4500" parentId="4498">��������������� ���������</category>
<category id="4501" parentId="4498">��������� ��������</category>
<category id="4502" parentId="4501">- 1 ���������</category>
<category id="4503" parentId="4501">- 2-4 ���������</category>
<category id="4504" parentId="4501">- 5 ���������</category>
<category id="4505" parentId="4501">- 7 � ����� ������� ��������</category>
<category id="4506" parentId="4498">������ ��������</category>
<category id="4507" parentId="4498">��������� ��� ���������</category>
<category id="4508" parentId="4498">��������� ��� ����������</category>
<category id="4467">�����</category>
<category id="4468" parentId="4467">������������� ������</category>
<category id="4469" parentId="4467">��������������</category>
<category id="4470" parentId="4467">�������������� �� ����</category>
<category id="4471" parentId="4467">�������������� �� ����</category>
<category id="4472" parentId="4467">�������������� Grado</category>
<category id="4473" parentId="4467">�������</category>
<category id="4474" parentId="4467">����������</category>
<category id="4459">AV ��������</category>
<category id="4460" parentId="4459">AV ��������</category>
<category id="4461" parentId="4459">AV ����������</category>
<category id="4462" parentId="4459">Blu-Ray ��������</category>
<category id="4525">��� / DAC</category>
<category id="4419">������</category>
<category id="4389">���������� � ������</category>
<category id="4390" parentId="4389">���������� � ���������� �� 32&quot; �� 43&quot;</category>
<category id="4391" parentId="4389">���������� � ���������� �� 46&quot; �� 58&quot;</category>
<category id="4392" parentId="4389">���������� � ���������� �� 60&quot; � �����</category>
<category id="4393" parentId="4389"> ���������� � OLED ��������</category>
<category id="4394" parentId="4389">3D ���� ��� �����������</category>
<category id="4395" parentId="4389">��������� ��� �����������</category>
<category id="4396" parentId="4389">���������� ������������ / �����������</category>
<category id="4397" parentId="4389">������������� �����</category>
<category id="4402">��������� </category>
<category id="4403" parentId="4402">��������� ��������</category>
<category id="4404" parentId="4402">��������� �������������</category>
<category id="4405" parentId="4402">��������� ���������������</category>
<category id="4406" parentId="4402">��������� ������� / ���������</category>
<category id="4407" parentId="4402">��������-������</category>
<category id="4408" parentId="4402">���� / �����������</category>
<category id="4409" parentId="4402">���������� Sim 2</category>
<category id="4509">������</category>
<category id="4510" parentId="4509">������ ��� ���������� ��������������</category>
<category id="4511" parentId="4509">������ ��� ���������� �������</category>
<category id="4512" parentId="4509">������ ��� ���������� ������������</category>
<category id="4513" parentId="4509">������ ��� ���������� � ������ ��������</category>
<category id="4514" parentId="4509">������ ��� ���������� ����������� ���������</category>
<category id="4515" parentId="4509">������������� �����</category>
<category id="4463">������ ��� ����������</category>
<category id="4398">����������� ������ / ��� ������� </category>
<category id="4410">�������������� / ���������</category>
<category id="4415">3D ����</category>
<category id="4416" parentId="4415">3D ���� ��� �����������</category>
<category id="4417" parentId="4415">3D ���� ��� ���������� </category>
<category id="4516">������ ����������</category>
<category id="4517" parentId="4516">������ ���������� ���������� ( 2 RCA - 2 RCA )</category>
<category id="4518" parentId="4516">������ ���������� ���������� ��������� ( 2 XLR - 2 XLR )</category>
<category id="4519" parentId="4516">������ ���������� HDMI</category>
<category id="4520" parentId="4516">������ ���������� �������� ������������ ( RCA - RCA )</category>
<category id="4521" parentId="4516">������ ���������� �������� ���������� / USB</category>
<category id="4522" parentId="4516">������ ���������� �����������</category>
<category id="4523" parentId="4516">������ ���������� � ��������</category>
<category id="4524" parentId="4516">���������� Siltech</category>
<category id="4494">������ ������������</category>
<category id="4495" parentId="4494">������ ������������ � �������</category>
<category id="4496" parentId="4494">������ ������������ ������� (��������� ��������)</category>
<category id="4497" parentId="4494">����������</category>
<category id="4532">������ �������</category>
<category id="4533" parentId="4532">������ ������� ������� (��������� ��������)</category>
<category id="4534" parentId="4532">������ ������� � �������</category>
<category id="4535" parentId="4532">������������ �������</category>
<category id="4487">������ ��� TV � Hi-Fi</category>
<category id="4488" parentId="4487">������ ��� TV / AV ����������</category>
<category id="4489" parentId="4487">������ ��� TV / AV ���������� � �����������</category>
<category id="4490" parentId="4487">������ ��� Hi-Fi ����������</category>
<category id="4491" parentId="4487">������ ��� CD / DVD ������</category>
<category id="4492" parentId="4487">����� ����������</category>
<category id="4493" parentId="4487">��������������� ���������� Solid Tech</category>
<category id="4475">������ ��� ��������</category>
<category id="4445">����������</category>
<category id="4446" parentId="4445">���������� ��� ��������</category>
<category id="4453" parentId="4445">���������� ��� ����������</category>
<category id="4447" parentId="4445">���������� ��� TV</category>
<category id="4448" parentId="4447">���������� ��� TV �������������</category>
<category id="4449" parentId="4447">���������� ��� TV � ������������ �������� � �������</category>
<category id="4450" parentId="4447">���������� ��� TV ��������������</category>
<category id="4451" parentId="4447">���������� ��� TV ����������</category>
<category id="4452" parentId="4447">���������� ��� TV ����������</category>
<category id="4477">������� ������� / ������������</category>
<category id="4454">��������</category>
<category id="4455" parentId="4454">�������� ��������� ����</category>
<category id="4456" parentId="4454">�������� ��������� ����</category>
<category id="4457" parentId="4454">�������� ��������������� (��������)</category>
<category id="4458" parentId="4454">���������� ��� ���������</category>
<category id="4465">������ ��</category>
<category id="4527">����������</category>
<category id="4528" parentId="4527">��� ������������ ������</category>
<category id="4529" parentId="4527">��� ���������� ����������� ���������, ����������, ��������������</category>
<category id="4530" parentId="4527">������</category>
<category id="4531" parentId="4527">������-������</category>
