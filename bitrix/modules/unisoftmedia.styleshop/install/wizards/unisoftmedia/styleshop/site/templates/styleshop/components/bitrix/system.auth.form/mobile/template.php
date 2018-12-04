<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$frame = $this->createFrame()->begin();
$frame->setBrowserStorage(true); ?>

<ul class="nav nav-pills">

	<?if($arResult["FORM_TYPE"] != "login")
	{ ?>
		<li class="welcom_user dropdown">
			<a class="account dropdown-toggle" data-toggle="dropdown" role="button" href="<?php echo $arParams['PROFILE_URL'] ?>">
				<span class="user-personal"><?php echo Loc::getMessage('personal') ?></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>profile/"><?php echo Loc::getMessage('PROFILE'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>cart/"><?php echo Loc::getMessage('PROFILE_CART'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>order/"><?php echo Loc::getMessage('PROFILE_ORDER'); ?></a></li>
				<li><a href="<?php echo $arParams['PROFILE_URL'] ?>favorites/"><?php echo Loc::getMessage('PROFILE_FAVORITES'); ?></a></li>
				<li><a href="?logout=yes"><?php echo Loc::getMessage('AUTH_LOGOUT_BUTTON') ?></a></li>
			</ul>
		</li>
<?} else {?>
		<li class="welcom_user">
			<a class="account dropdown-toggle" role="button" href="<?php echo $arParams['PROFILE_URL'] ?>">
				<span class="user-personal"><?php echo Loc::getMessage('personal') ?></span>
			</a>
		</li>
	<?}?>

	</ul>

	<? $frame->beginStub();
	?>
	<ul class="nav nav-pills">
		<li class="welcom_user">
			<a class="account dropdown-toggle" role="button" href="<?php echo $arParams['PROFILE_URL'] ?>">
				<span class="user-personal"><?php echo Loc::getMessage('personal') ?></span>
			</a>
		</li>
	</ul>
	<?php

	$frame->End(); ?>