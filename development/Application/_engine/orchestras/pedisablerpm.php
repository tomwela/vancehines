<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPEDisableRPM extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PEDisableRPM');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PEDisableRPM';
   }
}

?>
