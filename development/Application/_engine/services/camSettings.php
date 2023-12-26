<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCamSettings extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\camSettings');
  }

//Author "" Yoseph
//June 2017
  public  function getKeys($type=false)
  {
    //if no 'can' or 'j1850' is specified show all
    if(!isset($type)) return $this->db->rows("SELECT * FROM camSettings ORDER BY sortOrder, Manu ASC" );

    //else
       else {
    return $this->db->rows("SELECT * FROM camSettings where `type` = ? ORDER BY sortOrder, Manu ASC", array($type));
           }
    
  }

public  function getYearModel($v,$y){
  return $this->db->row("SELECT year,model FROM VinDesignator where SUBSTRING(vin,1,2) = ? and year= ?",  array($v,$y));
  // return 133;
}


   public  function getCamShaft($type)
  {
   
    return $this->db->rows("SELECT * FROM camSettings where `motor` = ? ORDER BY sortOrder, Manu ASC", array($type));
    
  }



}

?>