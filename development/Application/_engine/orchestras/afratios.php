<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraAFRatios extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\AFRatios');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\AFRatios';
   }
}

?>