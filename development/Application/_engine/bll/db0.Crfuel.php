<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;



class Crfuel extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALCrfuel(), __CLASS__);
    parent::__construct($pk);
  }
}

?>