<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**

 */
class DALSpeedLimFront extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'SpeedLimFront');
   }
}

?>