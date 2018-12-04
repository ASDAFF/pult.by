<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
CJSCore::Init("jquery");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<script type="text/javascript">
    var b_log_counter = <?= count($property_data) + 5; ?>;
    function AddCondition(alfavit, titleRow, propCode, propName, modifytypeCode, modifyTypeName, modifySubCode, modifySubName)
    {
        var addrowTable = document.getElementById('mcartxls_addrow_table1');

        b_log_counter++;
        var newRow = addrowTable.insertRow(-1);

        newRow.id = "delete_row_log_" + b_log_counter;

        var newCell = newRow.insertCell(-1);
        var newSelect = document.createElement("select");

        newSelect.setAttribute('b_log_counter', b_log_counter);
        newSelect.name = "XLS_GLOBALS[xls][" + b_log_counter + "]";
        var i = -1;
        var i1 = -1;

        newCell.appendChild(newSelect);
        var array1 = alfavit;

        var array2 = titleRow;

        for (var i = 0; i < array1.length; i++) {
            var option = document.createElement("option");
            option.value = array1[i];
            option.text = array2[i] + '[' + array1[i] + ']';
            newSelect.appendChild(option);
        }

        var newCell = newRow.insertCell(-1);
        var newSelect = document.createElement("select");


        newSelect.name = "XLS_GLOBALS[bx][" + b_log_counter + "]";
        var i = -1;
        var i1 = -1;

        newCell.appendChild(newSelect);
        var array1 = propCode;

        var array2 = propName;

        for (var i = 0; i < array1.length; i++) {
            var option = document.createElement("option");
            option.value = array1[i];
            option.text = array2[i];

            newSelect.appendChild(option);
        }

        var newCell = newRow.insertCell(-1);
        var newSelect = document.createElement("select");


        newSelect.name = "XLS_GLOBALS[modify_type][" + b_log_counter + "]";
        var i = -1;
        var i1 = -1;

        newCell.appendChild(newSelect);
        var array1 = modifytypeCode;

        var array2 = modifyTypeName;

        for (var i = 0; i < array1.length; i++) {
            var option = document.createElement("option");
            option.value = array1[i];
            option.text = array2[i];
            newSelect.appendChild(option);

        }

        var newCell = newRow.insertCell(-1);
        var newSelect = document.createElement("select");
        newSelect.name = "XLS_GLOBALS[modify_subaction][" + b_log_counter + "]";
        var i = -1;
        var i1 = -1;

        newCell.appendChild(newSelect);
        var array1 = modifySubCode;

        var array2 = modifySubName;

        for (var i = 0; i < array1.length; i++) {
            var option = document.createElement("option");
            option.value = array1[i];
            option.text = array2[i];
            newSelect.appendChild(option);

        }
        var newCell = newRow.insertCell(-1);
        //newCell.id = "id_row_value_" + b_log_counter;
        //   newCell.align="right";
        newCell.innerHTML = '<input type="text" name="XLS_GLOBALS[modify_subaction_params][' + b_log_counter + ']">';

        var newCell = newRow.insertCell(-1);
        //newCell.id = "id_row_value_" + b_log_counter;
        //   newCell.align="right";
        newCell.innerHTML = '<a href="#" onclick="MCARTXLSDeleteRow(' + b_log_counter + '); return false;"><?= GetMessage("MCART_DELETE_ROW") ?></a>';


    }
    function MCARTXLSDeleteRow(ind)
    {
        var addrowTable = document.getElementById('mcartxls_addrow_table1');

        var cnt = addrowTable.rows.length;
        for (i = 0; i < cnt; i++)
        {
            if (addrowTable.rows[i].id != 'delete_row_log_' + ind)
                continue;

            addrowTable.deleteRow(i);

            break;
        }
        // if (addrowTable.rows.length <= 0)
        //    addrowTable.style.display = 'none';
    }
</script>
<?
global $DB;
$db_type = strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mcart.xls/classes/general/profile2.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mcart.xls/classes/general/str.php");

$format_file = COption::GetOptionString("mcart.xls", "format");

//print "<pre>"; print_r($_SESSION['ARR_REAL_PROFILE']); print "</pre>";

$PROFILE_ID = $_SESSION['PROFILE_ID'];
if (intval($PROFILE_ID) > 0) {
    $profile_data = CMcartXlsProfile2::GetRows('main_profile', array('section_id', 'section_id_new', 'need_translit'), array('id' => $PROFILE_ID), 1);
    if (count($profile_data) > 0)
        $ARR_REAL_PROFILE = $profile_data[0];
}

$errMess = "";
$XLS_IBLOCK_ID = $_REQUEST["xls_iblock_id"];

$inputFileName = $_REQUEST['xls_file_name'];


$SKU_IBLOCK_ID = "";
$titleRow = $_REQUEST['title_xls_row'];

$add_sku = $_REQUEST['add_sku'];
if ($add_sku == "Y") {
    $SKU_IBLOCK_ID = $_REQUEST['iblock_sku_id'];
    $CML2_LINK_CODE = $_REQUEST['cml2_link_code'];
}

if ($format_file == 'Excel2007') {
    if ($XLS_IBLOCK_ID == 0)
        $errMess = GetMessage("XLS_NULL_IBLOCK_SET");

    if (empty($inputFileName))
        $errMess = $errMess . "</br>" . GetMessage("XLS_NULL_FILE");
}

$highestColumn = strtoupper($_REQUEST["column_b"]);
$firstColumn = strtoupper($_REQUEST["column_a"]);
$firstRow = $_REQUEST['first_row'];
if (empty($firstRow))
    $firstRow = 1;

$_SESSION['START_ROW'] = $firstRow;

if (empty($titleRow))
    $titleRow = $firstRow;

$arrLiteras = CMcartXlsStrRef::MakeDiapazone();

if ($arrLiteras["Flip"][$highestColumn] < $arrLiteras["Flip"][$firstColumn]) {
    $tempColumn = $firstColumn;
    $firstColumn = $highestColumn;
    $highestColumn = $tempColumn;
}

/*
  if ((ord($highestColumn)>90)||(ord($firstColumn)<65))
  $errMess.=GetMessage("XLS_WRONG_DIAPAZONE");
 */

if (empty($errMess)) {
    CModule::IncludeModule('mcart.xls');
    $langCls = new CMcartXlsStrRef();
    ?>
    <form action="/bitrix/admin/mcart_xls_import_step_2.php"  method="POST">
        <input type=hidden name="xls_iblock_id" value="<?= intval($XLS_IBLOCK_ID) ?>">
        <input type=hidden name="xls_input_filename" value="<?= $inputFileName ?>">
        <?
        include $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/mcart.xls/classes/general/PHPExcel/IOFactory.php';

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);

            if ($inputFileType == 'CSV') {
                /*
                  if (ini_get('mbstring.func_overload') & 2){
                  die(GetMessage("MCART_WRONG_FILE_FORMAT")."</br><a href = '/bitrix/admin/mcart_xls_start.php'>".GetMessage("STEP_BACK")."</a>");
                  }
                 */
                $_SESSION['DELIMITER'] = $_REQUEST['delimiter'];
                $_SESSION['CRM_YES'] = $_REQUEST['xls_crm'];

                $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter($_REQUEST['delimiter'])
                        ->setEnclosure('"')
                        ->setLineEnding("\r\n")
                        ->setSheetIndex(0);
                $objPHPExcel = $objReader->load($inputFileName);
                $worksheet_names = $objReader->listWorksheetInfo($inputFileName);
            }

            if ($inputFileType == 'Excel2007') {
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
                $worksheet_names = $objReader->listWorksheetNames($inputFileName);
            }
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        if ($inputFileType == 'CSV') {
            $sheet = $objPHPExcel->getSheet();
            $num = $_REQUEST['last_row_num'];
            $highestRow = intval($num);
        }

        if ($inputFileType == 'Excel2007') {
            $XLS_SHEET_INDEX = $_REQUEST['xls_shett_index'];
            $_SESSION['ARR_XLS_DATA']["SHEET_ID"] = $XLS_SHEET_INDEX;
            $arRows = array();
            $sheet = $objPHPExcel->getSheet($XLS_SHEET_INDEX);
            
            $LAST_ROW_TYPE = $_REQUEST['rows_end_label'];

            switch ($LAST_ROW_TYPE) {
                case 'auto':
                    $highestRow = $sheet->getHighestRow();
                    break;
                case 'lastrownumber':
                    $num = $_REQUEST['last_row_num'];
                    $highestRow = intval($num);
                    break;
                default:
                    $highestRow = $sheet->getHighestRow();
            }
        }
        ?>
        <input type=hidden name="xls_highest_row" value="<?= $highestRow ?>">
        <?
        foreach (range($arrLiteras["Flip"][$firstColumn], $arrLiteras["Flip"][$highestColumn]) as $v) {
            $alfavit[] = $arrLiteras["Literas"][$v];
        }
        //$alfavit[] = "CONSTANT";

        $arIBlocksSection = Array();
        $arIBlocksSection["-1"] = GetMessage("MCART_SECTION_NO_CHANGE");

        $tmpFfields = CIBlock::getFields($XLS_IBLOCK_ID);
        if ($tmpFfields["IBLOCK_SECTION"]["IS_REQUIRED"] != "Y")
            $arIBlocksSection["0"] = GetMessage("MCART_FIRST_SECTION_XLS");

        $db_list = CIBlockSection::GetList(Array("NAME" => "ASC"), array("IBLOCK_ID" => $XLS_IBLOCK_ID));
        while ($arRes = $db_list->GetNext())
            $arIBlocksSection[$arRes["ID"]] = $arRes["NAME"];

        if ($inputFileType == 'Excel2007') {
            ?>
            <h4><?= GetMessage("XLS_SELECT_IBLOCK_SECTION") ?></h4>
            <select name = "xls_iblock_section_id" >
                <? foreach ($arIBlocksSection as $key => $value): ?>
                    <option value="<?= $key ?>"   <? if (isset($ARR_REAL_PROFILE['section_id']) && ($ARR_REAL_PROFILE['section_id'] == $key)) echo " selected" ?>><?= $value ?></option>
                <? endforeach ?>
            </select>
            </br>
            <? unset($arIBlocksSection["-1"]); ?>

            <h4><?= GetMessage("XLS_SELECT_IBLOCK_SECTION_NEW") ?></h4>
            <select name = "xls_iblock_section_id_new" >
                <? foreach ($arIBlocksSection as $key => $value): ?>
                    <option value="<?= $key ?>"   <? if (isset($ARR_REAL_PROFILE['section_id_new']) && ($ARR_REAL_PROFILE['section_id_new'] == $key)) echo " selected" ?>><?= $value ?></option>
                <? endforeach ?>
            </select>

            </br>
        <? } ?>
        <h3><?= GetMessage("XLS_DATA_EXAMPLE") ?></h3>
        </br>
        <table border="1">

            <tr>
                <? foreach ($alfavit as $bukva): ?>
                    <td><h3><?= $bukva ?></h3></td>
                <? endforeach; ?>
            </tr>
            <?
            $alfavit[] = "CONSTANT";
            ?>

            <?
            $ARR_TITLE_ROW = array();

            if ($inputFileType == 'CSV') {
                $rowTitleData = $sheet->rangeToArray($firstColumn . $titleRow . ':' . $highestColumn . $titleRow, NULL, TRUE, FALSE);

                foreach ($rowTitleData as $row_str)
                    foreach ($row_str as $row_cell)
                        $ARR_TITLE_ROW[] = $langCls->ConvertArrayCharset($row_cell, BP_EI_DIRECTION_IMPORT);

                $ARR_TITLE_ROW[] = GetMessage("MCART_CONSTANT");

                for ($row = $firstRow; $row <= ($firstRow + 5); $row++) {
                    $rowData = $sheet->rangeToArray($firstColumn . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                    ?>
                    <? foreach ($rowData as $row_id => $row_val): ?>
                        <tr>
                            <? foreach ($row_val as $col_id => $txt): ?>
                                <td><?= $langCls->ConvertArrayCharset($txt, BP_EI_DIRECTION_IMPORT) ?></td>
                            <? endforeach ?>
                        </tr>
                    <? endforeach; ?>
                    <?
                }
            }
            ?>
            <? if ($inputFileType == 'Excel2007') { ?>
                <?
                $rowTitleData = $sheet->rangeToArray($firstColumn . $titleRow . ':' . $highestColumn . $titleRow, NULL, TRUE, FALSE);
                foreach ($rowTitleData as $row_str)
                    foreach ($row_str as $row_cell)
                        $ARR_TITLE_ROW[] = $langCls->ConvertArrayCharset($row_cell, BP_EI_DIRECTION_IMPORT);

                $ARR_TITLE_ROW[] = GetMessage("MCART_CONSTANT");

                for ($row = $firstRow; $row <= ($firstRow + 5); $row++) {
                    $rowData = $sheet->rangeToArray($firstColumn . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                    ?>
                    <? foreach ($rowData as $row_id => $row_val): ?>
                        <tr>
                            <? foreach ($row_val as $col_id => $txt): ?>
                                <td><?= $langCls->ConvertArrayCharset($txt, BP_EI_DIRECTION_IMPORT) ?></td>
                            <? endforeach ?>
                        </tr>
                    <? endforeach; ?>
                    <?
                }
            }
            ?>

        </table >

        <? if ($inputFileType == 'CSV') { ?>
            <?
            $SrcPropID["0"] = GetMessage("XLS_FLD_00");
            $SrcPropID["1"] = GetMessage("XLS_FLD_1");
            $SrcPropID["2"] = GetMessage("XLS_FLD_2");
            $SrcPropID["3"] = GetMessage("XLS_FLD_3");
            $SrcPropID["4"] = GetMessage("XLS_FLD_4");
            $SrcPropID["5"] = GetMessage("XLS_FLD_5");
            $SrcPropID["6"] = GetMessage("XLS_FLD_6");
            $SrcPropID["7"] = GetMessage("XLS_FLD_7");
            $SrcPropID["8"] = GetMessage("XLS_FLD_8");
            $SrcPropID["9"] = GetMessage("XLS_FLD_9");
        }
        ?>

        <? if ($inputFileType == 'Excel2007') { ?>
            <?
            global $MCART_IS_SKU;

            // определение базовой цены	
            if ((CModule::IncludeModule('catalog')) && (CModule::IncludeModule('sale'))) {
                $MCART_IS_SKU = true;
                $db_res = GetCatalogGroups(($b = "SORT"), ($o = "ASC"));
                while ($res = $db_res->Fetch()) {
                    if ($res["BASE"] == "Y") {
                        $BASE_PRICE_ID = $res["ID"];
                        $BASE_PRICE_NAME = $res["NAME_LANG"];
                        //print "<pre>"; print_r($res); print "</pre>";
                        break;
                    }
                }
            }

            //print "<pre>"; print_r($arRows); print "</pre>";
            $SrcPropID["DETAIL_TEXT"] = GetMessage("XLS_FLD_DETAIL_TEXT");
            $SrcPropID["PREVIEW_TEXT"] = GetMessage("XLS_FLD_PREVIEW_TEXT");

            $SrcPropID["DETAIL_PICTURE"] = GetMessage("XLS_FLD_DETAIL_PICTURE");
            $SrcPropID["PREVIEW_PICTURE"] = GetMessage("XLS_FLD_PREVIEW_PICTURE");
            $SrcPropID["FLD_AMOUNT"] = GetMessage("XLS_FLD_AMOUNT");

            if ($MCART_IS_SKU) {
                $SrcPropID["FLD_CATALOG_BASE_PRICE"] = $BASE_PRICE_NAME;
                $SrcPropID["FLD_PURCHASING_PRICE"] = GetMessage("XLS_FLD_PURCHASING_PRICE");
            }

            $res = CIBlock::GetProperties($XLS_IBLOCK_ID, Array("SORT" => "ASC"), Array());
            while ($res_arr = $res->GetNext())
                $SrcPropID["PROPERTY_" . $res_arr["CODE"]] = $res_arr["NAME"];

            //$SrcPropID["0"] =GetMessage("XLS_NOT_RECORD");
            $SrcPropID["NAME"] = GetMessage("XLS_FLD_NAME");
        }
        ?>
        <?
//===========================================================================================загрузка значений профиля	
        $property_data = array();
        if (intval($PROFILE_ID) > 0) {
            $property_data = CMcartXlsProfile2::GetRows('mcart_profile_property', array('id', 'column_litera', 'field_code', 'action', 'subaction', 'params'), array('profile_id' => $PROFILE_ID, 'identify' => false), 0, 'id');
            $identify_rows = CMcartXlsProfile2::GetRows('mcart_profile_property', array('column_litera', 'field_code', 'action', 'subaction', 'params'), array('profile_id' => $PROFILE_ID, 'identify' => true), 1);
            if (count($identify_rows) > 0)
                $identify_row = $identify_rows[0];
        }
//===========================================================================================загрузка значений профиля						
        ?>
        </br>
        </br>
        <h1><?= GetMessage("XLS_SET_COLUMN_FILED"); ?></h1>
        </br>
        <table border="1px" id='mcartxls_addrow_table1'>
            <thead>
            <td>
                <?= GetMessage("XLS_TITLE_1") ?>
            </td>
            <td>
                <?= GetMessage("XLS_TITLE_2") ?>
            </td>
            <td>
                <?= GetMessage("XLS_MODIFY_TYPE") ?>
            </td>
            <td>
                <?= GetMessage("XLS_SUBACTION") ?>
            </td>
            <td>
                <?= GetMessage("XLS_SUBACTION_PARAMS") ?>
            </td>
            </thead>

            <? if ($inputFileType == 'Excel2007') { ?>
                <tr>
                    <td colspan="4">
                        <h4><?= GetMessage("PROPERTY_IDENTIFY") ?></h4>
                    </td>
                </tr>
                <? $bcheck = false; ?>
                <? $key = 0; ?>	
                <tr id='tr_etalon'>
                    <td>
                        <select name = "XLS_IDENTIFY[xls]">
                            <? foreach ($alfavit as $key1 => $val): ?>
                                <?
                                $replace = array('"', "'", '*', "/");
                                $ARR_TITLE_ROW[$key1] = str_replace($replace, '', $ARR_TITLE_ROW[$key1]);
                                ?>
                                <option value="<?= $val ?>" <? if (isset($identify_row) && ($identify_row['column_litera'] == $val)): ?>selected<? endif; ?>><?= $ARR_TITLE_ROW[$key1] . " (" . $val . ") " ?></option>

                            <? endforeach; ?>	

                    </td>
                    <td>
                        <select name = "XLS_IDENTIFY[bx]">
                            <?
                            $arrJSPropName = array();
                            $arrJSPropCode = array();
                            $arrJSPropName[] = GetMessage("MCART_SELECT_CODE");
                            $arrJSPropCode[] = "0";
                            ?>
                            <? foreach ($SrcPropID as $kode => $name): ?>
                                <?
                                $arrJSPropName[] = $name;

                                $arrJSPropCode[] = $kode;
                                ?>
                                <option value="<?= $kode ?>" <? if (isset($identify_row) && ($identify_row['field_code'] == $kode)) echo " selected" ?>><?= $name ?></option>
                            <? endforeach ?>
                        </select>
                    </td>

                    <td>
                        <?
                        $arrModifyTypeCode = array
                            (
                            "XLS_MODIFY_TYPE_NONE",
                            "XLS_MODIFY_TYPE_TO_INT",
                            "XLS_MODIFY_TYPE_TO_DATE",
                            "XLS_MODIFY_TYPE_TO_LINK",
                            "XLS_TAKE_VALUE_TWO",
                            "XLS_TAKE_VALUE_THREE",
                        );
                        ?>

                        <select name = "XLS_IDENTIFY[modify_type]" >
                            <? foreach ($arrModifyTypeCode as $action): ?>
                                <option value="<?= $action ?>"  <? if (isset($identify_row) && ($identify_row['action'] == $action)) echo " selected" ?>><?= GetMessage($action) ?></option>
                            <? endforeach; ?>
                        </select>
                    </td>
                    <?
                    $arrModifyTypeName = array
                        (
                        GetMessage("XLS_MODIFY_TYPE_NONE"),
                        GetMessage("XLS_MODIFY_TYPE_TO_INT"),
                        GetMessage("XLS_MODIFY_TYPE_TO_DATE"),
                        GetMessage("XLS_MODIFY_TYPE_TO_LINK"),
                        GetMessage("XLS_TAKE_VALUE_TWO"),
                        GetMessage("XLS_TAKE_VALUE_THREE"),
                    );
                    ?>
                    <td>
                        <?
                        $arrModifySubactionCode = array
                            (
                            "XLS_MODIFY_TYPE_NONE",
                            "XLS_DEL_SUBSTR"
                        );
                        $arrModifySubactionName = array
                            (
                            GetMessage("XLS_MODIFY_TYPE_NONE"),
                            GetMessage("XLS_DEL_SUBSTR")
                        );
                        ?>
                        <select name = "XLS_IDENTIFY[modify_subaction]" >
                            <? foreach ($arrModifySubactionCode as $subaction): ?>
                                <option value="<?= $subaction ?>"  <? if (isset($identify_row) && ($identify_row['action'] == $subaction)) echo " selected" ?>><?= GetMessage($subaction) ?></option>
                            <? endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="XLS_IDENTIFY[modify_subaction_params]" value="<?= (isset($identify_row)) ? $identify_row['params'] : ''; ?>">
                    </td>

                </tr>
                <tr>
                    <td colspan="4">
                        <h4><?= GetMessage("OTHER_PROPS") ?></h4>
                    </td>
                </tr>	

                <?
                $i = 1;
                foreach ($property_data as $property):
                    ?>
                    <tr id="delete_row_log_<?= $i ?>">
                        <td>
                            <select b_log_counter="<?= $i ?>" name="XLS_GLOBALS[xls][<?= $i ?>]">
                                    <? foreach ($alfavit as $key => $litera): ?>
                                    <option value="<?= $litera ?>" <? if ($property['column_litera'] == $litera): ?>selected<? endif; ?>>
                                    <?= $ARR_TITLE_ROW[$key] . "[" . $litera . "]" ?>
                                    </option>
            <? endforeach; ?>		
                            </select>
                        </td>
                        <td>
                            <select name="XLS_GLOBALS[bx][<?= $i ?>]">
                                    <? foreach ($arrJSPropCode as $key => $litera): ?>
                                    <option value="<?= $litera ?>" <? if ($property['field_code'] == $litera): ?>selected<? endif; ?>>
                                    <?= $arrJSPropName[$key] ?>
                                    </option>
            <? endforeach; ?>	
                            </select>
                        </td>
                        <td>
                            <select name="XLS_GLOBALS[modify_type][<?= $i ?>]">
                                    <? foreach ($arrModifyTypeCode as $key => $litera): ?>
                                    <option value="<?= $litera ?>" <? if ($property['action'] == $litera): ?>selected<? endif; ?>>
                                    <?= $arrModifyTypeName[$key] ?>
                                    </option>
            <? endforeach; ?>	
                            </select>
                        </td>
                        <td>
                            <select name="XLS_GLOBALS[modify_subaction][<?= $i ?>]">
                                    <? foreach ($arrModifySubactionCode as $key => $litera): ?>
                                    <option value="<?= $litera ?>" <? if ($property['subaction'] == $litera): ?>selected<? endif; ?>>
                                    <?= $arrModifySubactionName[$key] ?>
                                    </option>
            <? endforeach; ?>	
                            </select>
                        </td>
                        <td>
                            <input type="text" name="XLS_GLOBALS[modify_subaction_params][<?= $i ?>]" value="<?= $property['params'] ?>">
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="MCARTXLSDeleteRow(<?= $i ?>);
                                    return false;"><?= GetMessage("MCART_DELETE_ROW") ?></a>
                        </td>
                    </tr>
                    <? $i++;
                endforeach;
                ?>

            <? } ?>

    <? if ($inputFileType == 'CSV') { ?>
                <tr>
                    <td colspan="4">
                        <h4><?= GetMessage("PROPERTY_IDENTIFY_CSV") ?></h4>
                    </td>
                </tr>
        <? $bcheck = false; ?>
        <? $key = 0; ?>	
                <tr id='tr_etalon'>
                    <td>
                        <select name = "XLS_IDENTIFY[xls]">
                            <? foreach ($alfavit as $key1 => $val): ?>
                                <option value="<?= $val ?>" <? if (isset($identify_row) && ($identify_row['column_litera'] == $val)): ?>selected<? endif; ?>><?= $ARR_TITLE_ROW[$key1] . " (" . $val . ") " ?></option>
        <? endforeach; ?>
                    </td>

                    <td>
                        <select name = "XLS_IDENTIFY[bx]">
                            <?
                            $arrJSPropName = array();
                            $arrJSPropCode = array();
                            //$arrJSPropName[] = GetMessage("XLS_FLD_00");
                            //$arrJSPropCode[] = "0";
                            ?>
                            <? foreach ($SrcPropID as $kode => $name): ?>
                                <?
                                $arrJSPropName[] = $name;
                                $arrJSPropCode[] = $kode;
                                ?>
                                <option value="<?= $kode ?>" <? if (isset($identify_row) && ($identify_row['field_code'] == $kode)) echo " selected" ?>><?= $name ?></option>
        <? endforeach ?>
                        </select>
                    </td>

                    <td>
                        <?
                        $arrModifyTypeCode = array
                            (
                            "XLS_MODIFY_TYPE_NONE",
                        );
                        ?>
                        <select name = "XLS_IDENTIFY[modify_type]" >
                            <? foreach ($arrModifyTypeCode as $action): ?>
                                <option value="<?= $action ?>"  <? if (isset($identify_row) && ($identify_row['action'] == $action)) echo " selected" ?>><?= GetMessage($action) ?></option>
                    <? endforeach; ?>
                        </select>
                    </td>
                    <?
                    $arrModifyTypeName = array
                        (
                        GetMessage("XLS_MODIFY_TYPE_NONE"),
                    );
                    ?>
                    <td>
                        <?
                        $arrModifySubactionCode = array
                            (
                            "XLS_MODIFY_TYPE_NONE",
                            "XLS_DEL_SUBSTR"
                        );
                        $arrModifySubactionName = array
                            (
                            GetMessage("XLS_MODIFY_TYPE_NONE"),
                            GetMessage("XLS_DEL_SUBSTR")
                        );
                        ?>
                        <select name = "XLS_IDENTIFY[modify_subaction]" >
                            <? foreach ($arrModifySubactionCode as $subaction): ?>
                                <option value="<?= $subaction ?>"  <? if (isset($identify_row) && ($identify_row['action'] == $subaction)) echo " selected" ?>><?= GetMessage($subaction) ?></option>
        <? endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="XLS_IDENTIFY[modify_subaction_params]" value="<?= (isset($identify_row)) ? $identify_row['params'] : ''; ?>">
                    </td>

                </tr>
            <? } ?>

            <?
            $i = 1;
            foreach ($property_data as $property):
                ?>
                <tr id="delete_row_log_<?= $i ?>">
                    <td>
                        <select b_log_counter="<?= $i ?>" name="XLS_GLOBALS[xls][<?= $i ?>]">
                            <? foreach ($alfavit as $key => $litera): ?>
                                <option value="<?= $litera ?>" <? if ($property['column_litera'] == $litera): ?>selected<? endif; ?>>
            <?= $ARR_TITLE_ROW[$key] . "[" . $litera . "]" ?>
                                </option>
        <? endforeach; ?>		
                        </select>
                    </td>
                    <td>
                        <select name="XLS_GLOBALS[bx][<?= $i ?>]">
                            <? foreach ($arrJSPropCode as $key => $litera): ?>
                                <option value="<?= $litera ?>" <? if ($property['field_code'] == $litera): ?>selected<? endif; ?>>
            <?= $arrJSPropName[$key] ?>
                                </option>
        <? endforeach; ?>	
                        </select>
                    </td>
                    <td>
                        <select name="XLS_GLOBALS[modify_type][<?= $i ?>]">
                            <? foreach ($arrModifyTypeCode as $key => $litera): ?>
                                <option value="<?= $litera ?>" <? if ($property['action'] == $litera): ?>selected<? endif; ?>>
            <?= $arrModifyTypeName[$key] ?>
                                </option>
        <? endforeach; ?>	
                        </select>
                    </td>
                    <td>
                        <select name="XLS_GLOBALS[modify_subaction][<?= $i ?>]">
                            <? foreach ($arrModifySubactionCode as $key => $litera): ?>
                                <option value="<?= $litera ?>" <? if ($property['subaction'] == $litera): ?>selected<? endif; ?>>
            <?= $arrModifySubactionName[$key] ?>
                                </option>
        <? endforeach; ?>	
                        </select>
                    </td>
                    <td>
                        <input type="text" name="XLS_GLOBALS[modify_subaction_params][<?= $i ?>]" value="<?= $property['params'] ?>">
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="MCARTXLSDeleteRow(<?= $i ?>);
                                return false;"><?= GetMessage("MCART_DELETE_ROW") ?></a>
                    </td>
                </tr>
        <? $i++;
    endforeach;
    ?>

        </table>

        <a href="javascript:void(0);" onclick="AddCondition(
        <?= CUtil::phpToJSObject($alfavit) ?>,
        <?= CUtil::phpToJSObject($ARR_TITLE_ROW) ?>,
        <?= CUtil::phpToJSObject($arrJSPropCode) ?>,
        <?= CUtil::phpToJSObject($arrJSPropName) ?>,
        <?= CUtil::phpToJSObject($arrModifyTypeCode) ?>,
           <?= CUtil::phpToJSObject($arrModifyTypeName) ?>,
    <?= CUtil::phpToJSObject($arrModifySubactionCode) ?>,
    <?= CUtil::phpToJSObject($arrModifySubactionName) ?>
                );
                return false;"><?= GetMessage("ADD_CONDITION"); ?></a>
        </br>
        </br>

            <? //echo "<pre>";print_r($arrJSPropName);echo "</pre>"; ?>

    <? if ($inputFileType == 'Excel2007') { ?>
            <h4><input type="checkbox" class="make_translit_code"  name="make_translit_code" value="Y" <? if ($ARR_REAL_PROFILE['need_translit']) echo "checked='checked'" ?>">
                <?= GetMessage("MAKE_TRANSLIT_CODE") ?></h4>
            </br>
            </br>
            <h4><input type="checkbox" class="save_profile"  name="save_profile" value="Y">
        <?= GetMessage("XLS_SAVE_PROFILE") ?></h4>
            </br>
            <input type="text" name="profile_name" >
            </br>
            </br>
            </br>
    <? } ?>	
        <input type='hidden' name='catalog_base_price_id' value=<?= $BASE_PRICE_ID ?>>
        <input type='hidden' name='sku_iblock_id' value=<?= $SKU_IBLOCK_ID ?>>
        <input type='hidden' name='cml2_link_code' value=<?= $CML2_LINK_CODE ?>>
        <input type='hidden' name='firstColumn' value=<?= $firstColumn ?>>
        <input type='hidden' name='firstRow' value=<?= $firstRow ?>>
        <input type='hidden' name='titleRow' value=<?= $titleRow ?>>
        <input type='hidden' name='highestColumn' value=<?= $highestColumn ?>>

        <a href = "/bitrix/admin/mcart_xls_import.php"><?= GetMessage("STEP_BACK") ?></a>
        <input type="submit" name="next_step" value="<?= GetMessage("BEGIN_IMPORT") ?>">
    </form>

    <?
}
else {
    ?>
    <?= $errMess ?>

    <a href = "/bitrix/admin/mcart_xls_import.php"><?= GetMessage("STEP_BACK") ?></a>
    <?
}
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>