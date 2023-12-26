<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraActiveExhaustDC2 extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\ActiveExhaustDC2');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\ActiveExhaustDC2';
   }
}

?>
