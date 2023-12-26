<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraRevLimitArray extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\RevLimitArray');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\RevLimitArray';
  }
}

?>