<?php 

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraOtacoderead extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Otacoderead');
  }
//Author "" Yoseph
//January 2018

// public function codeReadUpdate($a,$b,$c,$d,$vin=false){



// if($vin){
//   return $this->db->row("SELECT CONCAT(url,filename) as url from `Otacoderead` where CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <=? and CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >=? and hardware=? and app=? and os=? and vin=?", array($a,$a, $b,$c,$d,$vin));
//  }

// else {
//  return $this->db->row("SELECT CONCAT(url,filename) as url from `Otacoderead` where CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <=? and CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >=? and hardware=? and app=? and os=?", array($a,$a, $b,$c,$d));
//  }


// }
///*******************************************************************
  public function codeReadUpdate($userFirmware, $userHardware,$userApp, $userOS, $vin)
    {

    	//$w=$vin==true ? " WHERE vin = $vin " : " WHERE vin is NULL "; //If Vin is provided
    	if($vin){
 $w=" WHERE vin = '$vin' ";
    	}
    	else{
 $w=" WHERE vin is NULL ";
      }



        $sql = "SELECT CONCAT(url, filename) url
                FROM Otacoderead 
                $w
                AND os = ? 
                AND app = ? 
                AND hardware = ? 
                AND CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->row($sql, array($userOS, $userApp, $userHardware, $userFirmware, $userFirmware));
    }

}



 ?>