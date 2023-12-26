<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIdleSparkMax extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IdleSparkMax');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IdleSparkMax';
   }
}

?>
