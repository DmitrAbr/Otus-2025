<?php

namespace Models\Lists;

use Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\Entity\ReferenceField;
use Models\Lists\DoctorProcedureValuesPropertyTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

class DoctorTable extends AbstractIblockPropertyValuesTable
{

    public const IBLOCK_ID = 17;

    public static function getMap(): array
	{
		$map = [
			'PROCEDURE' => new ReferenceField(
				'PROCEDURE',
				DoctorProcedureValuesPropertyTable::class,
				['=this.PROCEDURE_ID' => 'ref.IBLOCK_ELEMENT_ID']
			)		
		];

		return parent::getMap() + $map;

	}

}