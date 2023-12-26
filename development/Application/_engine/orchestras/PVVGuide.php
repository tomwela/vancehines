<?php

namespace ClickBlocks\DB;

use ClickBlocks\Cache;
use ClickBlocks\Core;

class OrchestraPVVGuide extends \ClickBlocks\DB\Orchestra
{
    public function __construct()
    {
        parent::__construct('\ClickBlocks\DB\PVVGuide');
    }

public function pvvID($ecm){

return $this->db->col("SELECT id from `PVVGuide` where ecmFirmware=? ", array($ecm));

}
   

}

?>