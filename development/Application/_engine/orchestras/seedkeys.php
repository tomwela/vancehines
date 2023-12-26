<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSeedKeys extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SeedKeys');
  }

  public  function getKeys($seed)
  {
    return $this->db->rows("Select * FROM SeedKeys where seed = ?", array($seed));
  }
}

?>