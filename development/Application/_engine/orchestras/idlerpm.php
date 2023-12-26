<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIdleRPM extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IdleRPM');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IdleRPM';
   }
}

?>