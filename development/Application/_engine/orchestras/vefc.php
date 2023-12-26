<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraVEfc extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\VEfc');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\VEfc';
   }
}

?>