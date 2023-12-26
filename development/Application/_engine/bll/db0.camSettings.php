<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property varchar $manu
 * @property varchar $name
 * @property float $IOFront
 * @property float $IORear
 * @property float $ICFront
 * @property float $ICRear
 */
class CamSettings extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALCamSettings(), __CLASS__);
    parent::__construct($pk);
  }
}

?>