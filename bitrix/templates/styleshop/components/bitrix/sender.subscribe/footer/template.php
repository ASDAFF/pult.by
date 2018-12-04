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

$buttonId = $this->randString();
?>
<div class="bx-subscribe-footer"  id="sender-subscribe">
<?
$frame = $this->createFrame("sender-subscribe", false)->begin();
?>
	<script>
		BX.ready(function()
		{
			BX.bind(BX("bx_subscribe_btn_<?=$buttonId?>"), 'click', function() {
				setTimeout(mailSender, 250);
				return false;
			});
		});

		function mailSender()
		{
			setTimeout(function() {
				var btn = BX("bx_subscribe_btn_<?=$buttonId?>");
				if(btn)
				{
					var btn_span = btn.querySelector("span");
					var btn_subscribe_width = btn_span.style.width;
					BX.addClass(btn, "send");
					btn_span.outterHTML = "<span><i class='fa fa-check'></i> <?=Loc::getmessage("subscr_form_button_sent")?></span>";
					if(btn_subscribe_width)
						btn.querySelector("span").style["min-width"] = btn_subscribe_width+"px";
				}
			}, 400);
		}
	</script>

	<form class="form-horizontal" method="post" action="<?=$arResult["FORM_ACTION"]?>"  onsubmit="BX('bx_subscribe_btn_<?=$buttonId?>').disabled=true;">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="sender_subscription" value="add">

		<div class="form-group">
			<label class="col-sm-5 control-label headline h4"><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></label>
			<div class="input-group col-sm-7">
				<input class="form-control" type="email" name="SENDER_SUBSCRIBE_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=Loc::getmessage("subscr_form_email_title")?>" placeholder="<?=htmlspecialcharsbx(Loc::getmessage('subscr_form_email_title'))?>">
				<div class="input-group-btn">
					<button class="btn btn-default" id="bx_subscribe_btn_<?=$buttonId?>">
						<span><?=Loc::getmessage("footer_subscr_form_button")?></span>
					</button>
				</div>
			</div>
			<?if(isset($arResult['MESSAGE'])):?>
					<div id="sender-subscribe-response-cont">
						<div class="col-sm-5"></div>
						<div class="col-sm-7">
							<p class="<?php echo ($arResult['MESSAGE']['TYPE']=='ERROR')? 'text-danger' : 'text-success' ?>"><?=htmlspecialcharsbx($arResult['MESSAGE']['TEXT'])?></p>
						</div>
					</div>
				<?endif;?>
		</div>
		<?if(count($arResult["RUBRICS"])>0):?>
			<div class="bx-subscribe-desc"><?=Loc::getmessage("subscr_form_title_desc")?></div>
		<?endif;?>
		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<div class="bx_subscribe_checkbox_container">
			<input type="checkbox" name="SENDER_SUBSCRIBE_RUB_ID[]" id="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?>>
			<label for="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>"><?=htmlspecialcharsbx($itemValue["NAME"])?></label>
		</div>
		<?endforeach;?>
	</form>
<?
$frame->beginStub();
?>
	<?if(isset($arResult['MESSAGE'])): CJSCore::Init(array("popup"));?>
		<div id="sender-subscribe-response-cont" style="display: none;">
			<div class="bx_subscribe_response_container">
				<table>
					<tr>
						<td style="padding-right: 40px; padding-bottom: 0px;"><img src="<?=($this->GetFolder().'/images/'.($arResult['MESSAGE']['TYPE']=='ERROR' ? 'icon-alert.png' : 'icon-ok.png'))?>" alt=""></td>
						<td>
							<div style="font-size: 22px;"><?=Loc::getmessage('subscr_form_response_'.$arResult['MESSAGE']['TYPE'])?></div>
							<div style="font-size: 16px;"><?=htmlspecialcharsbx($arResult['MESSAGE']['TEXT'])?></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?endif;?>

	<script>
		BX.ready(function()
		{
			BX.bind(BX("bx_subscribe_btn_<?=$buttonId?>"), 'click', function() {
				setTimeout(mailSender, 250);
				return false;
			});
		});

		function mailSender()
		{
			setTimeout(function() {
				var btn = BX("bx_subscribe_btn_<?=$buttonId?>");
				if(btn)
				{
					var btn_span = btn.querySelector("span");
					var btn_subscribe_width = btn_span.style.width;
					BX.addClass(btn, "send");
					btn_span.outterHTML = "<span><i class='fa fa-check'></i> <?=Loc::getmessage("subscr_form_button_sent")?></span>";
					if(btn_subscribe_width)
						btn.querySelector("span").style["min-width"] = btn_subscribe_width+"px";
				}
			}, 400);
		}
	</script>

	<form class="form-horizontal" method="post" action="<?=$arResult["FORM_ACTION"]?>"  onsubmit="BX('bx_subscribe_btn_<?=$buttonId?>').disabled=true;">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="sender_subscription" value="add">

		<div class="form-group">
			<label class="col-sm-5 control-label headline h4"><?php echo htmlspecialcharsbx($arParams['MESS_TITLE']) ?></label>
			<div class="input-group col-sm-7">
				<input class="form-control" type="email" name="SENDER_SUBSCRIBE_EMAIL" value="" title="<?=Loc::getmessage("subscr_form_email_title")?>" placeholder="<?=htmlspecialcharsbx(Loc::getmessage('subscr_form_email_title'))?>" />
				<div class="input-group-btn">
					<button class="btn btn-default" id="bx_subscribe_btn_<?=$buttonId?>">
						<span><?=Loc::getmessage("footer_subscr_form_button")?></span>
					</button>
				</div>
			</div>
		</div>
		<?if(count($arResult["RUBRICS"])>0):?>
			<div class="bx-subscribe-desc"><?=Loc::getmessage("subscr_form_title_desc")?></div>
		<?endif;?>
		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
			<div class="bx_subscribe_checkbox_container">
				<input type="checkbox" name="SENDER_SUBSCRIBE_RUB_ID[]" id="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>" value="<?=$itemValue["ID"]?>">
				<label for="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>"><?=htmlspecialcharsbx($itemValue["NAME"])?></label>
			</div>
		<?endforeach;?>
	</form>
<?
$frame->end();
?>
</div>