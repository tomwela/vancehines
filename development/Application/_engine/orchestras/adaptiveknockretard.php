<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraAdaptiveKnockRetard extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\AdaptiveKnockRetard');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\AdaptiveKnockRetard';
   }
}

?>
