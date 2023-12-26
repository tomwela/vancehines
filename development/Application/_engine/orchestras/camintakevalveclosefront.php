<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCAMIntakeValveCloseFront extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\CAMIntakeValveCloseFront');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CAMIntakeValveCloseFront';
  }
}

?>
