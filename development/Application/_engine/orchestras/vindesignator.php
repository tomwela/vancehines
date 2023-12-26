<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraVinDesignator extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\VinDesignator');
  }

public function vinID($v,$y){
return $this->db->col("SELECT id from `VinDesignator` where vin=? and year=?",array($v,$y));
}

public function bikeEngine($a,$b,$c){
	
	$c=trim($c);
	$b=trim($b);
	$a=trim($a);
	$g=$this->db->col("SELECT `detail` from `VinDesignatorGuide` where `legend`=? and `model`=? and `type`=?",array($a,$b,$c));
  if($g==false) return "";
  return $g;
}

public function bikeType(){
	return $this->db->rows("SELECT vin,model,type from `VinDesignator`");

}

public  function getYearModel($v,$y){
 
  return $this->db->row("SELECT year,model FROM VinDesignator where SUBSTRING(vin,1,2) = ? and year= ?",  array($v,$y));
  // return 133;
}

// throws an error when the method is put in camsetting file
   public  function getCamShaft($type)
  {
   
    return $this->db->rows("SELECT * FROM camSettings where `motor` = ? ORDER BY sortOrder, Manu ASC", array($type));
    
  }

}

?>