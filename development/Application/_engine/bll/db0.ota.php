<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property tinytext $min_firmware
 * @property tinytext $firmware
 * @property tinytext $hardware
 * @property tinytext $app
 * @property tinytext $os
 * @property text $url
 * @property text $filename
 * @property timestamp $created
 */
class Ota extends \ClickBlocks\DB\BLLTable{
  public function __construct($pk = null)
  {
    $this->addDAL(new DALOta(), __CLASS__);
    parent::__construct($pk);
  }
}

?>