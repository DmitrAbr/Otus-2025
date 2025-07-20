<?php
namespace Models\Lists;

use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Models\AbstractIblockPropertyValuesTable;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 21;
}
