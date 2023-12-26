<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraThrottleTransGear extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\ThrottleTransGear');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\ThrottleTransGear';
   }
}

?>
