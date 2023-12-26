<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


class VinDesignatorGuide extends \ClickBlocks\DB\BLLTable{
  public function __construct($pk = null)
  {
    $this->addDAL(new DALVinDesignatorGuide(), __CLASS__);
    parent::__construct($pk);
  }
}

?>