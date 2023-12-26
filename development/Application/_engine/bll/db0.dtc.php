<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $code
 * @property int $short
 * @property int $description
 */
class Dtc extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALDtc(), __CLASS__);
    parent::__construct($pk);
  }
}

?>