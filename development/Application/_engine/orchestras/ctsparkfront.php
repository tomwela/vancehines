<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCTSparkFront extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CTSparkFront');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CTSparkFront';
   }
}

?>
