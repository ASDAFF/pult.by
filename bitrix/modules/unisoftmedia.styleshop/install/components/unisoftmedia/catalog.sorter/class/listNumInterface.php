<?php 
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

interface listNumInterface
{
    public function listNumUrl();

    public function listNumUrlSelected();
    
    public static function getListNum();
}