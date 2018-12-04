<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!empty($arResult['ITEMS'])): ?>

<div class="testimonials-list">
	<h2 class="testimonials-title"><?php echo $arParams['MESS_TITLE'] ?></h2>
	<?php 
	$sliderOptions = array();
	$sliderOptions['nav'] =  ($arParams['SLIDER_NAV'] == 'Y');
	$sliderOptions['smartSpeed'] = $arParams['SLIDER_SPEED'];
	$sliderOptions['autoplay'] = ($arParams['SLIDER_AUTOPLAY'] == 'Y');
	$sliderOptions['autoplayTimeout'] = $arParams['SLIDER_SHOW_SPEED'];
	$sliderOptions['autoplayHoverPause'] = ($arParams['SLIDER_AUTOPLAY_HOVER_PAUSE'] == 'Y');
	if($arParams['MOUSE_DRAG'] == 'N')
		$sliderOptions['mouseDrag'] = false;
	$sliderOptions['loop'] = ($arParams['SLIDER_LOOP'] == 'Y');
	$sliderOptions['dots'] = false;
	
	$sliderOptions['responsive'] = [
		'0' => [
			'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_MOBILE']
		],
		'480' => [
			'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_XS']
		],
		'700' => [
			'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_SM']
		],
		'992' => [
			'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_MD']
		],
		'1200' => [
			'items' => $arParams['RESPONSIVE_CAROUSEL_ITEMS_LG']
		]
	];
	?>
	<div class="testimonials-container">
		<ul class="list-unstyled <?if($arParams['USE_CAROUSEL'] == 'Y'):?>owl-carousel Owlcarousel<?endif?>" <?if($arParams['USE_CAROUSEL'] == 'Y'):?>data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>"<?endif?>>
			<?php
			foreach($arResult['ITEMS'] as $arItem):

				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<li class="testimonials-item<?if($arParams['USE_CAROUSEL'] != 'Y'):?> col-xs-<?php echo $arParams['RESPONSIVE_ITEMS_XS'] ?> col-sm-<?php echo $arParams['RESPONSIVE_ITEMS_SM'] ?> col-md-<?php echo $arParams['RESPONSIVE_ITEMS_MD'] ?> col-lg-<?php echo $arParams['RESPONSIVE_ITEMS_LG'] ?><?endif?>" id="<?php echo $this->GetEditAreaId($arItem['ID']);?>">
				<div class="testimonials-item-container">
					<?php if($arParams['DISPLAY_PICTURE'] != 'N' && is_array($arItem['PREVIEW_PICTURE'])):?>
						<?php if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])):?>
							<a href="<?php echo $arItem['DETAIL_PAGE_URL']?>">
								<img class="preview_picture"
									src="<?php echo $arItem['PREVIEW_PICTURE']['SRC']?>"
									width="<?php echo $arItem['PREVIEW_PICTURE']['WIDTH']?>"
									height="<?php echo $arItem['PREVIEW_PICTURE']['HEIGHT']?>"
									alt="<?php echo $arItem['PREVIEW_PICTURE']['ALT']?>"
									title="<?php echo $arItem["PREVIEW_PICTURE"]['TITLE']?>"
									/>
								</a>
							<?php else: ?>
								<img class="preview_picture"
								src="<?php echo $arItem['PREVIEW_PICTURE']['SRC']?>"
								width="<?php echo $arItem['PREVIEW_PICTURE']['WIDTH']?>"
								height="<?php echo $arItem['PREVIEW_PICTURE']['HEIGHT']?>"
								alt="<?php echo $arItem['PREVIEW_PICTURE']['ALT']?>"
								title="<?php echo $arItem['PREVIEW_PICTURE']['TITLE']?>"
								/>
							<?php endif ?>
						<?php endif ?>

						<?php if($arParams['DISPLAY_PREVIEW_TEXT'] != 'N' && $arItem['PREVIEW_TEXT']): ?>
							<p class="testimonials-item-text"><?php echo $arItem['PREVIEW_TEXT']; ?></p>
						<?php endif;?>

						<?php if($arParams['DISPLAY_NAME'] != 'N' && $arItem['NAME']):?>
							<?php if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])):?>
								<p class="testimonials-item-name"><a href="<?php echo $arItem['DETAIL_PAGE_URL']?>"><?php echo $arItem['NAME']?></a></p>
							<?php else:?>
								<p class="testimonials-item-name"><?php echo $arItem['NAME']?></p>
							<?php endif;?>
						<?php endif;?>
					</div>
					<div class="testimonials-background"></div>
				</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
<?php endif ?>