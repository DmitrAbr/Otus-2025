<?

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



if(!Loader::includeModule('currency'))
{
	return;
}

$currencyList = CurrencyTable::getList([
    'select' => ['CURRENCY'],
    'order'  => ['SORT' => 'ASC']
]);

$listCurrency == [];
while ($currency = $currencyList->fetch()) {
	$listCurrency[$currency['CURRENCY']]=$currency['CURRENCY'];
}

$arComponentParameters = Array(
	"PARAMETERS" => array(
		"CURRENCY" => array(
			"NAME" => GetMessage("NAME_GRUOPS_PARAMS"),
			"TYPE" => "LIST",
			"VALUES" => $listCurrency,
			"DEFAULT" => "N",
			"MULTIPLE" => "Y",
			
		),
		"SHOW_ALL" => array(
			"NAME" => GetMessage("NAME_SHOW_ALL_PARAMS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
	)
);