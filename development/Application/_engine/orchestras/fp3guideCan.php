<?php 

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraFp3guideCan extends Orchestra
{
  public  function __construct()
  {
   parent::__construct('\ClickBlocks\DB\Fp3guideCan');
  }

//Author "" Yoseph
//September 2017
  public  function getMapCount($params)
  {
     
  
    $cv=substr($params['cVin'],4,2);
    $cv="%$cv%";
    $yr=substr($params['bYear'],2);
    $bm=$params['bModel'];
    $em=$params['eManufacturer'];
    $eName=$params['eName'];
    $baffle=$params['eBaffleType'];
    $eSize=$params['eSize'];
    // $w[]='family='.$this->db->quote($bm);
    $w[]='year='.$this->db->quote($yr);
   
     if(isset($em) and $em !='') $w[]='pipemfr='.$this->db->quote($em);
     if(isset($eName) and $eName !='') $w[]='pipename='.$this->db->quote($eName);
     if(isset($baffle)and $baffle !='') $w[]='baffle='.$this->db->quote($baffle);
     if(isset($eSize)and $eSize !='') $w[]='engnotes='.$this->db->quote($eSize);
    $w[]='fitment LIKE '.$this->db->quote($cv);
    $w[]="fp3filename !=''";

    $w = 'WHERE ' . implode(' AND ', $w);
    
 
  // return $this->db->rows("SELECT fp3Num from `Fp3guideCan` $w");
   // return $this->db->rows("SELECT id as mapID, concat(family,'\n', CONCAT(pipename,' ',`header`,'\n'), baffle,'\n', concat(engnotes, ' ',IF(af='VO2','w/High Flow air filter',''))) as mDescription from `Fp3guideCan` $w");

     //to concatnate pipename and header
    // CONCAT(pipename, IF(header !=' ',' W/',''), header) as eName

      return $this->db->rows("SELECT id as mapID, family as mModel, CONCAT(pipemfr, '\n', family,'\n', CONCAT(pipename,' ',`header`,'\n'), baffle,'\n', concat(engnotes, ' ',IF(af='VO2','w/High Flow air filter','')), CONCAT(' \nMap id:', fp3filename))as mDescription, engnotes as eSize,pipemfr as eManufacturer,baffle as eBaffleType, pipename as eName, ' ' AS name  from `Fp3guideCan` $w");

    
  }

  //returns the ecmFirmware because the app is not sending
 public function getECM($vin){
return $this->db->col("SELECT ecmFirmware from `Customers` where vin='$vin'");

 }

   //check if new customer or not
 public function getVin($params){
  $vin=$params['bVin'];
$customer= $this->db->col("SELECT customerID from `Customers` where vin='$vin'");
if(!$customer) return "NEW";
// else return 'NEW';

 }
  
public  function getMapInfos($type, $params)
  {
    $table='';
              switch ($type) {
                case 'ex':
                $table='pipename';
                break;
                case 'eman':
                $table='pipemfr';
                break;

                case 'bf':
                $table='baffle';
                break;

                case 'eng':
                $table='engnotes';
                break;

                default:
                break;
            }

   $cv=substr($params['cVin'],4,2);
    $cv="%$cv%";
    $yr=substr($params['bYear'],2);
    if(isset($params['bModel']) && $params['bModel']!=''){
      $bm=$params['bModel'];
      $w[]='family='.$this->db->quote($bm);
}

    $em=$params['eManufacturer'];
    $eName=$params['eName'];
    $baffle=$params['eBaffleType'];
    $eSize=$params['eSize'];
 

     if(isset($em)and $em !='') $w[]="pipemfr=".$this->db->quote($em);
     if(isset($eName)and $eName !='') $w[]="pipename=".$this->db->quote($eName);
     if(isset($baffle)and $baffle !='') $w[]="baffle=".$this->db->quote($baffle);
     if(isset($eSize)and $eSize !='') $w[]="engnotes=".$this->db->quote($eSize);

     
    $w[]="year=".$this->db->quote($yr);
    $w[]="fitment LIKE ".$this->db->quote($cv);


    $w = "WHERE " . implode(" AND ", $w);


return $this->db->cols("SELECT distinct($table) from `Fp3guideCan` $w");

   //return $this->db->cols("SELECT distinct(pipemfr) from `Fp3guideCan` where family=? and year=? and fitment LIKE ?" , array($bm,$yr,$cv));
 
  }
//work in progress
 public  function getStockMap($params)
  {
   $vin=$params['bVin'];

    // if ($type=='vin')
   return $this->db->row("SELECT m.`year`, m.`mapID` from Maps m  INNER JOIN Customers c on c.`customerID`=m.`customerID` where c.`vin`= '$vin' and (m.`isCustomerMap`=1 or m.isOriginalMap=1) LIMIT 0,1");
   // return $this->db->row("SELECT m.`year`, m.`mapID` from Maps m  INNER JOIN Customers c on c.`customerID`=m.`customerID` where c.`vin`= '$vin' and IF(m.isOriginalMap=1, m.isOriginalMap=1, m.`isCustomerMap`=1) LIMIT 0,1");
    // if ($type=='year') return $this->db->cols("SELECT m.`year` from Maps m  INNER JOIN Customers c on c.`customerID`=m.`customerID` where c.`vin`= '$vin' and m.`isOriginalMap`=1");  
//SLOW
    // return $this->db->row("SELECT `year`,`mapID` from Maps where SUBSTRING_INDEX(`name`, '_', -1)='$vin'  AND (isOriginalMap=1 or isCustomerMap=1) LIMIT 0,1");
    // if ($type=='year') return $this->db->col("SELECT `year` from Maps where `name` LIKE '%$vin%' LIMIT 0,1");
    // return FALSE;
  }

  

}

 ?>