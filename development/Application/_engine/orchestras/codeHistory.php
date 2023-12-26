<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCodeHistory extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CodeHistory');
  }
 
 public function getCodeHistory($params){

 	return $this->db->row("SELECT vin_id, code FROM CodeHistory ");
}

 public function addCodeHistory($code,$params){
 
$vin=$params['vin'];
$u=$params['uname'];


 	 // $getVinId=$this->db->col("SELECT vin_id FROM `CodeReadVin`  WHERE vin=? and macaddress=?",array($vin,$macad));
$getVinId=$this->db->col("SELECT vin_id FROM `CodeReadVin`  WHERE vin=? and mac",array($vin));

 	 if(!$getVinId)  return "error";

 	 //  {
 	 // 	$userID=$this->db->col("SELECT users_id FROM `CodeReadUsers`  WHERE email=?",array($u));
   //  $getVinId=$this->db->col("SELECT vin_id FROM CodeReadVin WHERE users_id=?",array($userID));
 	 // }


 	 	
 	
if($getVinId) { 
	 // $chkCode=$this->db->col("SELECT count(vin_id) FROM `CodeHistory`  WHERE vin_id=? and code=?",array($getVinId,$code));
	 
	// if($chkCode==0){
		$this->db->rows("INSERT INTO CodeHistory (`vin_id`,`code`) VALUES (?,?)",array($getVinId,$code));
	return 'code history added';
// }
// else return "exists";
}

 
}

}

?>