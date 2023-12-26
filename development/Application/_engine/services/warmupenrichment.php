<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceWarmUpEnrichment extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\WarmUpEnrichment');
   }
}

?>
