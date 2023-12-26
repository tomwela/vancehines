<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraKnockMinTempHyst extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\KnockMinTempHyst');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\KnockMinTempHyst';
   }
}

?>
