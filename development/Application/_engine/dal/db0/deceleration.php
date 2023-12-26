<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $decelerationID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */ 
class DALDeceleration extends \ClickBlocks\DB\DALTable 
{
   public function __construct()
   {
      parent::__construct('db0', 'Deceleration');
   }
}

?>