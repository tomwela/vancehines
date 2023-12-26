<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraGearRatios extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\GearRatios');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\GearRatios';
   }
}

?>
