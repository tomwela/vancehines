<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int     $key
 * @property varchar $manu
 * @property varchar $name
 * @property float   $IOFront
 * @property float   $IORear
 * @property float   $ICFront
 * @property float   $ICRear
 * @property int     $sortOrder
 */
class DALCamSettings extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'camSettings');
   }
}

?>