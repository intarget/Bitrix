<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();	
$arComponentParameters = array(
	"GROUPS" => array(
		/*"DATA_SOURCE" => array(
			"NAME" => Loc::getMessage('UPTOLIKE_SHARE_GROUP_DATA_SOURCE'),
		),*/
	),
	"PARAMETERS" => array(
		/*"PAGE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("UPTOLIKE_SHARE_PARAMETER"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $id_ar,
		),*/
		"CACHE_TIME" => array("DEFAULT" => "36000000"),
	),
); 
?>