<?php

namespace App\Debug;

use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\FileExceptionHandlerLog;

class Log extends FileExceptionHandlerLog
{
    private $level;

    public static function addLog ($message, bool $clear = false, string $fileName = 'Otus-CustomLog'){

        $logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/'. $fileName .'_' . date("d.m.Y") . '.log';

        $_message = date("d.m.Y H:i:s") . ': ' . print_r($message, true);
        $_message .= "\n--------------\n";

        if($clear)
        {
            file_put_contents($logFile, $_message);       
        }
        else
        {
            file_put_contents($logFile, $_message, FILE_APPEND);
        }
    }
}