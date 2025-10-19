<?php
require_once (__DIR__.'/crest.php');

define('C_REST_CLIENT_ID', 'local.68f44405193ce6.35994078');
define('C_REST_CLIENT_SECRET', '7ogQvO1iKZyyZIjhPCB6P69XPrM6JTAA7MflW7tVkwfud2gw1S');

$result = CRest::installApp();

echo '<PRE>';
print_r($result);
echo '</PRE>';


$res = CRest::call(
	'event.unbind',
	[
		'EVENT' => 'ONCRMACTIVITYADD',
		'HANDLER' => 'https://co58172.tw1.ru/crm_activity/handler.php',
		'AUTH_TYPE' => 1
	]
);

$res = CRest::call(
	'event.bind',
	[
		'EVENT' => 'ONCRMACTIVITYADD',
		'HANDLER' => 'https://co58172.tw1.ru/crm_activity/handler.php',
		'AUTH_TYPE' => 1
	]
);

echo '<PRE>';
print_r($res);
echo '</PRE>';


?>