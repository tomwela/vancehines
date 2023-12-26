<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $crankingfuelID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALCrankingFuel extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'CrankingFuel');
   }
}

?>
