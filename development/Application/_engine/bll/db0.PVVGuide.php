<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property varchar $id
 */
class PVVGuide extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALPVVGuide(), __CLASS__);
    parent::__construct($pk);
  }
}

?>