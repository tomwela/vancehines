<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraMPGAdj extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\MPGAdj');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\MPGAdj';
   }
}

?>
