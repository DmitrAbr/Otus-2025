<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Компонент списка таблицы базы данных");

use Bitrix\Main\Type;

// use Models\Lists\CarsPropertyValuesTable as CarsTable;
// use Models\HospitalClientsTable as Clients;

// use Models\BookTable as Books;
// use Models\PublisherTable as Publishers;
// use Models\AuthorTable as Authors;
// use Models\WikiprofileTable as Wikiprofiles;

use Models\ClientsTable as Clients; 

/*// получаем список клиентов
$collection = Clients::getList([
    'select' => [
        'ID',
        'UF_NAME',
        'UF_LASTNAME',
        'UF_PHONE',
        'UF_JOBPOSITION',
        'UF_SCORE'
    ]
])->fetchCollection();
    
foreach ($collection as $key => $item) {
    echo $item->getUfName().' '.$item->getUfLastname().' '.$item->getUfPhone().' '.$item->getUfJobposition().' '.$item->getUfScore().'<br />';
}
*/
/*// получаем список клиентов в виде массива
$limit = 1;
$page = 2;
$offset = $limit * ($page-1);
$data = Clients::getList([
    'select' => [
        'ID',
        'UF_NAME',
        'UF_LASTNAME',
        'UF_PHONE',
        'UF_JOBPOSITION',
        'UF_SCORE'
    ],
    'order' => [
        'ID' => 'ASC'
    ],
    'limit' => $limit,
    'offset' =>$offset

])->fetchAll();
    
foreach ($data as $key => $item) {
    pr($item);
}*/
?>

<?
$APPLICATION->IncludeComponent(
	"otus:table.views", 
	"list", 
	array(
		"COMPONENT_TEMPLATE" => "list",
		"SHOW_CHECKBOXES" => "Y",
		"NUM_PAGE" => "1"
	),
	false
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>