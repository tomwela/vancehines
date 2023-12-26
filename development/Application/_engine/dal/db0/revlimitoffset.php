<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $accelerationID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALRevLimitOffset extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'RevLimitOffset');
   }
}

?>