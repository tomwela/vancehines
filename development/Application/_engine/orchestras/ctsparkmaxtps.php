<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCTSparkMaxTPS extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CTSparkMaxTPS');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CTSparkMaxTPS';
   }
}

?>
