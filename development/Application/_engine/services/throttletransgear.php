<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceThrottleTransGear extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\ThrottleTransGear');
   }
}

?>
