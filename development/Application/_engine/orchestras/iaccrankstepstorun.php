<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIACCrankStepsToRun extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IACCrankStepsToRun');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IACCrankStepsToRun';
   }
}

?>
