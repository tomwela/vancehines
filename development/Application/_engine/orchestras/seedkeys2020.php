<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraSeedKeys2020 extends Orchestra
{
  public  function __construct()
  {
      parent::__construct('\ClickBlocks\DB\SeedKeys2020');
  }

  public  function getKeys($seed)
  {
    return $this->db->rows("Select * FROM SeedKeys2020 where seed = ?", array($seed));
  }

// public  function updateKey2020($ascii, $seed)
//   {
//     return $this->db->rows("UPDATE SeedKeys2020 SET asciiKey= ? where seed = ?", array($ascii, $seed));
//   }


//   public  function keyFromSeed($seed)
//   {
//     return $this->db->row("SELECT * FROM SeedKeys2020 where seed = ?", array($seed));
//   }

}

?>