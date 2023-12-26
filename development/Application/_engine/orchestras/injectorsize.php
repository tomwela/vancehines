<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraInjectorSize extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\InjectorSize');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\InjectorSize';
   }
}

?>
