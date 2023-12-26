<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;



class Otacoderead extends \ClickBlocks\DB\BLLTable{
  public function __construct($pk = null)
  {
    $this->addDAL(new DALOtacoderead(), __CLASS__);
    parent::__construct($pk);
  }
}

?>