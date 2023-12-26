<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraIAC extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\IAC');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\IAC';
  }
}

?>