<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCamSettings extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CamSettings');
  }

//Author "" Yoseph
//June 2017
  public  function getKeys($type=false)
  {
  	//if no 'can' or 'j1850' is specified show all
    if(!isset($type)) $this->db->rows("SELECT * FROM camSettings ORDER BY sortOrder, Manu ASC" );

    
       else {
       
    return $this->db->rows("SELECT * FROM camSettings where `type` = ? ORDER BY sortOrder, Manu ASC", array($type));
           }
    
  }

  


}

?>