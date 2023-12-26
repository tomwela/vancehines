<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraRevLimitOffset extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\RevLimitOffset');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\RevLimitOffset';
  }
}

?>