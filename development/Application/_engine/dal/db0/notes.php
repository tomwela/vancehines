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
 */
class DALNotes extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'Notes');
   }
}

?>