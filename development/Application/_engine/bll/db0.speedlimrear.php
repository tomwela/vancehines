<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;



class SpeedLimRear extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSpeedLimRear(), __CLASS__);
    parent::__construct($pk);
  }
}

?>