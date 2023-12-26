<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $clmintempID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALCLMinTemp extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'CLMinTemp');
   }
}

?>
