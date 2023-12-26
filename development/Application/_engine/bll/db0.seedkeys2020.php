<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $seedKeyID
 * @property int $seed
 * @property int $asciiKey
 */
class SeedKeys2020 extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSeedKeys2020(), __CLASS__);
    parent::__construct($pk);
  }
}

?>