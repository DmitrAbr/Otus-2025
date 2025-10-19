<?php

define('NOT_CHECK_PERMISSIONS', true); // отключает проверку прав на файлы

use Bitrix\Crm\ActivityTable;
use CCrmOwnerType;
use Bitrix\Crm\ContactTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;

if($_REQUEST['event'] === 'ONCRMACTIVITYADD' && !empty($_REQUEST['data']))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	
	Loader::includeModule("crm");
	Loader::includeModule("main");
	
	$arFields = $_REQUEST['data']['FIELDS'];
	
	$objActivity = ActivityTable::getByID($arFields["ID"])->fetchObject();
	
	if($objActivity->getOwnerTypeId() !== CCrmOwnerType::Contact)
	{
		return;
	}
	
	$contactId = $objActivity->getOwnerId();
	
	$result = ContactTable::update($contactId, ['UF_LAST_COMMUNICATION_TIME' => new DateTime()]);
	if(!$result->isSuccess())
	{
		Debug::writeToFile($result->getErrorMessages(), 'Debug Result', "crm_activity/error.log");
	}
}