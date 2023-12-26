<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $mtableID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALMTables extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'MTables');
   }
}

?>