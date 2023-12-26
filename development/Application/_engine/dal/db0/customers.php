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
 * @property varchar $note
 * @property varchar $ipAddress
 * @property int $RAN
 * @property int $customerNumber
 * @property varchar $contactPreferences
 * @property datetime $created
 */
class DALCustomers extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'Customers');
   }
}

?>