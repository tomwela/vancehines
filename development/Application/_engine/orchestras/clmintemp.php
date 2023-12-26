<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCLMinTemp extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CLMinTemp');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CLMinTemp';
   }
}

?>
