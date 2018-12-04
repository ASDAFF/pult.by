<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);?>
<div class="sorter">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?if('Y' === $arParams['SORTERED_SHOW'] && is_array($arResult['SORTER']) && !empty($arResult['SORTER'])):?>
				<label class="hidden-xs hidden-sm"><?php echo Loc::getMessage('SORTER')?></label>
				<div class="btn-group sort">
					<button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown">
						<span class="sorter-option hidden-xs hidden-sm"><span class="text"><?php echo $arResult['SORTER_SELECTED']['NAME'] ?></span></span>
						<span class="hidden-md hidden-lg"><span class="text"><?php echo Loc::getMessage('SORTER')?></span></span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<?foreach($arResult['SORTER'] as $arSort):?>
							<li <?if($arSort['ASC']['SELECTED'] || $arSort['DESC']['SELECTED']):?>class="active"<?endif?>>
								<a data-asc-url="<?php echo $arSort['ASC']['URL']?>" data-desc-url="<?php echo $arSort['DESC']['URL']?>" href="<?php echo $arSort['ASC']['URL']?>"<?if($arParams['IS_AJAX'] == 'Y'):?> data-ajax="true"<?endif?>><span class="text"><?php echo $arSort['ASC']['NAME'] ?></span></a>
							</li>
						<?endforeach?>
					</ul>
					<a class="sortbutton<?php echo ($arResult['SORTER_SELECTED']['TYPE'] == 'asc')? ' up' : ' down' ?>" href="<?php echo $arResult['SORTER_SELECTED']['URL2'] ?>" <?if($arParams['IS_AJAX'] == 'Y'):?> data-ajax="true"<?endif?>></a>
				</div>
			<?endif?>
			<?if('Y' === $arParams['OUTPUT_LIST_NUM_SHOW'] && is_array($arResult['OUTPUT_LIST_NUM']) && !empty($arResult['OUTPUT_LIST_NUM'])):?>
				<label class="hidden-xs hidden-sm"><?php echo Loc::getMessage('LIST_NUM')?></label>
				<div class="btn-group">
					<button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown">
						<span class="sorter-option hidden-xs hidden-sm"><span class="text"><?php echo $arResult['OUTPUT_LIST_NUM_SELECTED']['NAME']?></span></span>
						<span class="hidden-md hidden-lg"><span class="text"><?php echo Loc::getMessage('LIST_NUM')?></span></span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<?foreach($arResult['OUTPUT_LIST_NUM'] as $arSort):?>
							<?if($arSort['VALUE'] > 1000):?>
								<li role="separator" class="divider"></li>
							<?endif?>
							<li <?if($arSort['SELECTED']):?>class="active"<?endif?>>
								<a href="<?php echo $arSort['URL']?>" <?if($arParams['IS_AJAX'] == 'Y'):?> data-ajax="true"<?endif?>><span class="text"><?php echo $arSort['NAME'] ?></span></a>
							</li>
						<?endforeach?>
					</ul>
				</div>
			<?endif?>
		<?if('Y' == $arParams['USE_VIEW'] && is_array($arResult['VIEW']) && !empty($arResult['VIEW'])):?>
				<div class="pull-right view">
					<label class="hidden-xs hidden-sm"><?php echo Loc::getMessage('VIEW')?></label>
					<?foreach($arResult['VIEW'] as $k => $arView):?>
						<a href="<?php echo $arView['URL'] ?>" role="button" class="btn btn-sm btn-default view-<?php echo $k+1 ?> s<?php echo $arView['NAME'] ?><?if($arView['SELECTED']):?> active<?endif?>" title="<?php echo Loc::getMessage(strtoupper($arView['NAME']))?>" <?if($arParams['IS_AJAX'] == 'Y'):?> data-ajax="true"<?endif?>></a>
					<?endforeach?>
				</div>
		<?endif?>
		</div>
	</div>
</div>