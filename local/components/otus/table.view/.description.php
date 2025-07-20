<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

$arComponentDescription = array(
	"NAME" => GetMessage("NAME_DESCRIPTION_SECTION"),
	"DESCRIPTION" => "TEST TEST TEST",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "otus",
		"CHILD" => array(
			"ID" => "table",
			"NAME" => "ddddd",
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "views"	
			)
		)
	)
);