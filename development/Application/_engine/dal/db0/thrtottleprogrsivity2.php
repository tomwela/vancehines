<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $ThrtottleProgrsivity2ID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */ 
class DALThrtottleProgrsivity2 extends \ClickBlocks\DB\DALTable 
{
   public function __construct()
   {
      parent::__construct('db0', 'ThrtottleProgrsivity2');
   }
}

?>