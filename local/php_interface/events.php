<?php

/**
 * This file contains a full list of custom event handlers
 * Code the handlers need NOT be contained in this file. 
 * It needs to be made relevant to the PSR-[0-4] structure, classes
 */

$eventManager = \Bitrix\Main\EventManager::getInstance();


//Вешаем обработчик на событие создания списка пользовательских свойств OnUserTypeBuildList
$eventManager->addEventHandler('iblock', 'OnIblockPropertyBuildList', ['Sibcem\Userfield\IBLink', 'GetUserTypeDescription']);
$eventManager->addEventHandler('iblock', 'OnIblockPropertyBuildList', ['Otus\Userfield\CUserTypeRecord', 'GetUserTypeDescription']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', ['Otus\Handlers\CrmIblockUpdateHandler', 'onUpdateIblockHandler']);
$eventManager->addEventHandler("crm", "OnAfterCrmDealUpdate", ['Otus\Handlers\CrmIblockUpdateHandler', 'onUpdateCrmHandler']);