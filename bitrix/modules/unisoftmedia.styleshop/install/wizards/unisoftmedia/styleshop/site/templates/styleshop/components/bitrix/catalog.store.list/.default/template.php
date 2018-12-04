<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
if(strlen($arResult["ERROR_MESSAGE"])>0)
	ShowError($arResult["ERROR_MESSAGE"]);
$arPlacemarks = array();
$gpsN = '';
$gpsS = '';
$store = '';
ob_start();?>
<?if(is_array($arResult["STORES"]) && !empty($arResult["STORES"])):?>
	<div class="store-list">
		<div class="row">
			<?foreach($arResult["STORES"] as $pid=>$arProperty):?>
				<div class="store-list-item col-xs-12">
					<div class="row">
						<?if(is_array($arProperty['DETAIL_IMG'])):?>
							<div class="col-sm-2 col-md-2 col-lg-2">
								<p><a href="<?php echo $arProperty["URL"]?>" title="<?php echo $arProperty['TITLE'] ?>"><img src="<?php echo $arProperty['DETAIL_IMG']['SRC'] ?>" alt="<?php echo $arProperty['TITLE'] ?>" title="<?php echo $arProperty['TITLE'] ?>" /></a></p>
							</div>
						<?endif?>
						<div class="col-sm-4 col-md-4 col-lg-4">
							<h4 class="store-list-name"><a href="<?=$arProperty["URL"]?>"><?php echo $arProperty["TITLE"]?></a></h4>
						</div>
						<div class="col-sm-3 col-md-3 col-lg-4">
							<?if(isset($arProperty['SCHEDULE'])):?>
								<p><?php echo $arProperty['SCHEDULE'] ?></p>
							<?endif?>
						</div>
						<div class="col-sm-3 col-md-3 col-lg-2">
							<?if(isset($arProperty['PHONE'])):?>
								<a rel="nofollow" href="tel:<?php echo $arProperty['PHONE'] ?>"><?php echo $arProperty['PHONE'] ?></a>
							<?endif?>
						</div>
					</div>
				</div>
				<?if($arProperty["GPS_S"]!=0 && $arProperty["GPS_N"]!=0)
				{
					$gpsN=substr(doubleval($arProperty["GPS_N"]),0,15);
					$gpsS=substr(doubleval($arProperty["GPS_S"]),0,15);
					$arPlacemarks[]=array("LON"=>$gpsS,"LAT"=>$gpsN,"TEXT"=>$arProperty["TITLE"]);
				}?>
			<?endforeach?>
		</div>
	</div>
<?endif;?>
<?
$store = ob_get_contents();
ob_end_clean();

if ($arResult['VIEW_MAP'])
{
	?><div class="store-map"><?
		if($arResult["MAP"]==0)
		{
			$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
					"INIT_MAP_TYPE" => "MAP",
					"MAP_DATA" => serialize(array("yandex_lat"=>$gpsN,"yandex_lon"=>$gpsS,"yandex_scale"=>10,"PLACEMARKS" => $arPlacemarks)),
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
				$component,
				array("HIDE_ICONS" => "Y")
			);
		}
		else
		{
			$APPLICATION->IncludeComponent("bitrix:map.google.view", ".default", array(
					"INIT_MAP_TYPE" => "MAP",
					"MAP_DATA" => serialize(array("google_lat"=>$gpsN,"google_lon"=>$gpsS,"google_scale"=>10,"PLACEMARKS" => $arPlacemarks)),
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
				$component,
				array("HIDE_ICONS" => "Y")
			);
		}
	?></div><?
}
echo $store;