<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCrfuel extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Crfuel');
  }

           public  function fuelID($vin_id=false)
            {
          return $this->db->col("SELECT id FROM Crfuel WHERE vin_id = ? ORDER BY id DESC LIMIT 0,1", array($vin_id));
            } 

            public  function fuelInfo($vin_id=false)
            {
          return $this->db->rows("SELECT id,vin_id,mpg,galnum,distance,odm,created FROM Crfuel WHERE vin_id = ? ORDER BY id DESC", array($vin_id));
            }

              public  function deleteRow($vin_id)
            {
              
           $this->db->cols("DELETE FROM Crfuel WHERE vin_id = ?  ORDER BY id DESC LIMIT 1 ", array($vin_id));

            }
            
 }

?>