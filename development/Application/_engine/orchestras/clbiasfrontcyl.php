<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCLBiasFrontCyl extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\CLBiasFrontCyl');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CLBiasFrontCyl';
  }
}

?>
