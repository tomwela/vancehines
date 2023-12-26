<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSystemSwitches extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\SystemSwitches');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\SystemSwitches';
   }
}

?>
