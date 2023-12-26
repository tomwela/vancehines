<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property tinytext $min_firmware
 * @property tinytext $firmware
 * @property tinytext $hardware
 * @property tinytext $app
 * @property tinytext $os
 * @property text $url
 * @property text $filename
 * @property timestamp $created
 */ 
class DALOta extends \ClickBlocks\DB\DALTable 
{
   public function __construct()
   {
      parent::__construct('db0', 'ota');
   }
}

?>