<?php 
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

interface sorterInterface
{
    public function sorterUrl();

    public function sorterUrlSelected();

    public static function getType();

    public static function getSort();
}