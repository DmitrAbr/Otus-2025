<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<?$APPLICATION->IncludeComponent(
	"otus:table.view", 
	".default", 
	[
		"SHOW_CHECKBOXES" => "Y",
		"NUM_PAGE" => "2",
		"COMPONENT_TEMPLATE" => ".default"
	],
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>