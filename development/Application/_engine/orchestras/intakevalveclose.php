<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIntakeValveClose extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IntakeValveClose');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IntakeValveClose';
   }
}

?>
