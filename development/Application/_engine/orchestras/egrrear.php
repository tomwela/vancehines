<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraEGRRear extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\EGRRear');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\EGRRear';
   }
}

?>
