<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceHistory extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\History');
   }
}

?>