<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCLLambdaRange extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CLLambdaRange');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CLLambdaRange';
   }
}

?>
