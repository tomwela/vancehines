<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $seedKeyID
 * @property int $seed
 * @property int $asciiKey
 */
class SeedKeys_J1850 extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSeedKeys_J1850(), __CLASS__);
    parent::__construct($pk);
  }
}

?>