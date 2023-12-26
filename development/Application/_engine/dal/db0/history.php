<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $historyID
 * @property int $mapID
 * @property int $customerID
 * @property datetime $date
 * @property int $userID
 */
class DALHistory extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'History');
   }
}

?>