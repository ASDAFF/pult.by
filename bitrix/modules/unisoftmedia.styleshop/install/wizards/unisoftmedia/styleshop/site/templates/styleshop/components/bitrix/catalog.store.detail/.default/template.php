<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="store-detail row" itemscope itemtype = "http://schema.org/Product">
	
	<div class="col-sm-4 col-md-4 col-lg-3">
		<p class="store-detail-image">

			<?if(intval($arResult['IMAGE_ID']) > 0){
				?><img src="<?php echo CFile::GetFileArray($arResult['IMAGE_ID'])['SRC'] ?>" alt="<?php echo $arResult["TITLE"] ?>" title="<?php echo $arResult["TITLE"] ?>" /><?
			}?>
		</p>
	</div>
	<div class="col-sm-8 col-md-8 col-lg-9">
		<div class="store-detail-desc">
			<?if($arResult["DESCRIPTION"]):?>
				<p itemprop="description"><?=$arResult["DESCRIPTION"];?></p>
			<?endif;?>
			<?if($arResult["ADDRESS"]):?>
				<p itemprop="description"><span class="store-detail-name"><?=GetMessage("S_ADDRESS")?></span><span class="store-detail-value"><?=$arResult["ADDRESS"]?></span></p>
			<?endif?>
			<?if($arResult["PHONE"] != ''):?>
				<p itemprop="description"><span class="store-detail-name"><?=GetMessage("S_PHONE")?></span><span class="store-detail-value"><?=$arResult["PHONE"]?></p>
			<?endif?>
			<?if ($arResult["SCHEDULE"] != ''):?>
				<p itemprop="description"><span class="store-detail-name"><?=GetMessage("S_SCHEDULE")?></span><span class="store-detail-value"><?=$arResult["SCHEDULE"]?></p>
			<?endif?>
		</div>
		<?if(isset($arResult["LIST_URL"]))
		{
			?>
			<p class="catalog-item-links">
				<a href="<?=$arResult["LIST_URL"]?>"><?=GetMessage("BACK_STORE_LIST")?></a>
			</p>
			<?
		}?>
	</div>

	<div id="map" class="col-xs-12 store-detail-map">
		<?
		if(($arResult["GPS_N"]) != 0 && ($arResult["GPS_S"]) != 0)
		{
			$gpsN = substr($arResult["GPS_N"],0,15);
			$gpsS = substr($arResult["GPS_S"],0,15);
			$gpsText = $arResult["ADDRESS"];
			$gpsTextLen = strlen($arResult["ADDRESS"]);
			if($arResult["MAP"] == 0)
			{
				$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
						"INIT_MAP_TYPE" => "MAP",
						"MAP_DATA" => serialize(array("yandex_lat"=>$gpsN,"yandex_lon"=>$gpsS,"yandex_scale"=>11,"PLACEMARKS" => array( 0=>array("LON"=>$gpsS,"LAT"=>$gpsN,"TEXT"=>$arResult["ADDRESS"])))),
						"MAP_WIDTH" => "100%",
						"MAP_HEIGHT" => "400",
						"CONTROLS" => array(
							0 => "ZOOM",
						),
						"OPTIONS" => array(
							0 => "ENABLE_SCROLL_ZOOM",
							1 => "ENABLE_DBLCLICK_ZOOM",
							2 => "ENABLE_DRAGGING",
						),
						"MAP_ID" => ""
					),
					false
				);
			}
			else
			{
				$APPLICATION->IncludeComponent("bitrix:map.google.view", ".default", array(
						"INIT_MAP_TYPE" => "MAP",
						"MAP_DATA" => serialize(array("google_lat"=>$gpsN,"google_lon"=>$gpsS,"google_scale"=>11,"PLACEMARKS" => array( 0=>array("LON"=>$gpsS,"LAT"=>$gpsN,"TEXT"=>$arResult["ADDRESS"])))),
						"MAP_WIDTH" => "100%",
						"MAP_HEIGHT" => "400",
						"CONTROLS" => array(
							0 => "ZOOM",
						),
						"OPTIONS" => array(
							0 => "ENABLE_SCROLL_ZOOM",
							1 => "ENABLE_DBLCLICK_ZOOM",
							2 => "ENABLE_DRAGGING",
						),
						"MAP_ID" => ""
					),
					false
				);
			}
		}
		?>
	</div>
</div>