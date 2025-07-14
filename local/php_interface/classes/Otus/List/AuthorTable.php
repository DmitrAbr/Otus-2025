<?php

namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Models\Lists\BookTable;

class AuthorTable extends DataManager
{
	public static function getTableName(): string
	{
		return 'otus_author';
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
				
			(new ManyToMany('BOOKS', BookTable::class))
				->configureTableName('otus_book_author')
				->configureLocalPrimary('ID', 'AUTHOR_ID')
				->configureRemotePrimary('ID', 'BOOK_ID')
				->configureTitle('Авторы'),
			
		];
	}
}