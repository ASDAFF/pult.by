<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
use Bitrix\Main\Localization\Loc;

$APPLICATION->SetTitle(Loc::getMessage('COMPARE_HEADER'));
?><h1><?$APPLICATION->ShowTitle(false);?></h1><?

$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");


?><div class="bx_compare" id="bx_catalog_compare_block"><?
if ($isAjax)
{
	$APPLICATION->RestartBuffer();
}
?><p class="bx_sort_container">
	<a class="btn sortbutton<? echo (!$arResult["DIFFERENT"] ? ' btn-primary current' : ' btn-default'); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N'; ?>" rel="nofollow"><?=Loc::getMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
	<a class="btn sortbutton<? echo ($arResult["DIFFERENT"] ? ' btn-primary current' : ' btn-default'); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y'; ?>" rel="nofollow"><?=Loc::getMessage("CATALOG_ONLY_DIFFERENT")?></a>
</p>
<div class="bx_compare-left pull-left">
<table class="table">
	<?if (!empty($arResult["SHOW_FIELDS"]))
	{
		foreach ($arResult["SHOW_FIELDS"] as $code => $arProp)
		{
			$showRow = true;
			if ((!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT']) && count($arResult["ITEMS"]) > 1)
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as $arElement)
				{
					$arPropertyValue = $arElement["FIELDS"][$code];
					if (is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}
			if ($showRow)
			{
				?><tr>
				<?switch($code)
				{
					case "NAME":
						
						break;
					case "PREVIEW_PICTURE":
					case "DETAIL_PICTURE":
							?><td><div style="height:150px"></div><div class="invisible"><p><?php echo Loc::getMessage("IBLOCK_FIELD_".$code) ?></p></div></td><?
						break;
					default:
						echo '<td>'.Loc::getMessage("IBLOCK_FIELD_".$code).'</td>';
						break;
				}?>
				<?
				unset($arElement);
			}
		?>
		</tr>
		<?
		}
	}

	if (!empty($arResult["SHOW_OFFER_FIELDS"]))
	{
		foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp)
		{
			$showRow = true;
			if ($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as $arElement)
				{
					$Value = $arElement["OFFER_FIELDS"][$code];
					if(is_array($Value))
					{
						sort($Value);
						$Value = implode(" / ", $Value);
					}
					$arCompare[] = $Value;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}
			if ($showRow)
			{
			?>
			<tr>
				<td><?=Loc::getMessage("IBLOCK_OFFER_FIELD_".$code)?></td>
				<?unset($arElement);
				?>
			</tr>
			<?
			}
		}
	}
	?>
	<tr>
		<td><?=Loc::getMessage('CATALOG_COMPARE_PRICE');?></td>
	</tr>

	<?
	if (!empty($arResult["SHOW_PROPERTIES"]))
	{
		foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty)
		{
			$showRow = true;
			if ($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as $arElement)
				{
					$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
					if (is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}

			if ($showRow)
			{
				?>
				<tr>
					<td><?=$arProperty["NAME"]?></td>
					<?
					unset($arElement);
					?>
				</tr>
			<?
			}
		}
	}

if (!empty($arResult["SHOW_OFFER_PROPERTIES"]))
{
	foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arElement);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
		?>
		<tr>
			<td><?=$arProperty["NAME"]?></td>
			<?
			unset($arElement);
			?>
		</tr>
		<?
		}
	}
}
	?>

</table>
</div>
<div class="bx_compare-right">
<div class="table-responsive">
<table class="table">
<?
if (!empty($arResult["SHOW_FIELDS"]))
{
	foreach ($arResult["SHOW_FIELDS"] as $code => $arProp)
	{
		$showRow = true;
		if ((!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT']) && count($arResult["ITEMS"]) > 1)
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["FIELDS"][$code];
				if (is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arElement);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
			?><tr><?
			foreach($arResult["ITEMS"] as $arElement)
			{
				switch($code)
				{
					case "NAME":
						
						break;
					case "PREVIEW_PICTURE":
					case "DETAIL_PICTURE":
						?><td valign="top">
							<div class="image">
								<a class="remove" onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arElement['~DELETE_URL'])?>');" href="javascript:void(0)" title="<?=Loc::getMessage("CATALOG_REMOVE_PRODUCT")?>"></a><?
								if(is_array($arElement["FIELDS"][$code])):?>
									<p>
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img
									class="img-thumbnail"
									src="<?=$arElement["FIELDS"][$code]["SRC"]?>"
									width="auto"
									height="150"
									alt="<?=$arElement["FIELDS"][$code]["ALT"]?>"
									title="<?=$arElement["FIELDS"][$code]["TITLE"]?>"
									/></a>
									<?else:?>
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img
									class="img-thumbnail"
									src="<?=$this->GetFolder().'/images/no_photo.png'?>"
									width="auto"
									height="150"
									alt=""
									/></a>
									</p>
								<?endif;
								?><div><a class="bx_compare-name" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></div>
							</div>
						</td>
						<?
						break;
					default:
						echo '<td valign="top"><'.$arElement["FIELDS"][$code].'</td>';
						break;
				}
			}
			unset($arElement);
		}
	?>
	</tr>
	<?
	}
}

if (!empty($arResult["SHOW_OFFER_FIELDS"]))
{
	foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$Value = $arElement["OFFER_FIELDS"][$code];
				if(is_array($Value))
				{
					sort($Value);
					$Value = implode(" / ", $Value);
				}
				$arCompare[] = $Value;
			}
			unset($arElement);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
		?>
		<tr>
			<?foreach($arResult["ITEMS"] as $arElement)
			{
			?>
			<td>
				<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
			</td>
			<?
			}
			unset($arElement);
			?>
		</tr>
		<?
		}
	}
}
?>
<tr>
	<?
	foreach ($arResult["ITEMS"] as $arElement)
	{
		if (isset($arElement['MIN_PRICE']) && is_array($arElement['MIN_PRICE']))
		{
			?><td><? echo $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></td><?
		}
		else if(isset($arElement['PROPERTIES']['MINIMUM_PRICE']) && !empty($arElement['PROPERTIES']['MINIMUM_PRICE']['VALUE']))
		{
			?><td><?php echo Loc::getMessage("PRICE", array('%price%' => CCurrencyLang::CurrencyFormat($arElement['PROPERTIES']['MINIMUM_PRICE']['VALUE'], $arResult['CONVERT_CURRENCY']['CURRENCY_ID']))) ?></td><?
		}
		else
		{
			?><td>&nbsp;</td><?
		}
	}
	unset($arElement);
	?>
</tr>
<?
if (!empty($arResult["SHOW_PROPERTIES"]))
{
	foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
				if (is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arElement);
			$showRow = (count(array_unique($arCompare)) > 1);
		}

		if ($showRow)
		{
			?>
			<tr>
				<?foreach($arResult["ITEMS"] as $arElement)
				{
					?>
					<td>
						<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
					</td>
				<?
				}
				unset($arElement);
				?>
			</tr>
		<?
		}
	}
}

if (!empty($arResult["SHOW_OFFER_PROPERTIES"]))
{
	foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty)
	{
		$showRow = true;
		if ($arResult['DIFFERENT'])
		{
			$arCompare = array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arElement);
			$showRow = (count(array_unique($arCompare)) > 1);
		}
		if ($showRow)
		{
		?>
		<tr>
			<?foreach($arResult["ITEMS"] as $arElement)
			{
			?>
			<td>
				<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
			</td>
			<?
			}
			unset($arElement);
			?>
		</tr>
		<?
		}
	}
}
	?>
</table>
</div>
</div>
<?
if ($isAjax)
{
	die();
}
?>
</div>
<script>
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>