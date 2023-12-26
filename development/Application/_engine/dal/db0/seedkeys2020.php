<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $seedKeyID
 * @property int $seed
 * @property int $asciiKey
 */
class DALSeedKeys2020 extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'SeedKeys2020');
   }
}

?>