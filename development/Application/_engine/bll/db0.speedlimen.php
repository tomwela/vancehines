<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class SpeedLImEn extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALSpeedLImEn(), __CLASS__);
    parent::__construct($pk);
  }

}

?>