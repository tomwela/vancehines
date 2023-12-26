<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCAMIntakeValveOpenFront extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\CAMIntakeValveOpenFront');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CAMIntakeValveOpenFront';
  }
}

?>
