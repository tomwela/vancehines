<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCLMinTempHyst extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CLMinTempHyst');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CLMinTempHyst';
   }
}

?>
