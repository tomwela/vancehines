<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $customerID
 * @property varchar $fullName
 * @property varchar $address
 * @property varchar $address2
 * @property varchar $city
 * @property varchar $state
 * @property varchar $zip
 * @property varchar $country
 * @property varchar $phone
 * @property varchar $email
 * @property varchar $make
 * @property varchar $model
 * @property smallint $year
 * @property varchar $vin
 * @property varchar $ecmFirmware
 * @property varchar $ecmPart
 * @property varchar $fVersion
 * @property varchar $hVersion
 * @property varchar $aVersion
 * @property int $currentMap
 * @property datetime $updated
 * @property int $updatedBy
 * @property int $lastPing
 * @property varchar $note
 * @property int $noteFlag
 * @property varchar $calID
 * @property varchar $ipAddress
 * @property varchar $demoMode
 * @property int $RAN
 * @property int $customerNumber
 * @property varchar $contactPreferences
 * @property datetime $created
 */
class Customers extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALCustomers(), __CLASS__);
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

  protected  function _inithistory()
  {
    $this->navigators['history'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'history');
  }
}

?>