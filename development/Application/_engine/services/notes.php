<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class ServiceNotes extends Service
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\Notes');
   }
}

?>