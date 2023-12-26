<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCRmaintenance extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CRmaintenance');
  }

public function bikeDataPut($params){
		$t=$params['type'];
	$bid=$params['bikeid'];
	$date=$params['date'];
	$data=$params['data'];
	$id=$params['id'];

if(isset($id)){
	$this->db->col("UPDATE  CRmaintenance SET `type`=?,`date`=?,`data`=? WHERE id=?" ,array($t,$date,$data,$id));
}

else $this->db->col("INSERT INTO CRmaintenance (`type`,`bike_id`,`date`,`data`) VALUES (?,?,?,?)",array($t,$bid,$date,$data));
 $sel=$this->db->rows("SELECT id, type, bike_id, `data`, `date` FROM CRmaintenance WHERE bike_id=? ",array($bid));
return $sel;

}


public function deleteInfo($params){

$id=$params['id'];
$sel= $this->db->cols("SELECT id FROM CRmaintenance WHERE id=? ",array($id));
if(count($sel)== 0) return 0; 
else $this->db->col("DELETE FROM CRmaintenance WHERE id=? ",array($id));

}

public function getInfo($params){

$bid=$params['bikeid'];
$sel= $this->db->rows("SELECT id, type, bike_id, `data`, `date` FROM CRmaintenance WHERE bike_id=? ",array($bid));
if(count($sel)== 0) return 0; 

else return $sel;

}


}

?>