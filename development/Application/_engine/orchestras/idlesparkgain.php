<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIdleSparkGain extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IdleSparkGain');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IdleSparkGain';
   }
}

?>
