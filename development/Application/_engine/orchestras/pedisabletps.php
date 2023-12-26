<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPEDisableTPS extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PEDisableTPS');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PEDisableTPS';
   }
}

?>
