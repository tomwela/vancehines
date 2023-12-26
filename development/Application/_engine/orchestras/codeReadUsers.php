<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCodeReadUsers extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\CodeReadUsers');
  }


 public function checkUserExists($params,$flag=false){
       $u= $params['uname'];
        $p=$params['pass'];

  if($flag=='HASEYTA'){

    $login= $this->db->col("SELECT count(`users_id`) FROM CodeReadUsers WHERE email=?  ",array($u));

  }
   else $login= $this->db->col("SELECT count(`users_id`) FROM CodeReadUsers WHERE email=? AND password=?  ",array($u,$p));
  
   if($login==0) return 'failure';


 }

public function userID($table,$params)
{
  if(isset($params['uname'] )) {
    $u=$params['uname'];
    return $this->db->col("SELECT `users_id` FROM $table WHERE email=? ",array($u));
 }

  if(isset($params['vinid'])) { 
    $v=$params['vinid'];
    return $this->db->col("SELECT `users_id` FROM $table WHERE vin_id=? ",array($v));
}

}


public function userName($params)
{
  if(isset($params['uname'])) $u=$params['uname'];
  if(isset($params['vinid'])) $v=$params['vinid'];

  if(isset($params['vinid'])) $name= $this->db->row("SELECT cu.firstname,cu.lastname FROM CodeReadVin cv LEFT JOIN CodeReadUsers cu on cu.users_id=cv.users_id WHERE vin_id=?  ",array($v));

 else $name= $this->db->row("SELECT `firstname`,`lastname` FROM CodeReadUsers WHERE email=? ",array($u));

 $fn=$name["firstname"];
 $ln=$name["lastname"];

  $fn=strtolower($fn);
   $ln=strtolower($ln);
   $fn=ucwords($fn);
   $ln=ucwords($ln);
 if(!$fn) $fn= "";
 if(!$ln) $ln= "";
  $arr=array($fn,$ln);
  return $arr;
}


 public function newUser($params){
       $u= $params['uname'];
        $p=$params['pass'];
        $v=$params['vin'];
        $m=$params['macad'];
        $fn=$params['fname'];
        $fn=mysql_real_escape_string($fn)
        $ln=$params['lname'];
        $ln=mysql_real_escape_string($ln)

$email= $this->db->col("SELECT `email` FROM CodeReadUsers WHERE email=? ",array($u));
if($email) return "failure";
//if email not found in db means new user and is added

   else if(isset($u) and isset($p)){
    $p=md5($p);
    $this->db->rows("INSERT INTO CodeReadUsers (`firstname`,`lastname`,`email`,`password`) VALUES (?,?,?,?)",array($fn,$ln,$u,$p));
}

  // return lastID();


 }
public function getAllCodes($vinID){
return $this->db->rows("SELECT c.code, LEFT(c.created,LOCATE(' ',c.created) - 1) as `date`, SUBSTRING_INDEX(c.created,' ', -1) as `time`, d.short as shortDescr, d.description,d.video as videoUrl FROM `CodeHistory` AS c left join dtc as d on c.code=d.code WHERE vin_id=?  ORDER BY c.created DESC",array($vinID));
}

public function getVinAndMacAd($params,$type='false'){

  $uid=$this->userID('CodeReadUsers',$params);

//CHECK IF THIS IS USED
 if(isset($params['vinid'])){
  $vinID=$params['vinid'];
  $uid=$this->db->col("SELECT `users_id` FROM CodeReadVin WHERE vin_id=? ",array($vinID));
}

 

// return $this->db->rows("SELECT vin,macaddress FROM CodeReadVin WHERE email=? ",array($m));
 if($type=='getmac')return $this->db->cols("SELECT distinct(macaddress) FROM `CodeReadVin`  WHERE users_id=? ",array($uid));

  if($type=='model')return $this->db->col("SELECT model FROM `VinDesignator`  WHERE vin=?  ",array($params));
   if($type=='vinID')return $this->db->col("SELECT vin_id FROM `CodeReadVin`   WHERE vin=?  ",array($params));

 return $this->db->cols("SELECT c.vin FROM CodeReadVin as c WHERE macaddress=? and users_id=? ",array($type,$uid));
}
public function vinId($vin,$macad){
 return $this->db->col("SELECT vin_id FROM `CodeReadVin`  WHERE vin=? and macaddress=? ",array($vin,$macad));

  }

 public function vinMacAd($params){
//Add vin when registering
       $u= $params['uname'];
         $p=$params['pass'];
        $v=$params['vin'];
        $m=$params['macad'];
       
        //getUsr id should return error if doesnt exist;
 $users_id= $this->userID("CodeReadUsers",$params);   


$checkVin= $this->db->col("SELECT count(`users_id`) FROM CodeReadVin WHERE macaddress=? ",array($m));
if($checkVin > 0)  return 1;

if($checkVin==0) $this->db->rows("INSERT INTO CodeReadVin (users_id,`vin`, `macaddress`) VALUES (?,?,?)",array($users_id,$v,$m));
 


 }

public function setPassResetCode($params,$rand){
    $m=$params['uname'];

  $this->db->col("UPDATE CodeReadUsers SET `passwd_reset`=? WHERE email=? ",array($rand,$m));
  }

public function checkResetCode($params){

    $c=$params['code'];
   $sel= $this->db->col("SELECT `passwd_reset` FROM CodeReadUsers WHERE passwd_reset=? ",array($c));
   if($sel) return 'match';
  }

public function resetPass($params){
   $p= $params['pass'];
   $u=$params['uname'];
   $c=$params['code'];
    
  $this->db->col("UPDATE CodeReadUsers SET `password`=?,passwd_reset=NULL WHERE passwd_reset=? ",array($p,$c));
  
}

// //check if code already sent to user if so just let them enter it without app sending email
// public function checkCodeExists($params){
//    $c=$params['code'];
    
//   $code=$this->db->col("SELECT `code` FROM CodeReadUsers WHERE code=? ",array($c));
//   return $code;
//   // if($code) return 'exists';
//   // else return 'none';
  
// }

// public function getYrModel($v){
//   return $this->db->col("SELECT `model`, `year` FROM VinDesignator WHERE vin=? LIMIT 0,1 ",array($v));
// }

public function addMacsAndVins($params){
  if(isset($params['vin']))   $v=$params['vin'];
  if(isset($params['macad'])) $m=$params['macad'];
  if(isset($params['uname'])) $u=$params['uname'];
$countMac=$this->db->col("SELECT count(`vin`) FROM CodeReadVin WHERE vin=? and macaddress=? ",array($v,$m));
 $vinUser=$this->db->col("SELECT users_id FROM CodeReadVin WHERE  macaddress=? AND `status`=?",array($m,'Active'));

$users_id=$this->userID('CodeReadUsers',$params);
if(!$users_id) return 'unkownUsr';
// if($vinUser != $users_id) return 'macMismatch';
if($countMac==0) {
  $this->db->rows("INSERT INTO CodeReadVin (`users_id`,`vin`, `macaddress`) VALUES (?,?,?)",array($users_id,$v,$m));
}



}

public function addCodeHistory($code,$params){

  if(isset($params['vin']))   $v=$params['vin'];
  if(isset($params['macad'])) $m=$params['macad'];
$getVinId=$this->db->col("SELECT vin_id FROM `CodeReadVin`  WHERE vin=? and macaddress=?",array($v,$m));

if($code!='') $this->db->rows("INSERT INTO CodeHistory (`vin_id`,`code`) VALUES (?,?)",array($getVinId,$code));

}


//THIS METHOD IS NOT BEING USED FOR NOW
public function addVinToMacAd($params){
   $v= $params['vin'];
   $u=$params['uname'];
   $m=$params['macad'];



$users_id=$this->userID('CodeReadUsers',$params);

 if(!$users_id) return 0;//'uknown user

//*************NEW ///////////

   $checkIFmacUsed=$this->db->row("SELECT users_id, macaddress FROM CodeReadVin WHERE macaddress=? ",array($m));
 // var_dump($checkIFmacUsed);

    if($checkIFmacUsed['users_id']!=$users_id and  $checkIFmacUsed['macaddress']==$m) return 1;//'mac already used by other user';


      $countMac=$this->db->col("SELECT count(`vin`) FROM CodeReadVin WHERE macaddress=? ",array($m));

    // $checkIFvinUsed=$this->db->col("SELECT count(vin) FROM CodeReadVin WHERE vin=? and users_id=? ",array($v,$users_id));
  
    // if($checkIFvinUsed >= 1) return 2;//"vin exists
    if($countMac < 4) {
      $tt=$this->db->rows("INSERT INTO CodeReadVin (`users_id`,`vin`, `macaddress`) VALUES (?,?,?)",array($users_id,$v,$m));

$insertedID= $this->db->col("SELECT vin_id FROM CodeReadVin WHERE macaddress=? and vin=? ",array($m,$v));

    //**********************************

//*************RETURN VIN ID*************************


      return array(3,$insertedID); //'new bike added';

    }
   else if($countMac >=4) return 4; //'already 4 vins associated with the macaddress';

   }

public function bikeYear($vin){

      $vinspl=str_split($vin);
$bYear=$vinspl[9]; //D
      $bYear=(string)$bYear;
      $bYear=strtoupper($bYear); 
      $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020,);
$bYear= $arrYearVin[$bYear];
return $bYear;
}


public function yearAndModelByVin($vin){
  $yr=$this->bikeYear($vin);
  $vin=substr($vin, 4,3);
  $md= $this->db->col("SELECT model  FROM `VinDesignator`  WHERE vin=?  ",array($vin));
  $arr=array('Year'=>"$yr",'Model'=>"$md");
  return $arr;
}


public function vinMacAssoc($params,$ip,$type){
  $vin=$params['vin'];
  $mac=$params['macad'];
  $os=$params['os'];

$canORj1850=$this->db->col("SELECT `type`  FROM VinDesignator  WHERE  vin=? LIMIT 0,1",array($vinJC));
  $c= $this->db->col("SELECT count(*)  FROM `CmacVin`  WHERE  macaddress=?  ",array($mac));
if($c==0) {
  $this->db->rows("INSERT INTO CmacVin (`vin`, `macaddress`,`ip`,os,`type`) VALUES (?,?,?,?,?)",array($vin,$mac,$ip,$os,$type));
  return 1;
}
return 0;
  
}

public function unlinkVin($params){
  $v=$params['vinid'];

  $this->db->col("UPDATE CodeReadVin SET `status`='Inactive' WHERE vin_id=? ",array($v));
}

public function unlinkMac($params){
  $m=$params['macad'];
$this->db->col("UPDATE CodeReadVin SET `status`='Inactive' WHERE macaddress=? ",array($m));
}


}

?>