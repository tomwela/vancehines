<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


class FaqCodeRead extends \ClickBlocks\DB\BLLTable{
  public function __construct($pk = null)
  {
    $this->addDAL(new DALFaqCodeRead(), __CLASS__);
    parent::__construct($pk);
  }
}

?>