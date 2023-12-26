<?php

require_once(__DIR__ . '/../../Application/connect.php');

$config = \ClickBlocks\Core\Register::getInstance()->config;

chdir($config->root . '/Framework/_plugins');

require_once($config->root . '/Framework/_plugins/PHPUnit/Framework.php');
require_once($config->root . '/Framework/_plugins/PHPUnit/TextUI/ResultPrinter.php');

?>
