<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$frame = $this->createFrame()->begin();
$frame->setBrowserStorage(true); ?>

<ul class="nav nav-pills">

	<?if($arResult["FORM_TYPE"] != "login")
	{ ?>
		<li class="welcom_user dropdown" data-hover="dropdown">
			<a class="account dropdown-toggle" data-toggle="dropdown" role="button" href="<?php echo $arParams['PROFILE_URL'] ?>">
				<span class="user-personal"><?php echo Loc::getMessage('personal') ?></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>orders/"><?php echo Loc::getMessage('PROFILE_ORDER'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>account/"><?php echo Loc::getMessage('PROFILE_MY_PAY'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>private/"><?php echo Loc::getMessage('PROFILE'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>orders/?filter_history=Y"><?php echo Loc::getMessage('PROFILE_ORDER_HISTORY'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>profiles/"><?php echo Loc::getMessage('PROFILE_DELIVERY'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>cart/"><?php echo Loc::getMessage('PROFILE_CART'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>subscribe/"><?php echo Loc::getMessage('PROFILE_SUBSCRIBE'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>contacts/"><?php echo Loc::getMessage('PROFILE_CONTACT'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>favorites/"><?php echo Loc::getMessage('PROFILE_FAVORITES'); ?></a></li>
			</ul>
		</li>
		<?php
	} else { ?>
		<li>
			<a class="user-in" href="<?php echo $arParams['REGISTER_URL'] ?>">
				<span class="user-personal"><?php echo Loc::getMessage('AUTH_LOGIN_BUTTON'); ?> </span></a>
			</li>
			<?php } 

				if($arResult["FORM_TYPE"] != "login") { ?>
					<li>
						<a class="user-out" href="?logout=yes">
							<span class="user-out"><?php echo Loc::getMessage('AUTH_LOGOUT_BUTTON') ?></span>
						</a>
					</li>
					<?php } ?>

				</ul>

				<? $frame->beginStub();
				?>
				<ul class="nav nav-pills">
					<li>
						<div class="loader-auth text"><img src="<?php echo SITE_TEMPLATE_PATH?>/images/loader/ajax-loader2.gif" /></div>
					</li>
				</ul>
				<?php

				$frame->End(); ?>