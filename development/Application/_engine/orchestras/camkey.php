<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCAMKey extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\CAMKey');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\CAMKey';
  }
}

?>
