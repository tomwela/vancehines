<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceIAC extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\IAC');
   }
}

?>