<?php

require_once('../../Application/connect.php');

use ClickBlocks\Core;

$cache = Core\Register::getInstance()->cache;
if (method_exists($cache, 'gc')) $cache->gc();

echo 'ok.';

?>