<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPEAirFuelRatio extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PEAirFuelRatio');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PEAirFuelRatio';
   }
}

?>
