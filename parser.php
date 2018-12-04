<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");
?>
<pre>
<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
              if(CModule::IncludeModule("iblock"))
                     {
$arSelect = Array("*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
$arFilter = Array("IBLOCK_ID"=>2);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>450), $arSelect);
$el = new CIBlockElement;
while($ob = $res->GetNextElement()){ 

 $arFields = $ob->GetFields();  
 print_r($arFields['ID']);
 $arProps = $ob->GetProperties();
 //print_r($arProps);
 




// $PROP = array();
//  $PROP[66]=  "KARE";
//  $PROP[46]=  $arProps['vendor']['VALUE'];
//  $PROP[47]= $arProps['classification']['VALUE'];
// $PROP[48]= $arProps['materials']['VALUE'];
//  $PROP[49]= $arProps['mdditional']['VALUE'];
//  $PROP[50]= $arProps['intelligence']['VALUE'];
//  $PROP[51]= $arProps['socle']['VALUE'];
//  $PROP[52]= $arProps['Number']['VALUE'];
//  $PROP[53]= $arProps['height']['VALUE'];
//  $PROP[54]= $arProps['width']['VALUE'];
//  $PROP[55]= $arProps['depth']['VALUE'];
//  $PROP[56]= $arProps['weight']['VALUE'];
//  $PROP[57]= $arProps['cube']['VALUE'];
//  $PROP[58]= $arProps['sizeheight']['VALUE'];
//  $PROP[59]= $arProps['sizewidth']['VALUE'];
//  $PROP[60]= $arProps['sizedepth']['VALUE'];
//  $PROP[61]= $arProps['packing']['VALUE'];
//  $PROP[62]= $arProps['packaging']['VALUE'];
//  $PROP[63]= $arProps['country']['VALUE'];
//  $PROP[20]= $arProps['vote_count']['VALUE'];
//  $PROP[21]= $arProps['vote_sum']['VALUE'];

//  $PROP[22]= $arProps['rating']['VALUE'];

 
// $PROP[64] =  array('1' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-detail-a-700x700.jpg") ,
// 					 '2' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-detail-b-700x700.jpg"),
// 					  '3' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-detail-c-700x700.jpg"),
// 					   '4' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-master-a-700x700.jpg"), 
// 					   '5' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-master-b-700x700.jpg"),
//              '6' =>CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-700x700.jpg")); 
//$f = CFile::MakeFileArray("https://myworldlight.ru/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-master-mood-a-700x700.jpg");
// if($f != ""){
 
//  }else{
 $PROP[65] = CFile::MakeFileArray("https://myworldlight.ru/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-master-mood-700x700.jpg");
 // }



//print_r( CFile::GetPath(CFile::MakeFileArray("http://test.poluavtomat.by/upload/kare-img/other-pic/KARE-".$arProps['vendor']['VALUE']."-master-mood-a-700x700.jpg")));
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), 
  "IBLOCK_SECTION" => 29,          
  "PROPERTY_VALUES"=> $PROP,
  "PREVIEW_PICTURE" => CFile::MakeFileArray("https://myworldlight.ru/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-700x700.jpg"),
  "DETAIL_PICTURE" => CFile::MakeFileArray("https://myworldlight.ru/upload/kare-img/700x700/KARE-".$arProps['vendor']['VALUE']."-700x700.jpg"),
  
  );
$PRODUCT_ID = $arFields['ID'];  
//$res1 = $el->Update($PRODUCT_ID, $arLoadProductArray);



    }
  }
 
 
?>
</pre>









<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>