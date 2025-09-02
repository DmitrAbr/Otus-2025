<?php

namespace Otus\Crmcustomtab\Crm;

use Otus\Crmcustomtab\Orm\BookModuleTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;

Loc::loadMessages(__FILE__);


class Handlers
{
	static function updateTabs(Event $event): EventResult
	{
		$entityTypeId = $event->getParameter('entityTypeID');
		$entityId = $event->getParameter('entityID');
		$tabs = $event->getParameter('tabs');
		
		$tabs[]=[
			'id' => 'book_tab_' . $entityTypeId . '_' . $entityId,
			'name' => Loc::getMessage('OTUS_CRMCUSTOMTAB_TAB_TITLE'),
			'enabled' => true,
			'loader' => [
				'serviceUrl' => sprintf(
					'/bitrix/components/otus.crmcustomtab/table.viewmodule/lazyload.ajax.php?site=%s&%s',
					\SITE_ID,
					\bitrix_sessid_get(),
				),	
				'componentData' => [
					'template' => '',
					'params' => [
						'ORM' => BookModuleTable::class,
						'DEAL_ID' => $entityId,
					]
				]
			],
		];
		
		return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs]);
	}
}