<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $activeexhaustdc4ID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALActiveExhaustDC4 extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'ActiveExhaustDC4');
   }
}

?>
