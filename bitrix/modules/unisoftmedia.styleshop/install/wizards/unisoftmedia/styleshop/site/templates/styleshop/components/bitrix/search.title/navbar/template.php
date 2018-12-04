<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true); ?>

<form class="navbar-form" action="<?php echo $arResult["FORM_ACTION"] ?>">
	<div class="hsearch-container">
		<?php 
		$INPUT_ID = trim($arParams["~INPUT_ID"]);
		if(strlen($INPUT_ID) <= 0)
			$INPUT_ID = "title-search-input";
		$INPUT_ID = CUtil::JSEscape($INPUT_ID);

		$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
		if(strlen($CONTAINER_ID) <= 0)
			$CONTAINER_ID = "title-search";
		$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

		if($arParams["SHOW_INPUT"] !== "N")
			{ ?>
				<div id="<?php echo $CONTAINER_ID ?>" class="bx_search_container">

					<div class="form-group">
						<div class="input-group form-search">
							<input id="<?php echo $INPUT_ID ?>" class="form-control" type="text" name="q" placeholder="<?php echo Loc::getMessage('CT_BST_SEARCH')?>" value="" size="23" maxlength="50" autocomplete="off"/>
							<div class="input-group-btn">
								<button class="btn btn-default" name="s" type="submit"><span><?php echo Loc::getMessage('CT_BST_SEARCH_BUTTON')?></span></button>
							</div>
						</div>
					</div>

				</div>
				<?php } ?>

			<script>
				BX.ready(function(){
					new JCTitleSearch({
						'AJAX_PAGE' : '<?php echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
						'CONTAINER_ID': '<?php echo $CONTAINER_ID?>',
						'INPUT_ID': '<?php echo $INPUT_ID?>',
						'MIN_QUERY_LEN': 2
					});
				});
			</script>
		</div>
</form>