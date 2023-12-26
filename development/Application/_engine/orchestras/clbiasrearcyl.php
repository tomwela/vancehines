<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCLBiasRearCyl extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\CLBiasRearCyl');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CLBiasRearCyl';
  }
}

?>
