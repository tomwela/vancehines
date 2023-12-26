<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraKnockMinTemp extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\KnockMinTemp');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\KnockMinTemp';
   }
}

?>
