<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSparkAdjAir extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\SparkAdjAir');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\SparkAdjAir';
   }
}

?>
