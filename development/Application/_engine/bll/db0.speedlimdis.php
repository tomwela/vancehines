<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;



class SpeedLimDIs extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSpeedLimDIs(), __CLASS__);
    parent::__construct($pk);
  }
}

?>