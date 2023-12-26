<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraVErc extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\VErc');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\VErc';
   }
}

?>