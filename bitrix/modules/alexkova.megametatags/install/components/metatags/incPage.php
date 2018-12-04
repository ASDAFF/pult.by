<div id="kuznica_megameta" class="bx-component-debug bx-debug-summary">
	<?if ($arResult['CURRENT_TAGS']):?>
		<b style="color: blue;"><?=GetMessage('CURRENT_METATAGS')?>:</b><br/>
		<?foreach($arResult['CURRENT_TAGS'] as $key => $val):?>
			<b><?=toLower($key)?>:</b> <span><?=$val?></span><br/>
		<?endforeach;?>
		<hr />
	<?endif;?>

	<?if($arResult['TAGS']):?>		
		<b style="color: green;"><?=GetMessage('CURRENT_METATAGS_KUZNICA')?>:</b><br/>
		<?foreach($arResult['TAGS'] as $key => $val):?>
			<b><?=toLower($key)?>:</b> <span><?=$val['VALUE']?></span><br/>
			<?if ($arResult['SHOW_DEBUG']):?>
				<span class="li-item-2"><i><b><?=GetMessage('MASK')?></b></i>:<?=$val['MASK']?></span><br/>
			<?endif;?>
		<?endforeach;?>
		<br /><i><?=GetMessage('ELEMENT_LINK')?>:</i>
		<a target="_blank" href="<?=$arResult['ELEMENT']['LINK']?>"><?=$arResult['ELEMENT']['NAME']?></a>
		<hr />
	<?endif;?>

	<?if($arResult['WHERE_SET'] && $arResult['SHOW_DEBUG']):?>		
		<b style="color: green;"><?=GetMessage('TAGS_KEYS')?>:</b><br/>
		<?foreach($arResult['WHERE_SET'] as $key => $val):?>
			<b>#<?=($key)?>#:</b> 
			<br/><span class="li-item"><b><?=GetMessage('KEYS_VAL')?></b>: <?=$arResult['KEYS'][$key]?></span>
			<?if ($arResult['IN_CACHE'][$key]):?>
				<br/><span class="li-item"><b><?=GetMessage('IN_CACHE')?>.</b>
			<?endif?>	
			<?if (!$val){continue;}?>

			<?if (is_string($val)):?>
				<br/><span class="li-item"><b><?=GetMessage('SET_INSTALL')?>:</b> <?=$val?></span>
			<?else:?>
				<br/><span class="li-item"><b><?=GetMessage('SET_INSTALL')?></b>:</span>
				<?foreach($val as $keyWhere => $valWhere):?>
					<br/><span class="li-item-2"><b><?=$keyWhere?>:</b> <?=$valWhere?></span>
				<?endforeach;?>
			<?endif;?>
			<br/><br/>
		<?endforeach;?>
	<?endif;?>
</div>