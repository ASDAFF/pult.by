<?
require 'ph.php' ;
$url="https://stroyinstrument.by/catalog/elektrotekhnicheskaya-produktsiya/lampy/";
$id= 4378 ; 
$file = file_get_contents($url);

$docc = phpQuery::newDocument($file);
for($i = 0; $i <=17; $i++){
 $main = $docc->find('.catalog-item-title a:eq('.$i.') ')->attr("href");
$fil = file_get_contents('https://stroyinstrument.by'.$main);

$doc = phpQuery::newDocument($fil);
 $tr1 = $doc->find('h1')->text();
//$tr = $doc->find(' a:eq(128) ')->attr("href");
$tr = $doc->find('.catalog-detail-images')->attr("href");
$tr2 = $doc->find('.catalog-detail-item-price');
$tr3 = $doc->find('.description');
$tr4 = $doc->find('.catalog-detail-properties');
$tr5 = $doc->find('.price');
// echo $tr1;
// echo $tr;
// echo "<br>";
// echo $tr2;
// echo "<br>";
// echo $tr3;
// echo "<br>";
// echo $tr4;
// echo "<br>";
echo $h= strip_tags($tr2);
 $pieces = explode(".", $tr2);
//$str = preg_replace("/[^0-9]/", '', $pieces[0]);
 $g = $pieces[0].".".$pieces[1];
 $bodytag = str_replace("руб.", "", $h);
 $bodytag1 = str_replace("за шт", "", $bodytag);
 $bodytag2 = str_replace(" ", "", $bodytag1);
?>


<?
 require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
 if(CModule::IncludeModule("iblock"))
                     {
$params = Array(
   "max_len" => "100", // обрезает символьный код до 100 символов
   "change_case" => "L", // буквы преобразуются к нижнему регистру
   "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
   "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
   "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
   "use_google" => "false", // отключаем использование google
);
 $arFields["CODE"]=CUtil::translit($tr1, "ru", $params); 

$el = new CIBlockElement;


$arLoadProductArray = Array(
  "IBLOCK_SECTION_ID" => $id,          // элемент лежит в корне раздела
  "IBLOCK_ID"      => 9,
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => $tr1,
  "ACTIVE"         => Y,            // активен
  "CODE"   => $arFields["CODE"],
  "DETAIL_TEXT"=>$tr4,
  "PREVIEW_PICTURE" => CFile::MakeFileArray('https://stroyinstrument.by'.$tr),
  "DETAIL_PICTURE" => CFile::MakeFileArray('https://stroyinstrument.by'.$tr)
  );

if($PRODUCT_ID = $el->Add($arLoadProductArray)){
  echo "New ID: ".$PRODUCT_ID;
$arFields = array(
                  "ID" => $PRODUCT_ID, 
                  "VAT_ID" => 1, //выставляем тип ндс (задается в админке)  
                  "VAT_INCLUDED" => "Y" //НДС входит в стоимость
                  );
if(CCatalogProduct::Add($arFields))
    echo "Добавили параметры товара к элементу каталога ".$PRODUCT_ID.'<br>';
else
    echo 'Ошибка добавления параметров<br>';
$PRICE_TYPE_ID = 1;

$arFields = array(
    "PRODUCT_ID" => $PRODUCT_ID,
    "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
    "PRICE" => $bodytag2,
    "CURRENCY" => "BYN"
    

);

$res = CPrice::GetList(
        array(),
        array(
                "PRODUCT_ID" => $PRODUCT_ID,
                "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
            )
    );

if ($arr = $res->Fetch())
{
    CPrice::Update($arr["ID"], $arFields);
}
else
{
    CPrice::Add($arFields);
}

}else{
  echo "Error: ".$el->LAST_ERROR;
}

}

?>
<?
//Установим для товара с кодом 15 цену типа 2 в значение 29.95 USD

// $PRICE_TYPE_ID = 1;

// $arFields = Array(
//     "PRODUCT_ID" => $PRODUCT_ID,
//     "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
//     "PRICE" => $str,
//     "CURRENCY" => "RUB",
//     "QUANTITY_FROM" => 1,
//     "QUANTITY_TO" => 10
// );

// $res = CPrice::GetList(
//         array(),
//         array(
//                 "PRODUCT_ID" => $PRODUCT_ID,
//                 "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
//             )
//     );

// if ($arr = $res->Fetch())
// {
//     CPrice::Update($arr["ID"], $arFields);
// }
// else
// {
//     CPrice::Add($arFields);
// }

}
?>



<?



require 'php.php' ;
$url= _DIR_ . "/url.php";
$id= 4378 ; 
$file = file_get_contents($url);
  $docc = phpQuery::newDocument($file);


 for($i = 0; $i < 20; $i++){


echo $name = $docc->find('.js-detaillink span:eq('.$i.')')->text();
echo "<br>";
echo $zena = $docc->find('.text-left:eq('.$i.')')->text();
echo "<br>";    
echo $img = $docc->find('.pic a img:eq('.$i.')')->attr('src');
echo "<br>";   
    foreach ($docc->find('.groupedprops:eq('.$i.')') as $key=>$tr) {
      
    $tr = pq($tr);
    $Date[] = $tr->find('.js-detaillink')->text();
    echo "<br>";
    echo $match = $tr->find(' .line:eq(0)')->text(); echo "  ------ ";
    echo $mat = $tr->find(' .val:eq(0)')->text();
    echo "<br>";
    echo $match1 = $tr->find(' .line:eq(1)')->text(); echo " ------ ";
    echo $mat1 = $tr->find(' .val:eq(1)')->text();
    echo "<br>";
    echo $match2 = $tr->find(' .line:eq(2)')->text(); echo "  ------ ";
    echo $mat2 = $tr->find(' .val:eq(2)')->text();
    echo "<br>";
    echo $match3 = $tr->find(' .line:eq(3)')->text(); echo "  ------ ";
    echo $mat3 = $tr->find(' .val:eq(3)')->text();
    echo "<br>";
    echo $match4 = $tr->find(' .line:eq(4)')->text(); echo "  ------ ";
    echo $mat4 = $tr->find(' .val:eq(4)')->text();
    echo "<br>";
    echo $match5 = $tr->find(' .line:eq(5)')->text(); echo "  ------ ";
    echo $mat5 = $tr->find(' .val:eq(5)')->text();
    echo "<br>";
    $homeTeam[] = $docc->find('td:nth-child(2) ')->text();
    echo "<br>";
     $homeTeamLogo[] = $tr->find('td:nth-child(3) a img')->attr('src');
     $awayTeamLogo[] = $tr->find('td:nth-child(5) a img')->attr('src');
     $awayTeam[] = $tr->find('td:nth-child(6) a')->text();

   }


echo "<br>";
 $main = $docc->find('.pic a:eq('.$i.') ')->attr("href");

$filu = file_get_contents('https://slesarka.by'.$main);
 $doc = phpQuery::newDocument($filu);
echo "20";
echo $tr5 = $doc->find('.glass a img')->attr('src');
echo "2";
echo $tr1 = $doc->find('.contentinner');
//$tr = $doc->find(' a:eq(128) ')->attr("href");
$tr = $doc->find('.catalog-detail-images')->attr("href");
$tr2 = $doc->find('.catalog-detail-item-price');
$tr3 = $doc->find('.description');
$tr4 = $doc->find('.catalog-detail-properties');


// echo $tr1;
// echo $tr;
// echo "<br>";
// echo $tr2;
// echo "<br>";
// echo $tr3;
// echo "<br>";
// echo $tr4;
// echo "<br>";
 $h= strip_tags($tr2);
 $pieces = explode(".", $tr2);
//$str = preg_replace("/[^0-9]/", '', $pieces[0]);
 $g = $pieces[0].".".$pieces[1];
 $bodytag = str_replace("руб.", "", $h);
 $bodytag1 = str_replace("за шт", "", $bodytag);
 $bodytag2 = str_replace(" ", "", $bodytag1);
?>


<?
 require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
 if(CModule::IncludeModule("iblock"))
                     {
$params = Array(
   "max_len" => "100", // обрезает символьный код до 100 символов
   "change_case" => "L", // буквы преобразуются к нижнему регистру
   "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
   "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
   "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
   "use_google" => "false", // отключаем использование google
);
 $arFields["CODE"]=CUtil::translit($tr1, "ru", $params); 

// $el = new CIBlockElement;


// $arLoadProductArray = Array(
//   "IBLOCK_SECTION_ID" => $id,          // элемент лежит в корне раздела
//   "IBLOCK_ID"      => 9,
//   "PROPERTY_VALUES"=> $PROP,
//   "NAME"           => $tr1,
//   "ACTIVE"         => Y,            // активен
//   "CODE"   => $arFields["CODE"],
//   "DETAIL_TEXT"=>$tr4,
//   "PREVIEW_PICTURE" => CFile::MakeFileArray('https://stroyinstrument.by'.$tr),
//   "DETAIL_PICTURE" => CFile::MakeFileArray('https://stroyinstrument.by'.$tr)
//   );

// if($PRODUCT_ID = $el->Add($arLoadProductArray)){
//   echo "New ID: ".$PRODUCT_ID;
// $arFields = array(
//                   "ID" => $PRODUCT_ID, 
//                   "VAT_ID" => 1, //выставляем тип ндс (задается в админке)  
//                   "VAT_INCLUDED" => "Y" //НДС входит в стоимость
//                   );
// if(CCatalogProduct::Add($arFields))
//     echo "Добавили параметры товара к элементу каталога ".$PRODUCT_ID.'<br>';
// else
//     echo 'Ошибка добавления параметров<br>';
// $PRICE_TYPE_ID = 1;

// $arFields = array(
//     "PRODUCT_ID" => $PRODUCT_ID,
//     "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
//     "PRICE" => $bodytag2,
//     "CURRENCY" => "BYN"
    

// );

// $res = CPrice::GetList(
//         array(),
//         array(
//                 "PRODUCT_ID" => $PRODUCT_ID,
//                 "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
//             )
//     );

// if ($arr = $res->Fetch())
// {
//     CPrice::Update($arr["ID"], $arFields);
// }
// else
// {
//     CPrice::Add($arFields);
// }

// }else{
//   echo "Error: ".$el->LAST_ERROR;
// }

 }

?>
<?
//Установим для товара с кодом 15 цену типа 2 в значение 29.95 USD

// $PRICE_TYPE_ID = 1;

// $arFields = Array(
//     "PRODUCT_ID" => $PRODUCT_ID,
//     "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
//     "PRICE" => $str,
//     "CURRENCY" => "RUB",
//     "QUANTITY_FROM" => 1,
//     "QUANTITY_TO" => 10
// );

// $res = CPrice::GetList(
//         array(),
//         array(
//                 "PRODUCT_ID" => $PRODUCT_ID,
//                 "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
//             )
//     );

// if ($arr = $res->Fetch())
// {
//     CPrice::Update($arr["ID"], $arFields);
// }
// else
// {
//     CPrice::Add($arFields);
// }

 }
?>