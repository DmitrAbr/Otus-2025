<?php

namespace Models;


use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DateField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\TextField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator,
    Bitrix\Main\ORM\Fields\Validator\Base,
    Bitrix\Main\ORM\Fields\Validators\RegExpValidator,
    Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Bitrix\Main\ORM\Fields\Relations\ManyToMany,
    Bitrix\Main\Entity\Query\Join;

use Models\PublisherTable as Publisher;
use Models\AuthorTable as Author;
use Models\WikiprofileTable as Wikiprofile;
/**
 * Class BookTable
 *
 * @package Models
 */
class BookTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'books';
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
            'name' => (new StringField('name',
                    [
                        'validation' => [__CLASS__, 'validateName']
                    ]
            ))->configureTitle(Loc::getMessage('_ENTITY_NAME_FIELD')),
            'text' => (new TextField('text',
                    []
                ))->configureTitle(Loc::getMessage('_ENTITY_TEXT_FIELD')),
            'publish_date' => (new DateField('publish_date',
                    []
                ))->configureTitle(Loc::getMessage('_ENTITY_PUBLISH_DATE_FIELD')),
           
           'ISBN' => (new StringField('ISBN',
                    [
                        'validation' => [__CLASS__, 'validateIsbn']
                    ]
            ))
           ->configureTitle(Loc::getMessage('_ENTITY_ISBN_FIELD')),

            'author_id' => (new IntegerField('author_id',
                    []
                ))->configureTitle(Loc::getMessage('_ENTITY_AUTHOR_ID_FIELD')),
            'publisher_id' => (new IntegerField('publisher_id',
                    []
                ))->configureTitle(Loc::getMessage('_ENTITY_PUBLISHER_ID_FIELD')),

            'wikiprofile_id' => (new IntegerField('wikiprofile_id',
                    []
                ))->configureTitle(Loc::getMessage('_ENTITY_WIKIPROFILE_ID_FIELD')),


            (new ManyToMany('AUTHORS', Author::class))
            ->configureTableName('book_author')
            ->configureLocalPrimary('id', 'book_id')
            ->configureLocalReference('BOOKS')
            ->configureRemotePrimary('id', 'author_id')
            ->configureRemoteReference('AUTHORS'),

            (new Reference('PUBLISHER', Publisher::class, Join::on('this.publisher_id', 'ref.id')))
            ->configureJoinType('inner'),

            (new Reference('WIKIPROFILE', Wikiprofile::class, Join::on('this.wikiprofile_id', 'ref.id')))
            ->configureJoinType('inner')

        ];
    }

    /**
     * Returns validators for name field.
     *
     * @return array
     */
    public static function validateName()
    {
        return [
            new LengthValidator(3, 50),
        ];
    }

    /**
     * Returns validators for ISBN field.
     *
     * @return array
     */
    public static function validateIsbn()
    {
        return 
            array(function($value) {
                $clean = str_replace('-', '', $value);
                if (preg_match('/[\d-]{13,}/', $clean))
                {
                    return true;
                }
                else
                {
                    return 'Код ISBN должен содержать 13 цифр.';
                }
            });
    }
}
