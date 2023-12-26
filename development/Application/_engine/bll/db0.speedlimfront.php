<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;



class SpeedLimFront extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSpeedLimFront(), __CLASS__);
    parent::__construct($pk);
  }
}

?>