<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $warmupenrichmentID
 * @property int $mapID
 * @property text $data
 * @property datetime $updated
 * @property int $updatedBy
 */
class DALWarmUpEnrichment extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'WarmUpEnrichment');
   }
}

?>
