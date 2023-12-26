<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraWarmUpEnrichment extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\WarmUpEnrichment');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\WarmUpEnrichment';
   }
}

?>
