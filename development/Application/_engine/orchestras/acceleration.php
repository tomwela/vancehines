<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraAcceleration extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Acceleration');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\Acceleration';
  }
}

?>