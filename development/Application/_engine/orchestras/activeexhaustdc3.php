<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraActiveExhaustDC3 extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\ActiveExhaustDC3');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\ActiveExhaustDC3';
   }
}

?>
