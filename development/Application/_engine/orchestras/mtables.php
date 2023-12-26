<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraMTables extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\MTables');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\MTables';
   }
}

?>