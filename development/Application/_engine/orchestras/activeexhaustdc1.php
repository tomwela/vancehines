<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraActiveExhaustDC1 extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\ActiveExhaustDC1');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\ActiveExhaustDC1';
   }
}

?>
