<?php
if (is_file(__DIR__.'/.local.ini')) {
	define('APP_CONFIG_FILE', __DIR__.'/.local.ini');
} else {
	define('APP_CONFIG_FILE', __DIR__.'/config.ini');
}
define('ROOT_PATH', __DIR__);

session_start();

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once 'include/edb.class.php';
require_once 'include/config.class.php';
require_once 'include/excel_reader2.php';
require_once ROOT_PATH.'/../../Framework/_plugins/PHPExcel/PHPExcel.php';
