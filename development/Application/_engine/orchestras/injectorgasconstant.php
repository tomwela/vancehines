<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraInjectorGasConstant extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\InjectorGasConstant');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\InjectorGasConstant';
   }
}

?>
