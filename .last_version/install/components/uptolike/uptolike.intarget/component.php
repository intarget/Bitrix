<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$module_id = "uptolike.intarget";
global $CACHE_MANAGER, $APPLICATION;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 0;

if($this->StartResultCache(false, $arResult))
{
	$arResult["intarget_code"] = COption::GetOptionString($module_id, "intarget_code");
		
	$this->IncludeComponentTemplate();
}