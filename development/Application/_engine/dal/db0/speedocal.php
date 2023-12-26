<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $speedocalID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALSpeedoCal extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'SpeedoCal');
  }
}

?>