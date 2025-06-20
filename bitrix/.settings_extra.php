<?php
return array(
    'exception_handling' => 
  array (
    'value' => 
    array (
      'debug' => true,
      'handled_errors_types' => 4437,
      'exception_errors_types' => 4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => array(
        'class_name' => Otus\Diag\FileExceptionHandlerCustom::class,
        'required_file' => 'php_interface/src/Otus/Diag/FileExceptionHandlerCustom.php',
        'settings' => array(
          'file' => 'local/logs/exceptions.log',
          'log_size' => 1000000,
        ),
      ),
    ),
    'readonly' => false,
  ),
);
