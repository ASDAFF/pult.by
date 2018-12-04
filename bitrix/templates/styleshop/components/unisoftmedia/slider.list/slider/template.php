<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!empty($arResult['ITEMS']))
	{
?>
		<div class="owlslider">
				<?php 
				$sliderOptions = array();
				$sliderOptions['nav'] =  ($arParams['SLIDER_NAV'] == 'Y');
				$sliderOptions['smartSpeed'] = $arParams['SLIDER_SPEED'];
				$sliderOptions['autoplay'] = ($arParams['SLIDER_AUTOPLAY'] == 'Y');
				$sliderOptions['autoplayTimeout'] = $arParams['SLIDER_SHOW_SPEED'];
				$sliderOptions['autoplayHoverPause'] = ($arParams['SLIDER_AUTOPLAY_HOVER_PAUSE'] == 'Y');
				$sliderOptions['mouseDrag'] = ($arParams['MOUSE_DRAG'] == 'Y');
				$sliderOptions['loop'] = ($arParams['SLIDER_LOOP'] == 'Y');
				$sliderOptions['dots'] = ($arParams['SLIDER_DOTS'] == 'Y');
				$sliderOptions['items'] = 1;
				$sliderOptions['responsive'] = [
					'0' => [
						'nav' => false
					],
					'767' => [
						'nav' => $sliderOptions['nav']
					]
				];
				?>
				<ul class="list-unstyled owl-carousel Owlcarousel" data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>">
					<?foreach($arResult['ITEMS'] as $arItem):

					if(!is_array($arItem['DETAIL_PICTURE']))
						continue;

					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
				<li class="item item-image__container" id="<?=$this->GetEditAreaId($arItem['ID'])?>"<?php if(intval($arParams['SLIDER_HEIGHT']) > 0):?> style="height: <?php echo $arParams['SLIDER_HEIGHT'] ?>px;"<?endif?>>
					<?if($arParams['SLIDER_FULL_SCREEN'] == 'Y'):?>
						<div class="container">
					<?endif?>
							<div class="row">
								<div class="col-xs-12">
									<div class="item-container clearfix">
										<?if(!empty($arItem['PREVIEW_PICTURE'])):?>
											<div class="item___image <?php echo ($arItem['PROPERTIES']['POSITION_TEXT']['VALUE_XML_ID'] == 'RIGHT')? 'left' : 'right' ?>" style="background-image:url(<?php echo $arItem['PREVIEW_PICTURE']['SRC'] ?>)"></div>
										<?endif?>

										<div class="owlslider-table h-base<?if($arItem['PROPERTIES']['POSITION_TEXT']['VALUE_XML_ID'] == 'RIGHT'):?> col-md-6 pull-right col-xs-offset-3 col-sm-offset-4<?elseif($arItem['PROPERTIES']['POSITION_TEXT']['VALUE_XML_ID'] == 'LEFT'):?> col-md-6 col-xs-9<?endif?>">
											<div class="owlslider-table-tr">
												<div class="owlslider-table-td panel-body"<?php if(intval($arParams['SLIDER_HEIGHT']) > 0):?> style="height: <?php echo $arParams['SLIDER_HEIGHT'] ?>px;"<?endif?>>
													<div class="h1" <?//=($arItem['PROPERTIES']['STYLE_H']['VALUE'])? ' style="'.$arItem['PROPERTIES']['STYLE_H']['VALUE'].'"' : ''?>><?//php echo $arItem['NAME'] ?></div>
													<?if(!empty($arItem['PREVIEW_TEXT'])):?>
														<div class="owlslider-table-text hidden-xs"><?php echo $arItem['PREVIEW_TEXT'] ?></div>
													<?endif?>
													<?if(!empty($arItem['DETAIL_TEXT'])):?>
														<div class="owlslider-table-text hidden-sm hidden-md hidden-lg"><?php echo $arItem['DETAIL_TEXT'] ?></div>
													<?endif?>
													<?if(!empty($arItem['PROPERTIES']['TEXT_BUTTON']['VALUE'])):?>
														<div><a class="btn btn-<?=($arItem['PROPERTIES']['SIZE_BUTTON']['VALUE_XML_ID']) ? $arItem['PROPERTIES']['SIZE_BUTTON']['VALUE_XML_ID'] : ''?> <?=(!empty($arItem['PROPERTIES']['CLASS_BUTTON']['VALUE']))? $arItem['PROPERTIES']['CLASS_BUTTON']['VALUE'] : 'btn-primary' ?>" href="<?php echo SITE_DIR.$arItem['PROPERTIES']['LINK']['VALUE'] ?>" <?if($arItem['PROPERTIES']['TARGET']['VALUE'] == 'Y'): ?>target="_blank"<?endif?>><?php echo $arItem['PROPERTIES']['TEXT_BUTTON']['VALUE'] ?></a></div>
													<?endif?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?if($arParams['SLIDER_FULL_SCREEN'] == 'Y'):?>
								</div>
							<?endif?>
					<div class="item-image" style="background-image:url(<?php echo $arItem['DETAIL_PICTURE']['SRC'] ?>)"></div>
					<?if(empty($arItem['PROPERTIES']['TEXT_BUTTON']['VALUE'])):?>
						<a class="item-image-link" href="<?php echo SITE_DIR.$arItem['PROPERTIES']['LINK']['VALUE'] ?>" <?if($arItem['PROPERTIES']['TARGET']['VALUE'] == 'Y'): ?>target="_blank"<?endif?>></a>
					<?endif?>
				</li>
			<?endforeach?>
		</ul>
</div>
<?}?>