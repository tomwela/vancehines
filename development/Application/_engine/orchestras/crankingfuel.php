<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCrankingFuel extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CrankingFuel');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CrankingFuel';
   }
}

?>
