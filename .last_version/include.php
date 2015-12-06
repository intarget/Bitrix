<?
CModule::IncludeModule("uptolike.intarget");
global $DBType;

CModule::AddAutoloadClasses(
	"uptolike.intarget",
	array(
		"CUptolikeIntarget" => "classes/general/intarget.php",
	)
);