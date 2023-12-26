<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraMaxKnockRetard extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\MaxKnockRetard');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\MaxKnockRetard';
   }
}

?>
