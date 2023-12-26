<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSparkAdjEng extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\SparkAdjEng');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\SparkAdjEng';
   }
}

?>
