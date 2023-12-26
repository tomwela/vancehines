<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPEEnableTPs extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PEEnableTPs');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PEEnableTPs';
   }
}

?>
