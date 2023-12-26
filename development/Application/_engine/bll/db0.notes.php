<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $noteID
 * @property text $note
 * @property int $mapID
 * @property int $createdBy
 * @property datetime $updated
 * @property navigation $maps
 * @property navigation $users
 */
class Notes extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALNotes(), __CLASS__);
    parent::__construct($pk);
  }

  protected  function _initmaps()
  {
    $this->navigators['maps'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'maps');
  }

  protected  function _initusers()
  {
    $this->navigators['users'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'users');
  }
}

?>