<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSpeedoCal extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SpeedoCal');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\SpeedoCal';
  }
}

?>