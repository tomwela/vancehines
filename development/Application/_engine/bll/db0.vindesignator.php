<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


class VinDesignator extends \ClickBlocks\DB\BLLTable{
  public function __construct($pk = null)
  {
    $this->addDAL(new DALVinDesignator(), __CLASS__);
    parent::__construct($pk);
  }
}

?>