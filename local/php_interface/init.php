<?php

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

	__DIR__.'/classes/Otus/AbstractIblockPropertyValuesTable.php',

	__DIR__.'/classes/Otus/List/DoctorTable.php',

	__DIR__.'/classes/Otus/List/DoctorProcedureValuesProperty.php',
	
	]
	as $filePath )
{
	if ( file_exists($filePath) )
	{
		require_once($filePath);
	}
}
unset($filePath);