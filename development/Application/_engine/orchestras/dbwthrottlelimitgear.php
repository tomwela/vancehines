<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraDBWThrottleLimitGear extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\DBWThrottleLimitGear');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\DBWThrottleLimitGear';
   }
}

?>
