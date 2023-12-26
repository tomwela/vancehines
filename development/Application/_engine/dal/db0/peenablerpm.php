<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $peenablerpmID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALPEEnableRPM extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'PEEnableRPM');
   }
}

?>
