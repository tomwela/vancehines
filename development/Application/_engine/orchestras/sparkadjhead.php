<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSparkAdjHead extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\SparkAdjHead');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\SparkAdjHead';
   }
}

?>
