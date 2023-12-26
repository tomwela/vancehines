<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraMAPLoadNorm extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\MAPLoadNorm');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\MAPLoadNorm';
   }
}

?>
