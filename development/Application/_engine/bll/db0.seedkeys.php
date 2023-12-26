<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $seedKeyID
 * @property int $seed
 * @property int $asciiKey
 */
class SeedKeys extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSeedKeys(), __CLASS__);
    parent::__construct($pk);
  }
}

?>