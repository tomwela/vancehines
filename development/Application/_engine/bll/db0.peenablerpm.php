<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $peenablerpmID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 * @property navigation $maps
 * @property navigation $users
 */
class PEEnableRPM extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALPEEnableRPM(), __CLASS__);
    parent::__construct($pk);
  }

  protected  function _initusers()
  {
    $this->navigators['users'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'users');
  }

  protected  function _initmaps()
  {
    $this->navigators['maps'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'maps');
  }
}

?>