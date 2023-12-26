<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIntakeValveOpen extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IntakeValveOpen');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IntakeValveOpen';
   }
}

?>
