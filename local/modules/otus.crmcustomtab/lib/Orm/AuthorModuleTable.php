<?php

namespace Otus\Crmcustomtab\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Otus\Crmcustomtab\Orm\BookModuleTable;

class AuthorModuleTable extends DataManager
{
	public static function getTableName(): string
	{
		return 'otus_author_module';
	}
	
	public static function getMap(): array
	{
		return [
			
			(new IntegerField('ID'))
				->configurePrimary()
				->configureAutocomplete()
				->configureTitle('ID'),
			
			(new StringField('FIRST_NAME'))
				->configureTitle('Имя'),
				
			(new StringField('LAST_NAME'))
				->configureTitle('Фамилия'),
				
			(new StringField('SECOND_NAME'))
				->configureTitle('Отчество'),
				
			(new DateField('BIRTH_DATE'))
				->configureTitle('Дата рождения'),
				
			(new TextField('BIOGRAPHY'))
				->configureTitle('Биография'),
				
			(new ManyToMany('BOOKS', BookModuleTable::class))
				->configureTableName('otus_book_author_module')
				->configureLocalPrimary('ID', 'AUTHOR_ID')
				->configureRemotePrimary('ID', 'BOOK_ID')
				->configureTitle('Авторы'),
			
		];
	}
}