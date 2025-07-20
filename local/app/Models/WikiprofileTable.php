<?php
namespace Models;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator,
	Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Bitrix\Main\ORM\Fields\Relations\ManyToMany,
    Bitrix\Main\Entity\Query\Join;

use Models\BookTable as Book;

/**
 * Class WikiprofileTable
 * 
 * @package Models
 **/

class WikiprofileTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'wikiprofiles';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'id' => (new IntegerField('id',
					[]
				))->configureTitle(Loc::getMessage('_ENTITY_ID_FIELD'))
						->configurePrimary(true)
						->configureAutocomplete(true),
			'wikiprofile_ru' => (new StringField('wikiprofile_ru',
					[
						'validation' => [__CLASS__, 'validateWikiprofileRu']
					]
				))->configureTitle(Loc::getMessage('_ENTITY_WIKIPROFILE_RU_FIELD')),
			'wikiprofile_en' => (new StringField('wikiprofile_en',
					[
						'validation' => [__CLASS__, 'validateWikiprofileEn']
					]
				))->configureTitle(Loc::getMessage('_ENTITY_WIKIPROFILE_EN_FIELD')),
			'book_id' => (new IntegerField('book_id',
					[]
				))->configureTitle(Loc::getMessage('_ENTITY_BOOK_ID_FIELD'))
						->configureRequired(true),

			 (new Reference('BOOK', Book::class, Join::on('this.book_id', 'ref.id')))->configureJoinType('inner')
		];
	}

	/**
	 * Returns validators for wikiprofile_ru field.
	 *
	 * @return array
	 */
	public static function validateWikiprofileRu()
	{
		return [
			new LengthValidator(null, 50),
		];
	}

	/**
	 * Returns validators for wikiprofile_en field.
	 *
	 * @return array
	 */
	public static function validateWikiprofileEn()
	{
		return [
			new LengthValidator(null, 50),
		];
	}
}