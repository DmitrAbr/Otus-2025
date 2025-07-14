<?php

namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Models\Lists\AuthorTable;
use Models\Lists\DoctorTable;
use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Iblock\Elements\ElementspecsTable;
use Bitrix\Main\Loader;

Loader::IncludeModule('iblock');

class BookTable extends DataManager
{
	public static function getTableName(): string
	{
		return 'otus_book';
	}
	
	public static function getMap(): array
	{
		return [
			
			(new IntegerField('ID'))
				->configurePrimary()
				->configureAutocomplete()
				->configureTitle('ID'),
			
			(new StringField('TITLE'))
				->configureRequired()
				->configureTitle('Название'),
				
			(new IntegerField('PAGES'))
				->configureTitle('К-во страниц'),
				
			(new TextField('DESCRIPTION'))
				->configureTitle('Описание'),
				
			(new DateField('PUBLISH_DATE'))
				->configureTitle('Дата публикации'),
				
			(new ManyToMany('AUTHORS', AuthorTable::class))
				->configureTableName('otus_book_author')
				->configureLocalPrimary('ID', 'BOOK_ID')
				->configureRemotePrimary('ID', 'AUTHOR_ID')
				->configureTitle('Авторы'),
				
			(new ManyToMany('RECOMMENDS_DOCTOR', ElementDoctorsTable::class))
				->configureTableName('otus_book_recommends')
				->configureLocalPrimary('ID', 'BOOK_ID')
				->configureRemotePrimary('ID', 'DOCTOR_ID')
				->configureTitle('Рекоммендации'),
				
			(new ManyToMany('SPECS', ElementspecsTable::class))
				->configureTableName('otus_book_specs')
				->configureLocalPrimary('ID', 'BOOK_ID')
				->configureRemotePrimary('ID', 'SPEC_ID')
				->configureTitle('Специализации')
			
		];
	}
}