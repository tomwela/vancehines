<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCTSparkRear extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CTSparkRear');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CTSparkRear';
   }
}

?>
