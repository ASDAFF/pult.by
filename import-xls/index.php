<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />

	<title>Загрузка протоколов </title>
	
	<meta name="keywords" content="Test keywords" />
    <meta name="description" content="Test description" />
	<link rel="icon" href="/favicon.ico">
	<link href="/assets/css/mincss.css" rel="stylesheet" />
	<link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<section>
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="card" style="margin-top: 50px; padding: 50px;">
					<div class="card-header">
						<h2>Загрузка структурированного XLS</h2>
					</div>
					<div class="card-block">
						<div class="row">
							<div class="col-12">

<?
	
	function getXLS($xls){
		include_once '../Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = PHPExcel_IOFactory::load($xls);
		$objPHPExcel->setActiveSheetIndex(0);
		$aSheet = $objPHPExcel->getActiveSheet();
		
		$array = array();
		
		foreach($aSheet->getRowIterator() as $row){
			
			$cellIterator = $row->getCellIterator();
			
			$item = array();
			foreach($cellIterator as $cell){
				//заносим значения ячеек одной строки в отдельный массив
				//array_push($item, iconv('UTF-8', 'cp1251', $cell->getCalculatedValue()));
				array_push($item, $cell->getCalculatedValue());
			}
			
			array_push($array, $item);
		}

		return $array;
	}?>
	
<?


?>
<div class="form-group form-ava">
	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" name="filename" class="form-control-file"><br />
		<input type="submit" value="Загрузить" class="btn btn-md btn-primary mt10">
	</form>
</div>

<?
 require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


   if(is_uploaded_file($_FILES["filename"]["tmp_name"]))    
   {
     
     move_uploaded_file($_FILES["filename"]["tmp_name"], "../xls/".$_FILES["filename"]["name"]);
   


     echo "Загрузка завершина!";
	$xlsData = getXLS('../xls/'.$_FILES["filename"]["name"]); 
	

	 foreach ($xlsData as $key=>$f){
					
	

	$user[] = $f;
	 echo "<pre>";
	 print_r($f);
	
	 echo "</pre>";
	$params = Array(
   "max_len" => "100", // обрезает символьный код до 100 символов
   "change_case" => "L", // буквы преобразуются к нижнему регистру
   "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
   "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
   "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
   "use_google" => "false", // отключаем использование google
);


require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

// if(CModule::IncludeModule("iblock"))
//  				   		{
// $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");
// $arFilter = Array("IBLOCK_ID"=>2,"SECTION_ID"=>29);
// $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
// while($ob = $res->GetNextElement()){ 
//  $arFields = $ob->GetFields();  
// print_r($arFields);
//  $arProps = $ob->GetProperties();
// print_r($arProps['vendor']);
// }

// }

echo  $arFields["CODE"]=CUtil::translit($f[5], "ru", $params); 

if(CModule::IncludeModule("iblock"))
 				   		{

$el = new CIBlockElement;

$PROP = array();
$PROP[138] = $f[1]; 
$PROP[139] = $f[2]; 
$PROP[140] = $f[3]; 
$PROP[141] = $f[4]; 
$PROP[142] = $f[6];
$PROP[77] = $f[9];   
$PROP[143] = $f[11];  
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
  "IBLOCK_ID"      => 9,
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => $f[5],
  "ACTIVE"         => "Y", 
  "CODE"         => $arFields["CODE"],             
  "PREVIEW_PICTURE"   => CFile::MakeFileArray($f[8]),
  "DETAIL_TEXT"    => $f[7],
  "DETAIL_PICTURE" => CFile::MakeFileArray($f[8])
  );

if($PRODUCT_ID = $el->Add($arLoadProductArray))
  echo "New ID: ".$PRODUCT_ID;
else
  echo "Error: ".$el->LAST_ERROR;

}

//  				   			$el = new CIBlockElement;
// 							 $PROP = array();
 				   	
						
						 	
						 // $PROP[46] = $f[0];
						 // $PROP[47] = $f[3];
						 // $PROP[48] = $f[4];
						 // $PROP[49] = $f[5];
						 // $PROP[51] = $f[7];
						 // $PROP[52] = $f[8];
						 // $PROP[53] = $f[9];
						 // $PROP[58] = $f[15];
						 // $PROP[59] = $f[16];
						 // $PROP[60] = $f[17];
							// if($f[18] == "MW-Light"){
				   // 				 $PROP[66] = Array("VALUE" => 20);
				   // 			}elseif($f[18] = "CHIARO"){
				   // 				 $PROP[66] = Array("VALUE" => 21);
				   // 			}elseif($f[18] ="REGENBOGEN-LIFE"){
				   // 				 $PROP[66] = Array("VALUE" => 22);
				   // 			}elseif($f[18] = "DEMARKT-CITY"){
				   // 				 $PROP[66] = Array("VALUE" => 23);
				   // 			}else{

				   // 			}

						// $PROP[73] = $f[4];
						// $PROP[46] = $f[1];
					 //    $arLoadProductArray = Array(

						// "MODIFIED_BY"    => $USER->GetID(), 
						// "IBLOCK_SECTION_ID" => 30,          
						// "IBLOCK_ID"      => 2,
						//  "PROPERTY_VALUES"=> $PROP,
						// "NAME"           => $f[2],
						// "ACTIVE"         => "Y"  ,         
						// "CODE"=> $arFields["CODE"].$f[2],
						// "DETAIL_TEXT"    => $f[3]
						  
						// );
						 
// 						 if($PRODUCT_ID = $el->Add($arLoadProductArray)){
// 						 	if($PRODUCT_ID = $el->Add($arLoadProductArray)){
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
//     "PRICE" => $f[5],
//     "CURRENCY" => "RUB"
    

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
// 						 }else
// 						 echo  $el->LAST_ERROR;
// 						}
		




	
// if(CModule::IncludeModule("iblock"))
//  				   		{
// $arSelect = Array("*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
// $arFilter = Array("IBLOCK_ID"=>2,"SECTION_ID"=>29);
// $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>400), $arSelect);
// while($ob = $res->GetNextElement()){ 
//  $arFields = $ob->GetFields();  
// print_r($arFields['ID']);
//  $arProps = $ob->GetProperties();
// //print_r($arProps['parser']['VALUE']);
// if($arProps['parser']['VALUE'] == 1){
// 	if($arFields['ID'] == 2123){
// if(CModule::IncludeModule("catalog"))
//  				   		{
// $PRODUCT_ID = $arFields['ID'];
// $PRICE_TYPE_ID = 1;

// $arFields = Array(
//     "PRODUCT_ID" => $PRODUCT_ID,
//     "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
//     "PRICE" => $f[2],
//     "CURRENCY" => "RUB"
    
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
// }
// }
// }
// }
// }


 //}






}

 	}



//  	echo "<pre>";
// print_r($user);
// echo "</pre>";
//$input = array_map("unserialize", array_unique(array_map("serialize", $user)));


	






// echo "<pre>";
// print_r($user[1][0]);
// echo "</pre>";



 // 	foreach ($user as $key => $value) {
 				   				
 // 				echo "<pre>";
	// print_r($value);
	// if($r[$key] == $value[0]){

	// }
		   						   			





//}


?>	

							</div>
						</div>
						<!-- Закрытие row card-block -->
					</div>
					<!-- Закрытие card-block -->
				</div>
				<!-- Закрытие card -->
			</div>
		</div>
	</div>
	<!-- Закрытие container -->
</section>

<!-- *************************************************************************************** SCRIPTS -->
<!-- Bootstrap v4.0.0-alpha.6 core JavaScript ================================================== -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/assets/js/jquery-3.2.1.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>

</html>



