<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**

 */
class DALSpeedLimRear extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'SpeedLimRear');
   }
}

?>