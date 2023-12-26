<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIACCrankVSTemp extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IACCrankVSTemp');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IACCrankVSTemp';
   }
}

?>
