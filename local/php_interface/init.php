<?php
use Bitrix\Main\Page\Asset;

/**
 * - /local/php_interface/classes/{Path|raw}/{*|raw}.php
 * - /local/php_interface/classes/{Path|ucfirst,lowercase}/{*|ucfirst,lowercase}.php
 */


/**
 * Project bootstrap files
 * Include
 * 
 */
 

 
foreach( [
	__DIR__.'/legacy.php',
	
	__DIR__.'/constants.php',
	
	__DIR__.'/vendor/autoload.php',

	__DIR__.'/classes/Otus/AbstractIblockPropertyValuesTable.php',

	__DIR__.'/classes/Otus/List/DoctorTable.php',

	__DIR__.'/classes/Otus/List/DoctorProcedureValuesProperty.php',
	
	__DIR__.'../js/sibcem/bizproc.task.crm/init/init.php',
	
	__DIR__.'/classes/Otus/List/BookTable.php',
	
	__DIR__.'/classes/Otus/List/AuthorTable.php',
	
	__DIR__.'/classes/Otus/List/ClientsTable.php',
	
	__DIR__.'/TaskClass.php',
	
	
	
	]
	as $filePath )
{
	if ( file_exists($filePath) )
	{
		require_once($filePath);
	}
}



unset($filePath);

require(__DIR__.'/autoload.php');

require dirname(__FILE__) . '/events.php';

//Asset::getInstance()->addJs($_SEVER["DOCUMENT_ROOT"] . "/local/js/otus/debug/script.js");
//\CJSCore::Init('otus.working_day');
?>
