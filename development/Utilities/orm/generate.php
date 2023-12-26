<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\DB\Sync;

require_once(__DIR__ . '/../../Application/connect.php');

$mode = urldecode($_GET['mode']);

$orm = ORM::getInstance();
$orm->addAllDB();

switch ($mode)
{
   case 'xml':
     $orm->generateXML('ClickBlocks\\DB');
     break;
   case 'classes':
     $orm->generateClasses();
     break;
   case 'permissions':
     $pms = new PMS();
     $pms->create($_GET['dbalias'] ?: 'db0', $_GET['table'] ?: 'Users', $_GET['field'] ?: 'UserID');
     break;
   case 'sync':
     $config = Core\Register::getInstance()->config;
     foreach ($orm->getDBAliases() as $alias)
     {
       $cfg = $config[$alias];
       $file = Core\IO::dir('engine') . '/' . $alias . '.bin';
       $s = new Sync\Synchronizer();
       $s->setInfoTables('/^lookup.*/i');
       if (!is_file($file)) $s->out($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->in($file)->sync();
       switch (strtolower($_GET['dir']))
       {
         case 'in':
           $res = $s->out($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->in($file)->sync();  
           break;
         case 'out':
           $res = $s->out($file)->in($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->sync();  
           break;
         default:
           die('Incorrect direction. It can be only "in" or "out".');
       }
     }
     foo(new ORMSynchronizer())->sync();
     echo '<pre>' . print_r($res, true) . '</pre>';
     break;
   default:
     die('The mode "' . $mode . '" is not recognized.');
}

echo 'ok.';

?>