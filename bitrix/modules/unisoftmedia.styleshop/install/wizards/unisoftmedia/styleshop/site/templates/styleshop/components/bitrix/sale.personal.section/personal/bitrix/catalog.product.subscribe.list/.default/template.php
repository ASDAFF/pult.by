<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;

$randomString = $this->randString();

$APPLICATION->setTitle(Loc::getMessage('CPSL_SUBSCRIBE_TITLE_NEW'));
if(!$arResult['USER_ID'] && !isset($arParams['GUEST_ACCESS'])):?>
	<?
	$contactTypeCount = count($arResult['CONTACT_TYPES']);
	$authStyle = 'display: block;';
	$identificationStyle = 'display: none;';
	if(!empty($_GET['result']))
	{
		$authStyle = 'display: none;';
		$identificationStyle = 'display: block;';
	}
	?>
	<div class="row">
		<div class="col-md-8 col-sm-7">
			<div class="alert alert-danger"><?=Loc::getMessage('CPSL_SUBSCRIBE_PAGE_TITLE_AUTHORIZE')?></div>
		</div>
		<? $authListGetParams = array(); ?>
		<div class="col-md-8 col-sm-7" id="catalog-subscriber-auth-form" style="<?=$authStyle?>">
			<?$APPLICATION->authForm('', false, false, 'N', false);?>
			<hr class="bxe-light">
		</div>
		<?$APPLICATION->setTitle(Loc::getMessage('CPSL_TITLE_PAGE_WHEN_ACCESSING'));?>
		<div id="catalog-subscriber-identification-form" style="<?=$identificationStyle?>">
		<div class="col-md-8 col-sm-7 catalog-subscriber-identification-form">
			<h4><?=Loc::getMessage('CPSL_HEADLINE_FORM_SEND_CODE')?></h4>
			<hr class="bxe-light">
			<form method="post">
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="siteId" value="<?=SITE_ID?>">
				<?if($contactTypeCount > 1):?>
					<div class="form-group">
						<label for="contactType"><?=Loc::getMessage('CPSL_CONTACT_TYPE_SELECTION')?></label>
						<select id="contactType" class="form-control" name="contactType">
							<?foreach($arResult['CONTACT_TYPES'] as $contactTypeData):?>
								<option value="<?=intval($contactTypeData['ID'])?>">
									<?=htmlspecialcharsbx($contactTypeData['NAME'])?></option>
							<?endforeach;?>
						</select>
					</div>
				<?endif;?>
				<div class="form-group">
					<?
						$contactLable = Loc::getMessage('CPSL_CONTACT_TYPE_NAME');
						$contactTypeId = 0;
						if($contactTypeCount == 1)
						{
							$contactType = current($arResult['CONTACT_TYPES']);
							$contactLable = $contactType['NAME'];
							$contactTypeId = $contactType['ID'];
						}
					?>
					<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
					<input type="text" class="form-control" name="userContact" id="contactInput">
					<input type="hidden" name="subscriberIdentification" value="Y">
					<?if($contactTypeId):?>
						<input type="hidden" name="contactType" value="<?=$contactTypeId?>">
					<?endif;?>
				</div>
				<button type="submit" class="btn btn-default"><?=Loc::getMessage('CPSL_BUTTON_SUBMIT_CODE')?></button>
			</form>
		</div>
		<div class="col-md-8 col-sm-7">
			<h4><?=Loc::getMessage('CPSL_HEADLINE_FORM_FOR_ACCESSING')?></h4>
			<hr class="bxe-light">
			<form method="post">
				<?=bitrix_sessid_post()?>
				<div class="form-group">
					<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
					<input type="text" class="form-control" name="userContact" id="contactInput" value=
						"<?=!empty($_GET['contact']) ? htmlspecialcharsbx(urldecode($_GET['contact'])): ''?>">
				</div>
				<div class="form-group">
					<label for="token"><?=Loc::getMessage('CPSL_CODE_LABLE')?></label>
					<input type="text" class="form-control" name="subscribeToken" id="token">
					<input type="hidden" name="accessCodeVerification" value="Y">
				</div>
				<button type="submit" class="btn btn-default"><?=Loc::getMessage('CPSL_BUTTON_SUBMIT_ACCESS')?></button>
			</form>
		</div>
		</div>
	</div>
	<script type="text/javascript">
		BX.ready(function() {
			if(BX('cpsl-auth'))
			{
				BX.bind(BX('cpsl-auth'), 'click', BX.delegate(showAuthForm, this));
				BX.bind(BX('cpsl-identification'), 'click', BX.delegate(showAuthForm, this));
			}
			function showAuthForm()
			{
				var formType = BX.proxy_context.id.replace('cpsl-', '');
				var authForm = BX('catalog-subscriber-auth-form'),
					codeForm = BX('catalog-subscriber-identification-form');
				if(!authForm || !codeForm || !BX('catalog-subscriber-'+formType+'-form')) return;

				BX.style(authForm, 'display', 'none');
				BX.style(codeForm, 'display', 'none');
				BX.style(BX('catalog-subscriber-'+formType+'-form'), 'display', '');
			}
		});
	</script>
<?endif;

?>
<script type="text/javascript">
	BX.message({
		CPSL_MESS_BTN_DETAIL: '<?=('' != $arParams['MESS_BTN_DETAIL']
			? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL'));?>',

		CPSL_MESS_NOT_AVAILABLE: '<?=('' != $arParams['MESS_BTN_DETAIL']
			? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL'));?>',
		CPSL_BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_BASKET_REDIRECT');?>',
		CPSL_BASKET_URL: '<?=$arParams["BASKET_URL"];?>',
		CPSL_TITLE_ERROR: '<?=GetMessageJS('CPSL_CATALOG_TITLE_ERROR') ?>',
		CPSL_TITLE_BASKET_PROPS: '<?=GetMessageJS('CPSL_CATALOG_TITLE_BASKET_PROPS') ?>',
		CPSL_BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CPSL_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		CPSL_BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_SEND_PROPS');?>',
		CPSL_BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		CPSL_STATUS_SUCCESS: '<?=GetMessageJS('CPSL_STATUS_SUCCESS');?>',
		CPSL_STATUS_ERROR: '<?=GetMessageJS('CPSL_STATUS_ERROR') ?>'
	});
</script>
<?

if(!empty($_GET['result']) && !empty($_GET['message']))
{
	$successNotify = strpos($_GET['result'], 'Ok') ? true : false;
	$postfix = $successNotify ? 'Ok' : 'Fail';
	$popupTitle = Loc::getMessage('CPSL_SUBSCRIBE_POPUP_TITLE_'.strtoupper(str_replace($postfix, '', $_GET['result'])));

	$arJSParams = array(
		'NOTIFY_USER' => true,
		'NOTIFY_POPUP_TITLE' => $popupTitle,
		'NOTIFY_SUCCESS' => $successNotify,
		'NOTIFY_MESSAGE' => urldecode($_GET['message']),
	);
	?>
	<script type="text/javascript">
		var <?='jaClass_'.$randomString;?> = new JCCatalogProductSubscribeList(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
	</script>
	<?
}

if (!empty($arResult['ITEMS']))
{
	$arSkuTemplate = array();
	if (!empty($arResult['SKU_PROPS'])) {

		$skuTooltip = ($arParams['USE_SKU_TOOLTIP'] == 'Y');

		foreach($arResult['SKU_PROPS'] as $skuProps)
		{
			foreach($skuProps as $arProp)
			{
				$propId = $arProp['ID'];

	      if(is_array($arProp['VALUES']) && !empty($arProp['VALUES'])){
	        foreach($arProp['VALUES'] as $key => $arOneValue)
	        {
	          if('PICT' == $arProp['SHOW_MODE']) {

		          $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.htmlspecialcharsbx($arOneValue["NAME"]).'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="color cnt" title="'.htmlspecialcharsbx($arOneValue["NAME"]).'"><span class="cnt_item" style="background-image:url('.$arOneValue["PICT"]["SRC"].');"></span></span></li>';
		
		        } else if('TEXT' == $arProp['SHOW_MODE']) {

		        	$descTooltip = '';
		        	if(isset($arOneValue['DESCRIPTION']) && $skuTooltip)
		        	{
		        		$descTooltip .= 'data-container="body" ';
	              $descTooltip .= 'data-toggle="popover" ';
	              $descTooltip .= 'data-placement="top" ';
	              $descTooltip .= 'data-html="true" ';
	              $descTooltip .= 'data-content="'.$arOneValue["DESCRIPTION"].'"';
		        	}
		
		         $arSkuTemplate[$propId][$arOneValue['ID']] = '<li style="display: none;" '.$descTooltip.' data-onevalue="'.$arOneValue["ID"].'" data-sku-title="'.htmlspecialcharsbx($arOneValue["NAME"]).'" data-treevalue="'.$arProp["ID"]."_".$arOneValue["ID"].'"><span class="size cnt" title="'.htmlspecialcharsbx($arOneValue['NAME']).'">'.htmlspecialcharsbx($arOneValue['NAME']).'</span></li>';
		
		        }
	      	}
	      }
	    }
	  }
	}

	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => Loc::getMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
	<div class="catalog-products-container">
		<?if($arParams['MESS_TITLE']) {?>
			<h2 class="product-title"><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></h2>
		<?}
?>
		<div class="row">
			<div class="unproduct">
				<?foreach ($arResult['ITEMS'] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
					$strMainID = $this->GetEditAreaId($arItem['ID']);

					$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

					$OFFERS = (isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))? true : false;

					$arrMainID = array(
						'ID' => $strMainID,
						'NAME' => $strMainID.'_name',
						'PICT' => $strMainID.'_pict',
						'SECOND_PICT' => $strMainID.'_secondpict',
						'MAIN_PROPS' => $strMainID.'_main_props',
				
						'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
						'AVAILABLE' => $strMainID.'_available',
						'NAME_MEASURE' => $strMainID.'_name_measure',
						'BUY_LINK' => $strMainID.'_buy_link',
						'COMPARE' => $strMainID.'_compare',
						'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
				
						'PRICE' => $strMainID.'_price',
						'OLD_PRICE' => $strMainID.'_old_price',
						'DSC_PERC' => $strMainID.'_dsc_perc',
						'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
				
						'PROP_DIV' => $strMainID.'_sku_tree',
						'PROP' => $strMainID.'_prop_',
						'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
						'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail'
				   );
				   $arJSParams = array(
						'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
						'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
						'SHOW_ADD_BASKET_BTN' => false,
						'SHOW_BUY_BTN' => true,
						'SHOW_ABSENT' => true,
						'USE_SUBSCRIBE' => true,
						'SHOW_SKU_PROPS' => $arItem['OFFERS_PROPS_DISPLAY'],
						'SECOND_PICT' => $arItem['SECOND_PICT'],
						'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
						'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
						'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
						'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
						'DEFAULT_PICTURE' => array(
							'PICTURE' => $arItem['PRODUCT_PREVIEW'],
							'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
						),
						'PRODUCT' => [
							'ID' => $arItem['ID'],
							'NAME' => $arrMainID['NAME'],
						],
						'VISUAL' => array(
							'ID' => $arrMainID['ID'],
							'NAME' => $arrMainID['NAME'],
							'AVAILABLE_ID' => $arrMainID['AVAILABLE'],
							'PICT_ID' => $arrMainID['PICT'],
							'SECOND_PICT_ID' => $arrMainID['SECOND_PICT'],
							'QUANTITY_MEASURE' => $arrMainID['QUANTITY_MEASURE'],
							'NAME_MEASURE' => $arrMainID['NAME_MEASURE'],
							'PRICE_ID' => $arrMainID['PRICE'],
							'OLD_PRICE_ID' => $arrMainID['OLD_PRICE'],
							'TREE_ID' => $arrMainID['PROP_DIV'],
							'TREE_ITEM_ID' => $arrMainID['PROP'],
							'BUY_ID' => $arrMainID['BUY_LINK'],
							'COMPARE_ID' => $arrMainID['COMPARE'],
							'ADD_BASKET_ID' => $arrMainID['ADD_BASKET_ID'],
							'DSC_PERC' => $arrMainID['DSC_PERC'],
							'SECOND_DSC_PERC' => $arrMainID['SECOND_DSC_PERC'],
							'DISPLAY_PROP_DIV' => $arrMainID['DISPLAY_PROP_DIV'],
							'IN_STOCK' => $arParams['MESS_IN_AVAILABLE'],
					    'NOT_STOCK' => $arParams['MESS_NOT_AVAILABLE'],
					    'SUBSCRIBE_ID' => $arrMainID['SUBSCRIBE_LINK'],
					    'NOT_AVAILABLE_MESS' => $arrMainID['NOT_AVAILABLE_MESS']
						),
						'OFFERS' => $arItem['JS_OFFERS'],
						'OFFER_SELECTED' => $arItem['OFFERS_SELECTED'],
						'QUICKBUY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
						'PRODUCTDAY_ID' => $arItem['PROPERTIES']['ID']['VALUE'],
						'QUICKBUY_PROP' => $arItem['PROPERTIES']['QUICKBUY']['VALUE'],
						'PRODUCTDAY_PROP' => $arItem['PROPERTIES']['PRODUCTDAY']['VALUE'],
						'TREE_PROPS' => $arSkuProps
					);

					$stickerNew   = '';
					$stickerHit   = '';
					if('Y' == $arItem['PROPERTIES']['NEWPRODUCT']['VALUE']) {
						$stickerNew = ' newproduct';
					}
					if('Y' == $arItem['PROPERTIES']['HIT']['VALUE']) {
						$stickerHit = ' hitproduct';
					}
?>
					<article class="unproduct-item js-item col-xs-6 col-sm-4 col-md-4 col-lg-3" id="<?php echo $strMainID ?>">
						<div class="unproduct-container">
							<?/********************************************** IMAGES CONTAINER ************************************************/
							if($arParams['SHOW_IMAGE'] == 'Y')
							{?>
								<figure class="unproduct-image-container">
								<?/********************** sticker ************************/?>
								<div class="sticker">
									<span class="stickernew"<?php echo ('' == $stickerNew)? ' style="display: none;"' : '' ?>>
										<span class="text"><?php echo $arItem['PROPERTIES']['NEWPRODUCT']['NAME']?></span>
									</span>
									<span class="stickerhit"<?php echo ('' == $stickerHit)? ' style="display: none;"' : '' ?>>
										<span class="text"><?php echo $arItem['PROPERTIES']['HIT']['NAME']?></span>
									</span>
								</div>
								<?/********************** sticker end ************************/
									/********************** IMAGE ************************/?>
								<a class="unproduct-image-link" href="<?php echo $arItem['DETAIL_PAGE_URL']?>">
									<?if(is_array($arItem['PREVIEW_PICTURE']))
									{?>
										<img class="picture__first" id="<?php echo $arJSParams['VISUAL']['PICT_ID']?>" src="<?php echo $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $arItem['NAME']?>" />
									<?}
									if('Y' == $arParams['HOVER_IMAGE'])
									{?>
										<img class="picture__second" id="<?php echo $arJSParams['VISUAL']['SECOND_PICT_ID']?>" src="<?php echo (!empty($arItem['PREVIEW_PICTURE_SECOND']))? $arItem['PREVIEW_PICTURE_SECOND']['SRC'] : $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?php echo $arItem['NAME']?>" />
									<?}?>
								</a>
								<?/********************** IMAGE end ************************/
									/************ COMPARE, QUICK LOOK, FAVORITES **********/
								if($OFFERS && !empty($arItem['OFFERS_PROP']))
								{?>
									<div class="mask hidden-xs">
										<?/********************************* SKU *************************************/
											$arSkuProps = array();
											if (!empty($arResult['SKU_PROPS'])) {
												?>
										      <div class="block-sku" id="<?php echo $arJSParams['VISUAL']['TREE_ID'] ?>"><?
														foreach($arResult['SKU_PROPS'] as $skuProps)
														{
															foreach($skuProps as $arProp)
															{
																if (!isset($arItem['SKU_TREE_VALUES'][$arProp['ID']]))
																	continue;

																$arSkuProps[] = array(
																	'ID' => $arProp['ID'],
																	'SHOW_MODE' => $arProp['SHOW_MODE'],
																	'TYPE' => $arProp['PROPERTY_TYPE'],
																	'VALUES_COUNT' => $arProp['VALUES_COUNT']
																);
										            if(is_array($arProp['VALUES']) && !empty($arProp['VALUES'])){?>
									                <div class="block-sku-detail-sku" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_cont">
									                    <span class="block-sku-detail-sku-name" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_selected"><?php echo htmlspecialcharsex($arProp['NAME']); ?>:
									                        <span class="selection"></span>
									                    </span>
									                    <div class="block-sku-detail-sku-container">
									                        <ul class="block-sku-detail-sku-container-list" id="<?php echo $arrMainID['PROP'].$arProp['ID']; ?>_list">
									                        	<?foreach($arSkuTemplate[$arProp['ID']] as $value => $valueItem)
									                        	{
									                        		if (!isset($arItem['SKU_TREE_VALUES'][$arProp['ID']][$value]))
																								continue;
																							echo $valueItem;
									                        	}
									                        		unset($value, $valueItem);
									                        	?>
									                        </ul>
									                    </div>
									                </div>
									            <?}
										          }
										        }?>
										      </div>
										    <?
											}
											$arJSParams['TREE_PROPS'] = $arSkuProps;
									/********************************* SKU end *************************************/
										if($arParams['USE_QUICKVIEW'] == 'Y')
										{?>
											<a href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>" class="quick-view"><span><?php echo Loc::getMessage('MSG_QUICK_VIEW')?></span></a>
										<?}?>
									</div>
								<?}
								if($arParams['USE_QUICKVIEW'] == 'Y' && empty($arItem['OFFERS_PROP']))
								{?>
									<a href="<?php echo $arItem['DETAIL_PAGE_URL'] ?>" class="quick-view hidden-xs"><span><?php echo Loc::getMessage('MSG_QUICK_VIEW')?></span></a>
								<?}
								/************ COMPARE, QUICK LOOK, FAVORITES end **********/?>
								</figure>
							<?}
							/********************************************** /IMAGES CONTAINER ************************************************/?>
							<div class="unproduct-bottom-container">
								<?if ($arParams['SHOW_NAME'] == 'Y')
								{?>
									<div class="name_unproduct">
										<a title="<?php echo $arItem['NAME']?>" href="<?php echo $arItem['DETAIL_PAGE_URL']?>"><?php echo $arItem['NAME']?></a>
									</div>
								<?}?>
								<div class="block_list">
										<div class="content_price">
											<span id="<?php echo $arJSParams['VISUAL']['PRICE_ID'] ?>" class="price"></span>
										</div>
									<div class="button-container hidden-xs">
										<a
										id="<?php echo $arrMainID['SUBSCRIBE_LINK'] ?>"
										class="btn btn-primary subscribe-delete"
										data-item="<?php echo $arItem['ID'] ?>"
										data-list-subscriptions="<?php echo CUtil::PhpToJSObject($arParams['LIST_SUBSCRIPTIONS'], false, true); ?>"
										role="button" title="<?php echo Loc::getMessage('CPSL_TPL_MESS_BTN_UNSUBSCRIBE') ?>" href="#">
											<span class="text"><?php echo Loc::getMessage('CPSL_TPL_MESS_BTN_UNSUBSCRIBE') ?></span>
										</a>
									</div>
								</div>
							</div>
						</div>
						<?if(!empty($arItem['OFFERS_PROP']) && $OFFERS) {
							?><script type="text/javascript">
								var <? echo $strObName; ?> = new JSCatalog(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
							</script><?
						}?>
					</article>
				<?}?>
			</div>
		</div>
	</div>
	<?
}
else
{
	if(isset($arParams['GUEST_ACCESS'])):
		echo '<h3>'.Loc::getMessage('CPSL_SUBSCRIBE_NOT_FOUND').'</h3>';
	endif;
}
?>