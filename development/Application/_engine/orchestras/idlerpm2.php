<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIdleRPM2 extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\IdleRPM2');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IdleRPM2';
  }
}

?>
