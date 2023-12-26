<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraActiveExhaustDC4 extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\ActiveExhaustDC4');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\ActiveExhaustDC4';
   }
}

?>
