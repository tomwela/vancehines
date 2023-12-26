<?php

namespace ClickBlocks\Core;

use ClickBlocks\DB;

require_once('phpunit.php');

$config = Register::getInstance()->config;

/*DB\ORM::getInstance()->addDB('db_test_0');
DB\ORM::getInstance()->parseXML($config->root . '/Framework/_tests/ORM/data/classes/db.xml');
$obj = DB\ORM::getInstance()->getORMInfoObject();

$sql = new DB\SQLGenerator();
echo $sql->getRow('Customers', array('userID', 'customerID'), true);
exit;*/

chdir($config->root . '/Framework/_plugins');

require_once($config->root . '/Framework/_plugins/PHPUnit/Framework.php');
require_once($config->root . '/Framework/_plugins/PHPUnit/TextUI/ResultPrinter.php');

$config->dirs['dal'] = '/Framework/_tests/ORM/data/classes/dal';
$config->dirs['bll'] = '/Framework/_tests/ORM/data/classes/bll';
$config->dirs['services'] = '/Framework/_tests/ORM/data/classes/services';
$config->dirs['orchestras'] = '/Framework/_tests/ORM/data/classes/orchestras';
$config->dirs['engine'] = '/Framework/_tests/ORM/data/classes';

$suite = new \PHPUnit_Framework_TestSuite();
$suite->addTestFiles(array($config->root . '/Framework/_tests/ORM/daltable.php'));
$suite->addTestFiles(array($config->root . '/Framework/_tests/ORM/blltable.php'));
$suite->addTestFiles(array($config->root . '/Framework/_tests/ORM/service.php'));

echo '<pre>';
foo(new \PHPUnit_TextUI_ResultPrinter(null, true))->printResult($suite->run(null, false, array('ORM')));
echo '</pre>';

//print_r(DB\DB::getStatistic());

?>