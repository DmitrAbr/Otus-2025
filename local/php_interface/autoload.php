<?php

use Bitrix\Main\Loader;


Loader::registerAutoLoadClasses(null, [
    'Sibcem\Main\SBizproc' => APP_CLASS_FOLDER . 'Sibcem/Main/bizproc.php',
    'Sibcem\Main\UserManager\Substitute' => APP_CLASS_FOLDER . 'Sibcem/Main/subtitute.php',
    'Sibcem\Main\UserManager\Deputy' => APP_CLASS_FOLDER . 'Sibcem/AbstractedClasses/Deputy.php',
    'Sibcem\Main\TaskHandler' => APP_CLASS_FOLDER . 'Sibcem/AbstractedClasses/TaskControl.php',
    'Sibcem\Main\RussianDateFormatter' => APP_CLASS_FOLDER . 'Sibcem/Main/russianDateFormatter.php',
    'Sibcem\Main\STask' => APP_CLASS_FOLDER . 'Sibcem/Main/task.php',
    'Sibcem\Vacancy\LeaveManager' => APP_CLASS_FOLDER . 'Sibcem/Main/leaveManager.php',
    'Sibcem\Main\Test' => APP_CLASS_FOLDER . 'Sibcem/Main/eventCommentHandler.php',
    'Sibcem\Userfield\IBLink' => APP_CLASS_FOLDER . 'Sibcem/Userfield/IBLink.php',
    'Otus\Userfield\CUserTypeRecord' => APP_CLASS_FOLDER . 'Otus/Userfield/CUserTypeRecord.php',
]);