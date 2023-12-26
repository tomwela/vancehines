<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**

 */
class DALSpeedLimDIs extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'SpeedLimDIs');
   }
}

?>