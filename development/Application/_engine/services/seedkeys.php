<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceSeedKeys extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SeedKeys');
   }
}

?>