<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPEEnableRPM extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PEEnableRPM');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PEEnableRPM';
   }
}

?>
