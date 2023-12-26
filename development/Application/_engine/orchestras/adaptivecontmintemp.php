<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraAdaptiveContMinTemp extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\AdaptiveContMinTemp');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\AdaptiveContMinTemp';
   }
}

?>
