<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $clbiasfrontcylID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALCLBiasFrontCyl extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'CLBiasFrontCyl');
  }
}

?>
