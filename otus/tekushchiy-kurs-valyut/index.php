<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent(
	"otus:currencies.view", 
	".default", 
	[
		"CURRENCY" => [
			0 => "RUB",
		],
		"SHOW_ALL" => "N",
		"COMPONENT_TEMPLATE" => ".default"
	],
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>