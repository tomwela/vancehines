<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $code
 * @property int $short
 * @property int $description
 */
class Orders extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALOrders(), __CLASS__);
    parent::__construct($pk);
  }
}

?>