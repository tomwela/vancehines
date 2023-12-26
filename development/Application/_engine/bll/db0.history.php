<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $historyID
 * @property int $mapID
 * @property int $customerID
 * @property datetime $date
 * @property int $userID
 * @property navigation $customers
 * @property navigation $maps
 * @property navigation $users
 */
class History extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALHistory(), __CLASS__);
    parent::__construct($pk);
  }

  protected  function _initmaps()
  {
    $this->navigators['maps'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'maps');
  }

  protected  function _initcustomers()
  {
    $this->navigators['customers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'customers');
  }

  protected  function _initusers()
  {
    $this->navigators['users'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'users');
  }
}

?>