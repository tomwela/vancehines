<?php

require_once(__DIR__ . '/../../Application/connect.php');

use ClickBlocks\Core;

$reg = Core\Register::getInstance();
$reg->cache->clean();

$loader = $reg->loader;
$loader->setClasses(array());
$loader->fillCache();

if ($reg->config->isDebug) echo 'Total: <b>' . count($loader->getClasses()) . '</b><br /><pre>' . print_r($loader->getClasses(), true) . '</pre>';

echo 'ok';

?>
