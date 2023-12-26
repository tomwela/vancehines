<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSparkFront extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SparkFront');
  }
}

?>