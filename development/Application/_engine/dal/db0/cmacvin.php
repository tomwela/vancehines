<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $code
 * @property int $short
 * @property int $description
 */
class DALCmacVin extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'CmacVin');
   }
}

?>