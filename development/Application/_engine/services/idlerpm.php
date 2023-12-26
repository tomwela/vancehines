<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceIdleRPM extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\IdleRPM');
   }
}

?>