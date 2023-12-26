<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $knockmintemphystID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALKnockMinTempHyst extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'KnockMinTempHyst');
   }
}

?>
