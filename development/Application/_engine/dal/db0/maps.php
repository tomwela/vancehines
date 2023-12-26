<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $mapID
 * @property varchar $name
 * @property text $description
 * @property datetime $updated
 * @property int $updatedBy
 * @property varchar $make
 * @property varchar $model
 * @property smallint $year
 * @property varchar $eManufacturer
 * @property varchar $eName
 * @property varchar $baffleType
 * @property varchar $eSize
 * @property varchar $isCustomerMap
 * @property int $customerID
 * @property varchar $isOriginalMap
 * @property varchar $photoUrl
 */
class DALMaps extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'Maps');
   }
}

?>