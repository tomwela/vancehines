<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraVEType extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\VEType');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\VEType';
   }
}

?>
