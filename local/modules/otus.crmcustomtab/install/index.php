<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\SystemException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;
use Otus\Crmcustomtab\Orm\AuthorModuleTable;
use Otus\Crmcustomtab\Orm\BookModuleTable;

Loc::getMessage(__FILE__);

class otus_crmcustomtab extends CModule
{
	public $MODULE_ID = 'otus.crmcustomtab';
	public $MODULE_SORT = 500;
	public $MODULE_DESCRIPTION;
	public $MODULE_VERSION_DATE;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	
	
	public function __construct()
	{
		$arModuleVersion = [];
		include __DIR__.'/version.php';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_DESCRIPTION = Loc::getMessage("OTUS_CRMCUSTOMTAB_INSTALL_DESCRIPTION");
		$this->MODULE_NAME = Loc::getMessage("OTUS_CRMCUSTOMTAB_INSTALL_NAME");
		$this->PARTNER_NAME = Loc::getMessage("OTUS_CRMCUSTOMTAB_PARTHER_NAME");
		$this->PARTNER_URI = Loc::getMessage("OTUS_CRMCUSTOMTAB_PARTHER_URI");
	}
	
	public function DoInstall()
	{
		if($this->isVersionD7())
		{
			ModuleManager::registerModule($this->MODULE_ID);
			$this->InstallFiles();
			$this->InstallDB();
			$this->InstallEvents();
		}
		else {
			throw new SystemException(Loc::getMessage("OTUS_CRMCUSTOMTAB_INSTALL_ERROR_VERSION"));
		}
	}
	
	public function DoUninstall()
	{
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$this->UnInstallEvents();
		
		ModuleManager::unRegisterModule($this->MODULE_ID);
	}
	
	public function InstallFiles($params = [])
	{
		$component_path = $this->getPath(). '/install/components';
		
		if(Directory::isDirectoryExists($component_path))
		{
			CopyDirFiles($component_path, $_SERVER["DOCUMENT_ROOT"].'/bitrix/components', true, true);
		}
		else
		{
			throw new InvalidPathException($component_path);
		}
	}
	
	public function InstallDB()
	{
		Loader::IncludeModule($this->MODULE_ID);
		
		$entities = $this->getEntities();
		
		foreach($entities as $entity)
		{
			if(!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName()))
			{
				Base::getInstance($entity)->createDbTable();
			}
		}
		$this->installManyToManyTable();
	}
	
	private function getEntities()
	{
		return[
			AuthorModuleTable::class,
			BookModuleTable::class
		];
	}
	
	private function installManyToManyTable()
	{
		$connection = Application::getConnection();
		$tableName = 'otus_book_author_module';
		
		if(!$connection->isTableExists($tableName))
		{
			$connection->queryExecute(
				"CREATE TABLE {$tableName} (
					BOOK_ID int NOT NULL,
					AUTHOR_ID int NOT NULL,
					PRIMARY KEY (BOOK_ID, AUTHOR_ID)
				)
			");
		}
	}
	
	public function InstallEvents()
	{
		$eventManager = EventManager::getInstance();
		
		$eventManager->registerEventHandler(
			'crm',
			'onEntityDetailsTabsInitialized',
			$this->MODULE_ID,
			'\\Otus\\Crmcustomtab\\Crm\\Handlers',
			'updateTabs'
		);
	}
	
	public function UnInstallFiles()
	{
		$component_path = $this->getPath(). '/install/components';
		
		if(Directory::isDirectoryExists($component_path))
		{
			$installed_components = new \DirectoryIterator($component_path);
			foreach($installed_components as $component)
			{
				if($component->isDir() && !$component->isDot())
				{
					$target_path = $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/'.$component->getFilename();
					if(Directory::isDirectoryExists($target_path))
					{
						Directory::deleteDirectory($target_path);
					}
				}
			}
		}
		else
		{
			throw new InvalidPathException($component_path);
		}
	}
	
	public function UnInstallDB()
	{
		Loader::IncludeModule($this->MODULE_ID);
		
		$connection = Application::getConnection();
		
		$entities = $this->getEntities();
		
		$this->unInstallManyToManyTable();
		
		foreach($entities as $entity)
		{
			if(Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName()))
			{
				$connection->dropTable($entity::getTableName());
			}
		}
	}
	
	private function unInstallManyToManyTable()
	{
		$connection = Application::getConnection();
		
		$tableName = 'otus_book_author_module';
		
		if($connection->isTableExists($tableName))
		{
			$connection->dropTable($tableName);
		}
	}
	
	public function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();
		
		$eventManager->unRegisterEventHandler(
			'crm',
			'onEntityDetailsTabsInitialized',
			$this->MODULE_ID,
			'\\Otus\\Crmcustomtab\\Crm\\Handlers',
			'updateTabs '
		);
	}
	
	public function getPath($notDocumentRoot = false)
	{
		if($notDocumentRoot)
		{
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		}
		else
		{
			return dirname(__DIR__);
		}
	}
	
	public function isVersionD7()
	{
		return CheckVersion(ModuleManager::getVersion('main'), '20.00.00');
	}
}