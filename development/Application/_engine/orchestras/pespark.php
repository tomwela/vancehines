<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraPESpark extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\PESpark');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\PESpark';
   }
}

?>
