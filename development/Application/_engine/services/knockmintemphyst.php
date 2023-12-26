<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceKnockMinTempHyst extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\KnockMinTempHyst');
   }
}

?>
