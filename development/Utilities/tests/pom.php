<?php

use ClickBlocks\Core;

require_once('phpunit.php');

$suite = new \PHPUnit_Framework_TestSuite();
$suite->addTestFiles(array($config->root . '/Framework/_tests/POM/helpers.php'));

echo '<pre>';
foo(new \PHPUnit_TextUI_ResultPrinter(null, true))->printResult($suite->run(null, false, array('Helpers')));
echo '</pre>';

?>
