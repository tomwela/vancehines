<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraDtc extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Dtc');
  }


  public  function getDtc($code=false)
  {
     if($code) return $this->db->row("select * from dtc where code = ?", array($code));
     return $this->db->rows("select code,short as shortDescr, description, video as videoUrl from dtc");
  }

 }

?>