<?php

namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Models\Lists\BookTable;
use Bitrix\Iblock\Elements\ElementDoctorsTable;

class ClientsTable extends DataManager
{
	public static function getTableName(): string
	{
		return 'otus_clients';
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
				
			(new ManyToMany('DOCTORS', ElementDoctorsTable::class))
				->configureTableName('otus_doctor_client')
				->configureLocalPrimary('ID', 'CLIENT_ID')
				->configureRemotePrimary('ID', 'DOCTOR_ID')
				->configureTitle('Врачи'),
			
		];
	}
}