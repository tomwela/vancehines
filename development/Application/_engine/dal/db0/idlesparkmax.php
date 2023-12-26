<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $idlesparkmaxID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALIdleSparkMax extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'IdleSparkMax');
   }
}

?>
