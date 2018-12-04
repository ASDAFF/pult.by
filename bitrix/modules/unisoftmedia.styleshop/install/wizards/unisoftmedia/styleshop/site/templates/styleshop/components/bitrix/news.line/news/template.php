<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!empty($arResult['ITEMS']))
	{
		$all_link = str_replace($arResult['ITEMS'][0]['CODE'], '', $arResult['ITEMS'][0]['DETAIL_PAGE_URL']);
		$all_link = str_replace('//', '/', $all_link );
		?>
		<div class="post-list">
			<h2 class="product-title"><?if($arParams['MESS_TITLE_LINK'] == 'Y'):?><a href="<?php echo $all_link ?>"><?endif?><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?><?if($arParams['MESS_TITLE_LINK'] == 'Y'):?></a><?endif?></h2>
			<div class="post-list-container row">
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

				$obParser = new CTextParser;
				?>
				<ul class="list-unstyled <?if($arParams['USE_CAROUSEL'] == 'Y'):?>owl-carousel Owlcarousel<?endif?>" <?if($arParams['USE_CAROUSEL'] == 'Y'):?>data-options="<?php echo CUtil::PhpToJSObject($sliderOptions, false, true); ?>"<?endif?>>
					<?php foreach($arResult['ITEMS'] as $arItem):

					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<li class="post-list-item row<?if($arParams['USE_CAROUSEL'] != 'Y'):?> col-xs-<?php echo $arParams['RESPONSIVE_ITEMS_XS'] ?> col-sm-<?php echo $arParams['RESPONSIVE_ITEMS_SM'] ?> col-md-<?php echo $arParams['RESPONSIVE_ITEMS_MD'] ?> col-lg-<?php echo $arParams['RESPONSIVE_ITEMS_LG'] ?><?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<?php
						if(is_array($arItem['PREVIEW_PICTURE']) && !empty($arItem['PREVIEW_PICTURE'])):
							$arPic = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"],
								array(
									"width" => $arParams['MAX_WIDTH'],
									"height" => $arParams['MAX_HEIGHT']
								),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);
						?>
						<figure class="post-list-1 <?=($arParams['MODE_VIEW'] == 'TOP')? 'col-xs-12' : 'col-lg-6 col-md-6 col-sm-12 col-xs-12' ?>">
							<a href="<?php echo $arItem["DETAIL_PAGE_URL"]?>"><img src="<?php echo $arPic['src'] ?>" alt="<?php echo $arItem['PREVIEW_PICTURE']['ALT'] ?>" /></a>
						</figure>
					<?php endif ?>
					<div class="post-list-2 <?php if(is_array($arItem['PREVIEW_PICTURE']) && !empty($arItem['PREVIEW_PICTURE']) && $arParams['MODE_VIEW'] != 'TOP'): ?>col-lg-6 col-md-6 col-sm-12 col-xs-12<?php else: ?>col-xs-12<?php endif;?>">
						<?if(isset($arItem['DATE_ACTIVE_FROM'])):?>
							<small class="post-list-active-date"><?php echo $arItem['DISPLAY_ACTIVE_FROM'] ?></small>
						<?endif?>
						<h4 class="post-list-name"><a href="<?php echo $arItem["DETAIL_PAGE_URL"]?>"><?php echo htmlspecialcharsbx($arItem["NAME"])?></a></h4>
						<?if(!empty($arItem['PREVIEW_TEXT'])):?>
						<p>
							<?if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
								$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);?>
							<?php echo $arItem['PREVIEW_TEXT'] ?>
						</p>
						<?endif?>
						<?if($arParams['HIDDEN_BUTTON_MORE'] != 'Y'):?>
							<div class="post-list-view_more"><a class="btn btn-primary btn-xs" href="<?php echo $arItem["DETAIL_PAGE_URL"]?>"><?php echo Loc::getMessage('VIEW_MORE') ?></a></div>
						<?endif?>
					</div>
				</li>
			<?php endforeach ?>
		</ul>
	</div>
	<?if($arParams['ALL_LINK'] == 'Y'):?>
		<div class="clearfix"><a class="pull-right" href="<?php echo $all_link ?>"><?php echo htmlspecialcharsbx($arParams['MESS_ALL_LINK']) ?></a></div>
	<?endif?>
</div>
<?php } ?>