<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSeedKeys_J1850 extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SeedKeys_J1850');
  }

  public  function getKeys($seed)
  {
    return $this->db->rows("Select * FROM SeedKeys_J1850 where seed = ?", array($seed));
  }
}

?>