<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (\Bitrix\Main\Loader::includeModule('iblock'))
{
    $arFilter = array(
        "TYPE" => $arParams['ROOT_MENU_TYPE'],
        "SITE_ID" => SITE_ID,
        "ACTIVE" => "Y"
    );

    $arSections = array();
    $obCache = new CPHPCache();
    if ($obCache->InitCache($arParams['MENU_CACHE_TIME'], serialize($arFilter), "/iblock/menu"))
    {
        $arSections = $obCache->GetVars();
    }
    elseif ($obCache->StartDataCache())
    {
        $dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
        $dbIBlock = new CIBlockResult($dbIBlock);
        $curIblockID = 0;

        $arSelect = array(
            'ID',
            'IBLOCK_ID',
            'DETAIL_PICTURE',
            'NAME',
            'SECTION_PAGE_URL',
            'UF_MENU_TYPE',
            'UF_IMAGE_MENU',
            'UF_INDENT',
            'UF_INDENT_BOTTOM'
            );

        if ($arIBlock = $dbIBlock->GetNext())
        {
            $arFilter = Array(
                'IBLOCK_ID'     => $arIBlock["ID"],
                'GLOBAL_ACTIVE' => 'Y',
                'DEPTH_LEVEL'   => 1
            );
            $dbSections = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
            while($ar_result = $dbSections->GetNext())
            {
                $ar_result['UF_IMAGE_MENU'] = intval($ar_result['UF_IMAGE_MENU']);
                if(0 < $ar_result['UF_IMAGE_MENU']) {
                    $ar_result['UF_IMAGE_MENU'] = CFile::GetFileArray($ar_result['UF_IMAGE_MENU']);
                }

                if($ar_result['UF_INDENT'])
                    $ar_result['UF_INDENT'] = intval($ar_result['UF_INDENT']);
                else
                    $ar_result['UF_INDENT'] = 0;

                if($ar_result['UF_INDENT_BOTTOM'])
                    $ar_result['UF_INDENT_BOTTOM'] = intval($ar_result['UF_INDENT_BOTTOM']);
                else
                    $ar_result['UF_INDENT_BOTTOM'] = 0;

                if($ar_result['UF_MENU_TYPE']) {
                   $rsUsers = CUser::GetList($by = '', $order = '', array("SELECT" => array("UF_MENU_TYPE")));
                   while ($rsUsers->GetNext())
                   {
                      $rsGender = CUserFieldEnum::GetList(array(), array("ID" => intval($ar_result['UF_MENU_TYPE'])));
                      if ($arCat = $rsGender->GetNext()) {
                         $ar_result['UF_MENU_TYPE'] = $arCat['VALUE'];
                        }
                    }
                }
                $arSections[$ar_result['SECTION_PAGE_URL']] = $ar_result;
            }

            $arSections['IBLOCK']['NAME'] = $arIBlock['NAME'];
            $arSections['IBLOCK']['LIST_PAGE_URL'] = $arIBlock['LIST_PAGE_URL'];

            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/menu");
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);
                $CACHE_MANAGER->EndTagCache();
            }
        }
        $obCache->EndDataCache($arSections);
    }

        $arRes = array();
        foreach($arResult as $k => $arItem)
        {
            $arRes[$k] = $arItem;
            if($arItem['DEPTH_LEVEL'] == 1 && isset($arSections[$arItem['LINK']])) {
              $arRes[$k]['UF_MENU_TYPE'] = $arSections[$arItem['LINK']]['UF_MENU_TYPE'];
              $arRes[$k]['UF_IMAGE_MENU'] = $arSections[$arItem['LINK']]['UF_IMAGE_MENU'];
              $arRes[$k]['UF_INDENT'] = $arSections[$arItem['LINK']]['UF_INDENT'];
              $arRes[$k]['UF_INDENT_BOTTOM'] = $arSections[$arItem['LINK']]['UF_INDENT_BOTTOM'];
          }
        }
        $arRes['IBLOCK'] = $arSections['IBLOCK'];

}
$arResult = array();
$arResult = $arRes;
