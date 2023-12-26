<?php

require_once('../../Application/connect.php');

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\DB\ORM;

$reg = Core\Register::getInstance();

switch (strtolower($_REQUEST['mode']))
{
  case 'all':
    echo 'Delete ALL cache... ';
    $reg->cache->clean();
    break;
  case 'autoload':
    echo 'Delete Autoloader cache... ';
    $reg->loader->cleanCache();
    break;
//case 'db':break;
  default:
  case 'orm':
    echo 'Delete ORM cache... ';
    DB\ORM::getInstance()->cleanCache();
    break;
  case 'tables':
    echo 'Delete Table cache... ';
    DB\Service::cleanCache();
    break;
  case 'one':
    $key = $_REQUEST['key'];
    if (!$key) die('Key not specified.');
    $reg->cache->delete($key);
    break;
}

echo 'ok.';

?>