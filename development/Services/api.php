<?php

namespace API;


use ClickBlocks\Core;
use ClickBlocks\DB;
use ClickBlocks\MVC;

require_once __DIR__ . '/../Application/connect.php';

/**
 * Description of API
 *
 * @author Vladimir Jankovenko <jankovenko@clickbrand.com>
 */

final class API
{
    private $debug_oop = 1;  //1=on or 0=off

    private $params   = array();
    private $host     = null;
    private $output   = null;
    private $zip      = null;
    private $config   = null;
    private $cache    = null;
    private $cacheExpire = null;

    const XML_HEADER = '<?xml version="1.0" encoding="utf-8" ?>';


    private $errors = array(
      'ERR_METHOD_REQUIRED'  => array(1 => 'Method required'),
      'ERR_METHOD_INCORRECT' => array(2 => 'Method does not exist'),
      'ERR_UDID_REQUIRED'    => array(3 => 'UDID required'),
      'ERR_UDID_INCORRECT'   => array(4 => 'UDID does not exist'),
      'ERR_UDID_INACTIVE'    => array(5 => 'UDID is inactive'),
      'ERR_SINCE_INCORRECT'  => array(7 => 'since must be integer (unix timestamp)')
    );

    public function __construct()
    {
      $this->config = Core\Register::getInstance()->config;
      $this->host = 'http://' . $_SERVER['HTTP_HOST'];

      if (!$_GET['output'])
        $_GET['output'] = 'json';

      $this->output = $_GET['output'];
      $this->zip = 'zip -j';

      $this->cache = Core\Register::getInstance()->cache;
      $this->cacheExpire = ini_get('session.gc_maxlifetime');

      if ( $this->cacheExpire < CACHE_EXPIRE_DAY ) {
           $this->cacheExpire = CACHE_EXPIRE_DAY;
      }

      $this->mediaDir = Core\IO::dir($this->config->dirs['MEDIA']);
    }

    public function handle(array $params = array())
    {
      // required checks
      if (!isset($params['method']))
        return $this->error('ERR_METHOD_REQUIRED');
      elseif ((!method_exists(new self(), 'api_' . $params['method'])))
        return $this->error('ERR_METHOD_INCORRECT');

      if (isset($params['since']) && !!is_int($params['since']))
        return $this->error('ERR_SINCE_INCORRECT');

      $this->params = $params;
      return $this->{'api_' . $params['method']}();
    }

    private function error($msg)
    {
      $this->output = 'json';
      if ($this->errors[$msg])
        $error = each($this->errors[$msg]);
      else
        $error = array('unknown', $msg);
      return $this->send(array('status' => 'error', 'errorCode' => $error[0], 'errorMessage' => $error[1]), 'error');
    //    return $this->send(self::XML_HEADER . '<error><errorcode>' . $error[0] . '</errorcode><message><![CDATA[' . $error[1] . ']]></message></error>', 'error');
    }

    /**
    * Author: Ruben Leon
    * Date:   09/2014
    * Helps troubleshoot issues by logging output to the file system
    */
    public function logger_oop($fname, $fdata)
    {
      $logPath = Core\IO::dir($this->config->dirs['log']);
      $logFile = $logPath ."/". $fname;

      $lsm = fopen($logFile, "a");
      fwrite($lsm, date("m/d/Y h:i:s A") . ' ');
      fwrite($lsm, "\n". json_encode($fdata). "\n\n");
      fclose ($lsm);
    }

 private function api_OTAWBUpdate()
    {
        //  firmware=2.8.3
        //  app=2.8.4
        //  os=Android
        //  hardware=3.0.3
        //  vin (optional)

        $params = $this->params;

        if (!$params['firmware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing firmware parameter.' ), 'api_OTAWBUpdate');

        } elseif (!$params['app']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing app parameter.' ), 'api_OTAWBUpdate');

        } elseif (!$params['os']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing os parameter.' ), 'api_OTAWBUpdate');

        } elseif (!$params['hardware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing hardware parameter.' ), 'api_OTAWBUpdate');

        }

        // if vin is set and NOT empty, use it's value, else set it to NULL
        $vin = ( isset($params['vin']) && ! empty($params['vin']) ) ? trim($params['vin']) : NULL;

        $userFirmware = str_replace('.', '', $params['firmware']);
        settype($userFirmware, "integer");

        $userApp      = $params['app'];
        $userOS       = $params['os'];
        $userHardware = $params['hardware'];


        //custom firmware check
        $update = false;
        if ( !is_null($vin) ) {
            $customFirmware = foo(new DB\OrchestraOta())->getCustomWBFirmwareData($userOS, $userApp, $userHardware,
                $userFirmware, $vin);

            // if the vin is not found in the database, the result will be:
            // an array who's first element is null:   $customFirmware[0] = NULL;
            // if the vin does exist, $customFirmware['url'] will have a value
            if ( isset($customFirmware['url']) ) {
                $update = true;

                return $this->send(array( 'status' => 'success', 'message' => $customFirmware['url'] ), 'api_OTAWBUpdate');
            }

        }

        $normalUpdate = foo(new DB\OrchestraOta())->getWBFirmwareData($userOS, $userApp, $userHardware, $userFirmware);
        if ( isset($normalUpdate['url']) ) {
            $update = true;

            return $this->send(array( 'status' => 'success', 'message' => $normalUpdate['url'] ), 'api_OTAWBUpdate');
        }

        if ( !$update ) {
            return $this->send(array( 'status' => 'none', 'message' => 'No update available.' ), 'api_OTAWBUpdate');
        }

    }
//"" YOSEPH
//may 2018

private function api_crmaintain(){
  $params=$this->params;

if(!$params['type'])return $this->send(array('status' => 'error', 'message' => 'type is required.'), 'api_crmaintain');
if(!$params['date'])return $this->send(array('status' => 'error', 'message' => 'date is required.'), 'api_crmaintain');
if(!$params['bikeid'])return $this->send(array('status' => 'error', 'message' => 'bikeid is required.'), 'api_crmaintain');

  $infoId=foo(new \ClickBlocks\DB\OrchestraCRmaintenance())->bikeDataPut($params);
  return $this->send(array('status' => 'success', 'message' =>  "new info added", 'data' =>  $infoId), 'api_crmaintain');
}  

//"" Y
  //MAY 2018

private function api_deleteMaint(){
  $params=$this->params;

if(!$params['id'])return $this->send(array('status' => 'error', 'message' => 'id is required.'), 'api_deleteMaint');

  $deleteInfo= foo(new \ClickBlocks\DB\OrchestraCRmaintenance())->deleteInfo($params);

  if($deleteInfo=='0') return $this->send(array('status' => 'error', 'message' => 'Uknown id.'), 'api_maintAll');
  return $this->send(array('status' => 'success', 'message' =>  "info removed"), 'api_deleteMaint');
}

//"" Y
  //MAY 2018
private function api_maintAll(){
  $params=$this->params;

if(!$params['bikeid'])return $this->send(array('status' => 'error', 'message' => 'bikeid is required.'), 'api_maintAll');

  $allInfo=foo(new \ClickBlocks\DB\OrchestraCRmaintenance())->getInfo($params);

  if($allInfo==0) return $this->send(array('status' => 'error', 'message' => 'Uknown bikeid.'), 'api_maintAll');
  return $this->send(array('status' => 'success', 'message' =>'', "data"=>  $allInfo), 'api_maintAll');
}


//"" Y
  //MAY 2018
private function api_maintAll2(){
  $params=$this->params;

if(!$params['bikeid'])return $this->send(array('status' => 'error', 'message' => 'bikeid is required.'), 'api_maintAll');

  $allInfo=foo(new \ClickBlocks\DB\OrchestraCRmaintenance())->getInfo($params);

  if($allInfo==0) return $this->send(array('status' => 'error', 'message' => 'Uknown bikeid.'), 'api_maintAll');
  return $this->send(array('status' => 'success', 'message' =>  $allInfo), 'api_maintAll');
}

//"" y
//aug 2018

  private function api_fuel() {
   
  $params = $this->params;

    $id= foo(new \ClickBlocks\DB\OrchestraCrfuel())->fuelID($params['vinid']);
     $calDistance = foo(new DB\ServiceCrfuel())->getByID($id);
      if($params['action']=='add'){
      $fuel = foo(new DB\ServiceCrfuel())->getByID();
      $fuel->vin_id          = $params['vinid'];
      $fuel->galnum   = $params['galnum'];
      if(!$id) {
        $fuel->distance   =  0;
        $fuel->mpg=0;
      }
      else {
        if($params['odometer'] >= $calDistance->odm){
        $fuel->distance=$params['odometer'] - $calDistance->odm ;
        }
        else return $this->send(array('status' => 'error', 'message' => array('description'=>'odometer error')), 'fuel');
      }
      $fuel->odm   = $params['odometer'];

      if($fuel->distance!=0) $fuel->mpg   = round($fuel->distance/$fuel->galnum) ;

      $fuel->created   = $params['timestamp'];
      $fuel->save();
      }

      if($params['action']=='delete'){
 
foo(new \ClickBlocks\DB\OrchestraCrfuel())->deleteRow($params['vinid']);
     }


$fuelinfo=foo(new \ClickBlocks\DB\OrchestraCrfuel())->fuelInfo($params['vinid']);
for ($i=0; $i < count($fuelinfo) ; $i++) { 
  $fuelinfo[$i]['odm']=(int)$fuelinfo[$i]['odm'];
  $fuelinfo[$i]['distance']=(int)$fuelinfo[$i]['distance'];
  $fuelinfo[$i]['galnum']=(int)$fuelinfo[$i]['galnum'];
  $fuelinfo[$i]['mpg']=(int)$fuelinfo[$i]['mpg'];
}


return $this->send(array( 'status' => 'success', 'message' => $fuelinfo ), 'api_fuel');
    
    }
    

    //"" YOSEPH
//APRIL 2018
//used to inset purchase info into the db
  // data comes from vhfp3.com/prototype/stripe/index.php
private function api_purchaseInfo(){

$params=$this->params;


$d=foo(new \ClickBlocks\DB\OrchestraOrders())->codeReadOrders($params);
return $this->send(array('status' => 'success', 'message' => array('status'=>'success', 'message'=>"card accepted")), 'purchaseInfo'); 
}


private function api_codeReadRegister(){
  $params=$this->params;

 $d=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->newUser($params);

 if(!$params['uname']) return $this->send(array('status' => 'error', 'message' => 'email is required'), 'codeReadRegister');
 if(!$params['pass']) return $this->send(array('status' => 'error', 'message' => 'password is required'), 'codeReadRegister');
 // if(!$params['name']) return $this->send(array('status' => 'error', 'message' => 'name is required'), 'codeReadRegister');
 $params['pass']=md5( $params['pass']);

  if($d=='failure') return $this->send(array('status' => 'error', 'message' => 'email already in db'), 'codeReadRegister');


     if($params['vin'] && $params['macad']){
         //IOS does not separate macaddress by colon
  if(strlen($params['macad'])==12) $params['macad']=addColonToMac($params['macad']);
      $addV=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->vinMacAd($params);
    if($addV==1) return $this->send(array('status' => 'success', 'message' => 'Macaddress used by another, user added'), 'codeReadRegister');
           }

      return $this->send(array('status' => 'success', 'message' => array('newUser' => $params['uname'] )), 'codeReadRegister'); 
   

}

//"" Y
//April 2018
private function api_sendResetMail(){

   $params = $this->params;
   if(!$params['uname']) return $this->send(array('status' => 'error', 'message' => 'email required'), 'sendResetMail');

$email=$params['uname'];

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  return $this->send(array('status' => 'error', 'message' => 'invalid email format'), 'sendResetMail');
    }

      
   //retrives id from db; $userId can also be used to by services to retrive value
   // $userId= foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userID('CodeReadUsers',$params);
 
 $d=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->newUser($params);
    //if email exists in the db
    if($d!='failure') return $this->send(array('status' => 'error', 'message' => 'email not found'), 'sendResetMail');
   $rand=rand(100000,999999);

           foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->setPassResetCode($params,$rand);

        
$to = $params['uname'];
$subject = "Code:READ password reset";
$msg='Click the link below (or copy and paste to your browser) to change your Code:READ password. <p></p> ';
$msg.="<a href='http://dev.vhfp3.com/prototype/passreset/index.php?c=".$rand."'>http://dev.vhfp3.com/prototype/passreset/index.php?c=$rand </a>";

    $headers  = "From: Code\:READ < noreply@vanceandhines.com\r\n >\n";
    // $headers .= "Cc: testsite < mail@testsite.com >\n"; 
    $headers .= "X-Sender: Code\:READ <noreply@vanceandhines.com\r\n >\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
    $headers .= "X-Priority: 1\n"; // Urgent message!
    $headers .= "Return-Path: s""@vanceandhines.com\r\n"; // Return path for errors
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    mail($to,$subject,$msg,$headers);
  

        return $this->send(array('status' => 'success', 'message' => array('resetCode' => "reset code sent to ".$params['uname'])), 'sendResetMail'); 
 
  }
    //Author "" Yoseph
//October 2018
  private function api_bikeTypeFromVin(){
    $params=$this->params;

      
    $data=foo(new \ClickBlocks\DB\OrchestraVinDesignator())->bikeType();

   return $this->send(array('status' => 'success', 'message' => $data), 'bikeTypeFromVin');
  }   

  //Author "" Yoseph
//October 2018
  private function api_ServiceInfo(){
    $params=$this->params;

    if(!$params['vin']) return $this->send(array('status' => 'error', 'message' => 'vin is required'), 'ServiceInfo');


    
    $data=foo(new \ClickBlocks\DB\OrchestraVinDesignator())->bikeType();

   return $this->send(array('status' => 'success', 'message' => $data), 'bikeTypeFromVin');
  }

  //Author "" Yoseph
//April 2018

   private function api_TaxShipping()
    { 
return $this->send(array('status' => 'success', 'message' => array('tax' => "9.50", 'shipping'=>"6.00")), 'TaxRate');
    }


      //Author "" Yoseph
//April 2018

// private function api_historyAdd()
//     {
// $params=$this->params;
// $email=$params['uname'];
// $vinID=$params['vinID'];
// if(!$params['action'])return $this->send(array('status' => 'error', 'message' => 'action is required.'), 'historyAdd');

// if(!$params['code'])return $this->send(array('status' => 'error', 'message' => 'code is required.'), 'historyAdd');
// // var_dump($params);
// // var_dump($params);

// $codesArr= explode(',',$params['code']);
// $macad=$params['macad'];
// $vinID=$params['vinid'];


// if($params['action']=='add'){


// $vin=$params['vin'];
//   // if(!$vin) return $this->send(array('status' => 'error', 'message' => 'vin is required'), 'addBike');
// if(!$params['macad']) return $this->send(array('status' => 'error', 'message' => 'macaddress is required'), 'addBike');

// if(!$params['uname']) return $this->send(array('status' => 'error', 'message' => 'email is required'), 'addBike');
// $yrModel=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->yearAndModelByVin($vin);

// $newBike=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->addVinToMacAd($params);

// switch ($newBike) {
//    case 0:
//     $msg="unknown user or macaddress";
//     break;
//   case 1:
//     $msg="macaddress already used by other user";
//     break;
//   // case 2:
//   //   $msg="vin already in a database";
//   //   break; 
//   case is_array($newBike):
//     $newVinID=$newBike[1];
//     $yrModel['vinID']=$newVinID; //VinID IS PUSHED TO THE ARRAY CONTAINING YEAR AND MODEL
//     break;  
  
//   case 4:
//     $msg="already 4 vins associated with the macaddress";
//     break; 
    
  
//   default:
//     # code...
//     break;
// }

// if($newBike==0 or $newBike==1 or $newBike==4) return $this->send(array('status' => 'error', 'message' => $msg), 'addBike');
// }





// for ($i=0; $i < count($codesArr); $i++) { 
//  $e=foo(new \ClickBlocks\DB\OrchestraCodeHistory())->addCodeHistory($codesArr[$i],$params);
// }

// if($e=='error' && $params['action']!='add') return $this->send(array('status' => 'error', 'message' => 'vinID doesn\'t exist'), 'historyAdd');

// if($params['action']=='delete') {
//   foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->unlinkVin($params);
// }

// if($params['action']=='update') {
//   foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->unlinkMac($params);
// }





// $distinctMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,"getmac");

//    $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020);
  
//   $userID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userID('CodeReadUsers',$params);

// $usrName=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userName($params);
// $fn=$usrName[0];
// $ln=$usrName[1];

// $arrAll=array();

// for ($i=0; $i < count($distinctMac) ; $i++) { 
//   $tk=array();
//   $macName= $distinctMac[$i];
//   $associateVinMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,$macName);
//   // $arrAll[$macName]=$associateVinMac;
// for ($k=0; $k < count($associateVinMac) ; $k++) { 

//  $vin=$associateVinMac[$k];
//       $vinspl=str_split($vin);
//       $bYear=$vinspl[9]; //D
//       $bYear=(string)$bYear;
//       $bYear= $arrYearVin[$bYear];


//   $vinID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($associateVinMac[$k],"vinID");
//   $model=substr($associateVinMac[$k],4,3);

//   $model=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($model,"model");
//   $tsk=array('macAddrVinId'=>$vinID,'model'=>$model, 'vin'=>$associateVinMac[$k], 'year'=>"$bYear");
// array_push($tk,$tsk);

// }

//   $arrG=array('macID'=>$macName, 'vins'=>$tk);
//   array_push($arrAll,$arrG);


// }


//  return $this->send(array('status' => 'success', 'message' => array('email' => $email,'firstName'=>$fn,'lastName'=>$ln,'userID'=>$userID ,'macAddrs'=>$arrAll )), 'codeReadLogin');
//  }

// AUTHOR "" ""
// Nov 2018
//makes vins inactive for coderead
   private function api_vinUnlink(){
      $params=$this->params;
      if(!$params['vinid']) return $this->send(array('status' => 'error', 'message' => 'vinId is required'), 'vinUnlink');
      $suc=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->unlinkVin($params);
      if($suc=="error")  return $this->send(array('status' => 'error', 'message' => "Unknown bike"), 'vinUnlink'); 
    return $this->send(array('status' => 'success', 'message' => "Bike deleted"), 'vinUnlink'); 

    }



//AUTHOR "" ""
//APRIL 2018

//The same api ad codeReadLogin but it basically gets the info without password
//used when refresshing a page


    private function api_vinInfo(){
      $params=$this->params;

if(!$params['vin']) return $this->send(array('status' => 'error', 'message' => 'vin is required'), 'vinInfo');
if(strlen($params['vin'])!= 17) return $this->send(array('status' => 'error', 'message' => 'vin length should be 17'), 'vinInfo');
//vin is 5HD4LE2C4DC421566
      $vin=$params['vin'];
      $vinPart=substr($vin,4,3);//get LE2
      $vinspl=str_split($vin);
      $bYear=$vinspl[9]; //D
      $bYear=(string)$bYear;
      $bYear=strtoupper($bYear); 
       $legend=$vinspl[6];
      $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020,);
$bYear= $arrYearVin[$bYear];



$id=foo(new \ClickBlocks\DB\OrchestraVinDesignator())->vinID($vinPart,$bYear); //GET THEIR ID AND AND FETCH EACH USING SERVICES
if(!$id) return $this->send(array('status' => 'error', 'message' => 'no information available'), 'vinInfo');
$vinCols=foo(new DB\ServiceVinDesignator())->getByID($id);
// $vinCols->model='Dyna'; $vinCols->type='CAN';
$engineType=foo(new \ClickBlocks\DB\OrchestraVinDesignator())->bikeEngine($legend,$vinCols->model, $vinCols->type);

$country=substr($vin,0,3);
switch ($country) {
  case 'MEG':
    $manuCountry='India';
    break;
    case '932':
    $manuCountry='Brazil';
    break;

    case '5HD':
    $manuCountry='International';
    break;
  
  default:
    $manuCountry='United States';
    break;
}

$weightClass=$vinspl[3];

switch ($weightClass) {
  case 1:
    $weightClass='Heavyweight';
    break;
    case 4:
    $weightClass='Lightweight';
    break;

    case 8:
    $weightClass='Sidecar';
    break;
  
  default:
    $weightClass='Unknown';
    break;
}


       
       return $this->send(array('status' => 'success', 'message' => array( 'Model' => $vinCols->model, 'Year' => $vinCols->year,'ManufacturedFor'=>$manuCountry, 'WeightClass'=>$weightClass,   'Series' => $vinCols->model_description,'EngineDisplacement'=>$engineType, 'BikeType' => $vinCols->type)), 'vinInfo');
    }

private function api_noteHoliday()
    {

//TURN HOLIDAY NOTIFACTION ON DURING THE SPECIFIED TIME 

      $start=strtotime("2018-12-06"); //YEAR MONTH DAY
      $end=strtotime("2019-01-02"); //YEAR MONTH DAY
      $now=strtotime(date('Y-m-d'));

      if($now > $start and $end > $now) {
      return $this->send(array('status' => 'success', 'message' => 'Vance & Hines will be closed between Dec. 22, 2018  and Jan. 1, 2019', 'id'=>"1"), 'noteHoliday'); 
      
      }
  
      else return  $this->send(array('status' => 'error', 'message' => 'Note status is off'), 'noteHoliday');


    }

    //test
    //"" Yoseph
private function api_SearchMap2()
    {

    $params = $this->params;

  //   if($params['ecmFirmware'] != 617){
  //     $redirectedLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  // $redirectedLink =preg_replace("/SearchMap2/", 'SearchMap', $redirectedLink);
  // header("Location:$redirectedLink") ;
  // die();

  // }

      if ($params['eBaffleType'])
        $params['baffleType'] = $params['eBaffleType'];

      if($params['eManufacturer'] == "All")
        $params['eManufacturer'] = NULL;

      if($params['eName'] == "All")
        $params['eName'] = NULL;

      if($params['baffleType'] == "All")
        $params['baffleType'] = NULL;

      if($params['eSize'] == "All")
        $params['eSize'] = NULL;

      foreach ($params as $key => $value)
      {
        $params[key] = urldecode($params[$key]);
      }

 // return $this->send(array( 'status' => 'error', 'message' => $params ), 'SearchMap2');

      if ( !isset($params['ecmFirmware']) )
      { // old apps
        $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
      } else {
        $busType = decodeBusType($params['bYear'], $params['cVin'], $params['bModel'], $params['ecmFirmware']);
        if ($busType == 1) {
          $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
        } else {

            $params['cVin'] = substr($params['cVin'],0,15). API_J1850_VERSION;

        }
      }

   
      // CARB masking
      if (substr($params['cVin'],7,1) != "0") {
        $params['cVin'] = substr($params['cVin'], 0, 7) ."3". substr($params['cVin'], 8);
        //echo $params['cVin'] ."<br />";
      }


       $mapCount = foo(new DB\OrchestraFp3guideCan)->getMapCount($params);
     $eManu=foo(new DB\OrchestraFp3guideCan)->getMapInfos("eman",$params);
     $exhaustName=foo(new DB\OrchestraFp3guideCan)->getMapInfos('ex',$params);
     $baffleName=foo(new DB\OrchestraFp3guideCan)->getMapInfos('bf',$params);
     $engineSize=foo(new DB\OrchestraFp3guideCan)->getMapInfos('eng',$params);
     

$arrCount=count($mapCount);
  // if($arrCount <= 15)
        $aMaps = $mapCount;


     //return $this->send(array('status' => 'success', 'message' => $maps), 'SearchMap2');
     return $this->send(array('status' => 'success', 'message' => array('mapsCount' => $arrCount, 'maps' =>$aMaps, 'eManufacturer' => $eManu, 'eName' => $exhaustName,'eBaffleType' => $baffleName, 'eSize' => $engineSize)), 'SearchMap2');

        
    }


 private function api_historyAdd(){

$params=$this->params;
$email=$params['uname'];
$vin=$params['vin'];

if(!$params['action'])return $this->send(array('status' => 'error', 'message' => 'action is required.'), 'historyAdd');
if(!$params['macad'])return $this->send(array('status' => 'error', 'message' => 'macaddress is required.'), 'historyAdd');
if(!$params['vin'])return $this->send(array('status' => 'error', 'message' => 'vin is required.'), 'historyAdd');
if(strlen($params['macad'])==12) $params['macad']=addColonToMac($params['macad']);




$k=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->addMacsAndVins($params);
if($k=='unkownUsr')return $this->send(array('status' => 'error', 'message' => 'Unknown user'), 'historyAdd');


//************************************
$vinspl=str_split($vin);
$bYear=$vinspl[9]; //D
      $bYear=(string)$bYear;
      $bYear=strtoupper($bYear); 
  $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020);
  $bYear= $arrYearVin[$bYear];

$vinPart=substr($vin,4,3);
$newEntry=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->newEntryFromHistory($vinPart,$bYear);
//************************************

$codesArr= explode(',',$params['code']);
for ($i=0; $i < count($codesArr); $i++) { 
 $e=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->addCodeHistory($codesArr[$i],$params);
}

if($k=="newEntry") return $this->send(array('status' => 'success', 'message'=>array( "codes"=>"codes added", "New Entry"=>$newEntry)), 'historyAdd');
return $this->send(array('status' => 'success', 'message' => array( "codes"=>"codes added")), 'historyAdd');
}


private function api_otaCodeRead()
    {



$params = $this->params;

        if (!$params['firmware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing firmware parameter.' ), 'api_otaCodeRead');

        } elseif (!$params['app']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing app parameter.' ), 'api_otaCodeRead');

        } elseif (!$params['os']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing os parameter.' ), 'api_otaCodeRead');

        } elseif (!$params['hardware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing hardware parameter.' ), 'api_otaCodeRead');

        }

        // if vin is set and NOT empty, use it's value, else set it to NULL
        $vin = ( isset($params['vin']) && ! empty($params['vin']) ) ? trim($params['vin']) : NULL;

        $userFirmware = str_replace('.', '', $params['firmware']);
        settype($userFirmware, "integer");

        //$userFirmware =  $params['firmware'];
        
        $userApp      = $params['app'];
        $userOS       = $params['os'];
        $userHardware = $params['hardware'];


        // $userApp      = '3.3.0';
        // $userOS       = "iOS";
        // $userHardware = '3.0.3';
        //  $userFirmware ='1.0.0.9';
         //$vin=6;

      //*****************************************************
        //Vin check
        $update = false;
        if ( !is_null($vin) ) {
$customFirmware = foo(new DB\OrchestraOtaCodeRead())->codeReadUpdate($userFirmware, $userHardware,$userApp, $userOS,$vin);

            if ( isset($customFirmware['url']) ) {
                $update = true;

             
return $this->send(array( 'status' => 'success', 'message' => $customFirmware ), 'api_otaCodeRead');
            }

       }
         //*****************************************************

         $normalUpdate = foo(new DB\OrchestraOtaCodeRead())->codeReadUpdate($userFirmware, $userHardware,$userApp, $userOS);
        
        if ( isset($normalUpdate['url']) ) {
            $update = true;

            return $this->send(array( 'status' => 'success', 'message' => $normalUpdate['url'] ), 'api_otaCodeRead');
        }

        if ( !$update ) {
            return $this->send(array( 'status' => 'none', 'message' => 'No update available.' ), 'api_otaCodeRead');
        }

    }





//Author "" Yoseph
//April 2018

 private function api_addBike(){
  $params=$this->params;
$vin=$params['vin'];

//IOS does not separate macaddress by colon
   //IOS does not separate macaddress by colon
   if(strlen($params['macad'])==12) $params['macad']=addColonToMac($params['macad']);

  if(!$vin) return $this->send(array('status' => 'error', 'message' => 'vin is required'), 'addBike');
if(!$params['macad']) return $this->send(array('status' => 'error', 'message' => 'macaddress is required'), 'addBike');

if(!$params['uname']) return $this->send(array('status' => 'error', 'message' => 'email is required'), 'addBike');
$yrModel=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->yearAndModelByVin($vin);

$newBike=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->addVinToMacAd($params);

switch ($newBike) {
   case 0:
    $msg="unknown user or macaddress";
    break;
  case 1:
    $msg="macaddress already used by other user";
    break;
  case 2:
    $msg="vin already in a database";
    break; 
  case is_array($newBike):
    $newVinID=$newBike[1];
    $yrModel['vinID']=$newVinID; //VinID IS PUSHED TO THE ARRAY CONTAINING YEAR AND MODEL
    break;  
  
  case 4:
    $msg="Maximum vins reached";
    break; 
    
  
  default:
    # code...
    break;
}

if($newBike==0 or $newBike==1 or $newBike==2 or $newBike==4) return $this->send(array('status' => 'error', 'message' => $msg), 'addBike');

if(is_array($newBike)) return $this->send(array('status' => 'success', 'message' =>  $yrModel), 'addBike');

  }



//Author "" Yoseph
//March 2018
private function api_vinMacAssociate(){
    $params=$this->params;
if(strlen($params['macad'])==12) $params['macad']=addColonToMac($params['macad']);

    $ip= getrealip();
    if(!$params['vin']) return $this->send(array('status' => 'error', 'message' => 'vin is required'), 'vinMacAssociate');
     if(!$params['macad']) return $this->send(array('status' => 'error', 'message' => "macaddress is required"), 'vinMacAssociate');

$res=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->vinMacAssoc($params,$ip);

  if($res==1)return $this->send(array('status' => 'success', 'message' => array('mac' => "macaddress added  ip is $ip" )), 'vinMacAssociate'); 
  else if($res==0) return $this->send(array('status' => 'error', 'message' => 'macaddress already in db'), 'vinMacAssociate');
}




private function api_macInfo(){
  $params=$this->params;
  $email=$params['uname'];



  if (!$params['uname']) {
            return $this->send(array( 'status' => 'error', 'message' => 'email is required.' ), 'macInfo');
        }

$flag='HASEYTA';
 $checkUser=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->checkUserExists($params,$flag);

$distinctMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,"getmac");
// var_dump( $distinctMac);
   $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020,);
$userID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userID('CodeReadUsers',$params);

$usrName=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userName($params);
$fn=$usrName[0];
$ln=$usrName[1];

$arrAll=array();

for ($i=0; $i < count($distinctMac) ; $i++) { 
  $tk=array();
  $macName= $distinctMac[$i];
  $associateVinMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,$macName);
  // $arrAll[$macName]=$associateVinMac;
for ($k=0; $k < count($associateVinMac) ; $k++) { 

 $vin=$associateVinMac[$k];
      $vinspl=str_split($vin);
      $bYear=$vinspl[9]; //D
      $bYear=(string)$bYear;
      $bYear= $arrYearVin[$bYear];


  $vinID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($associateVinMac[$k],"vinID");
  $model=substr($associateVinMac[$k],4,3);

  $model=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($model,"model");
  $tsk=array('macAddrVinId'=>$vinID,'model'=>$model, 'vin'=>$associateVinMac[$k], 'year'=>"$bYear");
array_push($tk,$tsk);

}

  $arrG=array('macID'=>$macName, 'vins'=>$tk);
  array_push($arrAll,$arrG);


}

if($checkUser!='failure') return $this->send(array('status' => 'success', 'message' => array('logIn' => $email,'firstName'=>$fn,'lastName'=>$ln,'userID'=>$userID ,'macAddrs'=>$arrAll )), 'macInfo');


else return $this->send(array('status' => 'error', 'message' => 'failure'), 'macInfo');

}




//"" Y
//April 2018

private function api_codeReadLogin(){
  $params=$this->params;
  $email=$params['uname'];


  if (!$params['uname']) {
            return $this->send(array( 'status' => 'error', 'message' => 'email is required.' ), 'codeReadLogin');
        }

if ( !$params['pass'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'password is required.' ), 'codeReadLogin');
        }
$params['pass']=md5( $params['pass']);
      
 $checkUser=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->checkUserExists($params);

$distinctMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,"getmac");


   $arrYearVin=array('7'=>2007,'8'=>2008,'9'=>2009,'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,'J'=>2018,'K'=>2019,'L'=>2020,);
$userID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userID('CodeReadUsers',$params);

$usrName=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->userName($params);
$fn=$usrName[0];
$ln=$usrName[1];

$arrAll=array();

for ($i=0; $i < count($distinctMac) ; $i++) { 
  $tk=array();
  $macName= $distinctMac[$i];
  $associateVinMac=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($params,$macName);

for ($k=0; $k < count($associateVinMac) ; $k++) { 

 $vin=$associateVinMac[$k];
 // echo $associateVinMac[$k]."===";
      $vinspl=str_split($vin);
      $bYear=$vinspl[9]; //D
      $bYear=(string)$bYear;
      $bYear= $arrYearVin[$bYear];

  $vinID=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->vinId($associateVinMac[$k],$macName);
  
  $model=substr($associateVinMac[$k],4,3);

  $model=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getVinAndMacAd($model,"model");
  $tsk=array('macAddrVinId'=>$vinID,'model'=>$model, 'vin'=>$associateVinMac[$k], 'year'=>"$bYear");
array_push($tk,$tsk);

}

  $arrG=array('macID'=>$macName, 'vins'=>$tk);
  array_push($arrAll,$arrG);


}


if($checkUser!='failure') return $this->send(array('status' => 'success', 'message' => array('logIn' => $email,'firstName'=>$fn,'lastName'=>$ln,'userID'=>$userID ,'macAddrs'=>$arrAll )), 'codeReadLogin');


else return $this->send(array('status' => 'error', 'message' => 'failure'), 'codeReadLogin');

}

 //author "" Yoseph
    //march 2018
// private function api_allCodes(){
//   $params=$this->params;
// $vin=$params['macAddrVinId'];

//  if(!$params['macAddrVinId']) return $this->send(array('status' => 'error', 'message' => 'vinID is required'), 'allCodes');
// $getCodes=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getAllCodes($vin);

// return $this->send(array('status' => 'success', 'message' => array('codeList' => $getCodes )), 'allCodes'); 
// }



 //author "" Yoseph
    //march 2018
private function api_allCodes(){
  $params=$this->params;
$vin=$params['macAddrVinId'];

 if(!$params['macAddrVinId']) return $this->send(array('status' => 'error', 'message' => 'vinID is required'), 'allCodes');
$getCodes=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getAllCodes($vin);

for ($i=0; $i < count($getCodes); $i++) { 
     $oneVideo=explode(",",$getCodes[$i]['videoUrl']);
 $getCodes[$i]['videoUrl']=$oneVideo[0];

}

return $this->send(array('status' => 'success', 'message' => array('codeList' => $getCodes )), 'allCodes'); 
}

private function api_allCodes2(){
  $params=$this->params;
$vin=$params['macAddrVinId'];

 if(!$params['macAddrVinId']) return $this->send(array('status' => 'error', 'message' => 'vinID is required'), 'allCodes');
$getCodes=foo(new \ClickBlocks\DB\OrchestraCodeReadUsers())->getAllCodes($vin);

for ($i=0; $i < count($getCodes); $i++) { 
     $videos=explode(",",$getCodes[$i]['videoUrl']);
 $getCodes[$i]['videoUrl']=$videos;
 // send empty array [] than an array with with empty string([""])
 if($getCodes[$i]['videoUrl'][0]=="") $getCodes[$i]['videoUrl']=array();

}

return $this->send(array('status' => 'success', 'message' => array('codeList' => $getCodes )), 'allCodes'); 
}


//author "" Yoseph
    //march 2018
   private function api_CodeReadFaq()
    {
      // header("Content-Type: text/html; charset=ISO-8859-1");
      $params=$this->params;
$faq=foo(new \ClickBlocks\DB\OrchestraFaqcoderead())->faq();

return $this->send($faq, 'CodeReadFaq');
}



    private function api_OTAUpdate()
    {
        //  firmware=2.8.3
        //  app=2.8.4
        //  os=Android
        //  hardware=3.0.3
        //  vin (optional)

        $params = $this->params;

        if (!$params['firmware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing firmware parameter.' ), 'OTAUpdate');

        } elseif (!$params['app']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing app parameter.' ), 'OTAUpdate');

        } elseif (!$params['os']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing os parameter.' ), 'OTAUpdate');

        } elseif (!$params['hardware']) {
            return $this->send(array( 'status' => 'error', 'message' => 'Missing hardware parameter.' ), 'OTAUpdate');

        }

        $vin = ( isset($params['vin']) && ! empty($params['vin']) ) ? trim($params['vin']) : NULL;

        $userFirmware = str_replace('.', '', $params['firmware']);
        settype($userFirmware, "integer");

        $userApp      = $params['app'];
        $userOS       = $params['os'];
        $userHardware = $params['hardware'];


        //custom firmware check
        $update = false;
        if ( !is_null($vin) ) {
  $customFirmware = foo(new DB\OrchestraOta())->getCustomFirmwareData($userOS, $userApp, $userHardware, $userFirmware, $vin);
  $slot0Restore = foo(new DB\OrchestraMaps())->slot0Restore($vin);

  if($slot0Restore) {
     $ecm=$params['ecmFirmware'];
     
     $mediaDir= $this->mediaDir;
     $mediaUrl = "dev.vhfp3.com/development/media";
     $yr=$slot0Restore['year'];
     $mapID=$slot0Restore['mapID'];
     $sourceMap      = $mediaDir ."/". $yr ."/". $mapID . ".map";
  

     if(file_exists($sourceMap) && $ecm==$slot0Restore['ecm']) {
 
      $restoreSlot=True;
      $slot0path= "{$mediaUrl}/{$yr}/{$mapID}.map";
    }
     else {
      $restoreSlot=false;
      $slot0path= "";
    }


    }

    else {
      $restoreSlot=false;
      $slot0path= "";
    }

            // if the vin is not found in the database, the result will be:
            // an array who's first element is null:   $customFirmware[0] = NULL;
            // if the vin does exist, $customFirmware['url'] will have a value
            if ( isset($customFirmware['url']) ) {
                $update = true;
     $autoUp=foo(new DB\OrchestraOta())->customAutoTuneFlag($userOS, $userApp, $userHardware, $userFirmware,$vin);
$autoUp=$autoUp=='1' ? True : False;
                return $this->send(array( 'status' => 'success', 'message' => $customFirmware['url'],'autoTuneUpdate'=>$autoUp, 'slot0restore'=>$restoreSlot, 'slot0url'=>$slot0path ), 'OTAUpdate');
            }

        }


        $normalUpdate = foo(new DB\OrchestraOta())->getFirmwareData($userOS, $userApp, $userHardware, $userFirmware);
        if ( isset($normalUpdate['url']) ) {
            $update = true;

// $autoUp=foo(new DB\OrchestraOta())->autoTuneFlag($userOS, $userApp, $userHardware, $userFirmware);
// $autoUp=$autoUp==1 ? True : False;

                return $this->send(array( 'status' => 'success', 'message' => $normalUpdate['url'],'autoTuneUpdate'=>false, 'slot0restore'=>$restoreSlot, 'slot0url'=>$slot0path ), 'OTAUpdate');
            // return $this->send(array( 'status' => 'success', 'message' => $normalUpdate['url'],"shel"=>99), 'OTAUpdate');
        }

        if ( !$update ) {
            return $this->send(array( 'status' => 'none', 'message' => 'No update available.', 'slot0restore'=>$restoreSlot, 'slot0url'=>$slot0path  ), 'OTAUpdate');
        }

    }


    private function api_FlashFP3Unit()
    {  // used by Raspberry Pi device to flash an fp3 unit
      $params = $this->params;
      if (!$params['customerID'])
      {
        return $this->send(array('status' => 'error', 'message' => 'A Customer ID is required.'), 'FlashFP3Unit');
      } else {
          $cID = $params['customerID'];
      }

      $data = foo(new DB\OrchestraCustomers())->getFlashData( $cID );
      if (!is_array($data) || !count($data))
      {
        return $this->send(array('status' => 'error', 'message' => "This customer ID: ${cID} is invalid OR does not have an original map."), 'FlashFP3Unit');
      }
      $data['ota'] = array();
      $ecmFirmware = $data['ecmFirmware'];
      //var_dump($ecmFirmware);

      if( $ecmFirmware >= 240 ){
          //var_dump(">= 240");

          $canInfo = foo(new DB\OrchestraOta())->getCANData();
          //var_dump($canInfo['zip']);

          if (!is_array($canInfo) || !count($canInfo))
          {
              return $this->send(array('status' => 'error', 'message' => $canInfo ), 'FlashFP3Unit');
          }

          $data['ota'] = $canInfo;
          //array_push( $data, $canInfo );
          return $this->send(array('status' => 'success', 'message' => $data), 'FlashFP3Unit');

      } elseif ( $ecmFirmware < 240 ) {
          //var_dump("< 240");

          $j1850Info = foo(new DB\OrchestraOta())->getJ1850Data();
          if (!is_array($j1850Info) || !count($j1850Info))
          {
              return $this->send(array('status' => 'error', 'message' => $j1850Info ), 'FlashFP3Unit');
          }

          $data['ota'] = $j1850Info;
          //array_push( $data, $j1850Info );
          return $this->send(array('status' => 'success', 'message' => $data), 'FlashFP3Unit');
      } else {
        return $this->send(array('status' => 'error', 'message' => 'Invalid ecmFirmware.' ), 'FlashFP3Unit');
      }
    }


   
    /**
    * Author: Ruben Leon
    * Date:   07/2014
    * Parameters & Values
    *          vin:  Customer VIN number (database lookup in Customers Table)
    *    clearNote:  0: just read the note
    *                1: essentially a delete flag;
    * To Test (URLs)
    *  Read Note:   https://www.vhfp3.com/development/Services/api.php?method=SendNote&vin=1HD1KRM12EB709917&clearNote=0
    *  Delete Note: https://www.vhfp3.com/development/Services/api.php?method=SendNote&vin=1HD1KRM12EB709917&clearNote=1
    *
    */
    private function api_SendNote()
    {

        $params = $this->params;
        if ( !$params['vin'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'A VIN Number is required.' ), 'SendNote');
        }

        //nullify the note and noteflag cols
        if ( $params['clearNote'] == 1 ) {
            $data = foo(new \ClickBlocks\DB\OrchestraCustomers())->updateCNoteByVin($params['vin']);
            return $this->send(array( 'status' => 'success', 'message' => array( "note" => NULL, "noteFlag" => "0" ) ), 'SendNote');
        }

        $data = foo(new \ClickBlocks\DB\OrchestraCustomers())->getNoteFlagByVin($params['vin']);

        //to send survey
        // var_dump($data);
        $data["note"]="http://www.vhfp3.com";
        $data["noteFlag"]="1";


        // var_dump($data);
        if ( !is_array($data) || !count($data) ) {
            // Error: This Vin does not exist in our system
            //return $this->send(array('status' => 'error', 'message' => 'This Vin does not exist in our system.'), 'SendNote');
            return $this->send(array( 'status' => 'success', 'message' => array( "note" => "", "noteFlag" => "0" ) ), 'SendNote');
        }

        //success
        return $this->send(array('status' => 'success', 'message' => $data), 'SendNote');

    }

    /* Author: Ruben Leon
     * Date: 04/2015
     * Description:  API to update a User's Contact Info
     *
     * API will first read from the database what information we currently have, present it to the user
     * and allow them to update it or delete it.
     *
     */
    private function api_CustomerContactInfo()
    {
        $params = $this->params;

        if ( !$params['vin'] ) {

            return $this->send(
                array(
                    'status' => 'error',
                    'message' => 'A vin number is required.'
                ),
                'CustomerContactInfo');
        }

        if ( !$params['mode'] ) {

            return $this->send(
                array('status' => 'error',
                      'message' => 'A mode parameter is required.'
                ),
                'CustomerContactInfo');
        }


        switch ($params['mode']) {

            //mode "read":  performs a select query
            //mode "update":  performs an update query

            case "read":
                $customerData = foo(new \ClickBlocks\DB\OrchestraCustomers())->getCustomerContactInfo( $params['vin'] );
                if ( !is_array($customerData) || !count($customerData) ) {
                  return $this->send(array('status' => 'error', 'message' => 'The VIN provided does not exist in our system.'), 'CustomerContactInfo');

                } else {
                  return $this->send(array('status'=>'success', 'message'=> $customerData), 'CustomerContactInfo');

                }
                break;

            case "update":

                foreach ($params as $key => $value) {
                    switch ($key) {
                        case "vin":
                        case "fullName":
                        case "address":
                        case "address2":
                        case "city":
                        case "state":
                        case "zip":
                        case "country":
                        case "phone":
                        case "email":
                        case "contactPreferences":
                            $userData[ $key ] = trim(urldecode($params[ $key ]));
                            break;

                        default:
                            break;
                    }
                }

                //save
                $data = foo(new \ClickBlocks\DB\OrchestraCustomers())->updateCustomerContactInfo( $userData );

                //success
                return $this->send( array('status'=>'success', 'message'=>'Contact Info was Updated!'), 'CustomerContactInfo');
                break;

            default:
              return $this->send(array('status' => 'error', 'message' => 'An unknown value was provided for the mode parameter'), 'CustomerContactInfo');
              break;
        }
    }

    private function api_ShareAMap()
    {
        $mediaDir = $this->mediaDir;

        $params   = $this->params;
        $sendersMapID  = (integer) $params['mapID'];
        $recipientsVin = (string) strtoupper(trim($params['vin']));

        if ( !$sendersMapID ) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "The Sender's mapID parameter is required.",
                ),
                'ShareAMap'
            );
        }

        if ( !$recipientsVin ) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "The Recipient's vin number parameter is required.",
                ),
                'ShareAMap'
            );
        }


        $sendersMapModel = foo(new DB\ServiceMaps())->getByID($sendersMapID);
        if ( !$sendersMapModel->mapID ) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "Could not find the Sender's map in our Database.  map id: {$sendersMapID}",
                ),
                'ShareAMap'
            );
        }

        $sendersCustomerModel = foo(new DB\ServiceCustomers())->getByID($sendersMapModel->customerID);
        if ( !$sendersCustomerModel->customerID ) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "Could not find the Sender's Customer ID in our Database.",
                ), 'ShareAMap'
            );
        }


        $ecmFirm = ($sendersMapModel->ecmFirmware === NULL || $sendersMapModel->ecmFirmware === '') ? $sendersCustomerModel->ecmFirmware : $sendersMapModel->ecmFirmware;

        $mList = array();
        $mList['sender']['vin']            = $sendersCustomerModel->vin;
        $mList['sender']['ecmFirmware']    = $ecmFirm;
        $mList['sender']['mapID']          = $sendersMapModel->mapID;
        $mList['sender']['mapYear']        = $sendersMapModel->year;
        $mList['sender']['mapDescription'] = $sendersMapModel->description;



        $recipientsCustomerID    = foo(new DB\OrchestraCustomers())->getCustomerIDByVin($recipientsVin);
        $recipientsCustomerModel = new DB\Customers($recipientsCustomerID);
        if ( !$recipientsCustomerModel->vin) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "Could not find the Recipient's vin in our Database. vin: {$recipientsVin}",
                ), 'ShareAMap');
        }

        $recipientsMapModel = foo(new DB\ServiceMaps())->getByID( $recipientsCustomerModel->currentMap );

        $mList['recipient']['vin']            = $recipientsCustomerModel->vin;
        $mList['recipient']['ecmFirmware']    = $recipientsCustomerModel->ecmFirmware;
        $mList['recipient']['mapID']          = $recipientsCustomerModel->currentMap;
        $mList['recipient']['mapYear']        = $recipientsCustomerModel->year;
        $mList['recipient']['mapDescription'] = $recipientsMapModel->description;



        // Validation check
        // ECM Firmware must be the same for both Sender and Receiver
        if( $mList['sender']['ecmFirmware'] != $mList['recipient']['ecmFirmware'] ) {
            return $this->send(
                array(
                    'status'  => 'error',
                    'message' => "The Sender's and Receiver's ECU do not match.  Can not share map. ". $mList['sender']['ecmFirmware'] .",".$mList['recipient']['ecmFirmware'] ,
                ),
                'ShareAMap'
            );
        }


        foreach ($mList as $maps => $val) {

            $sourceMap      = $mediaDir ."/". $val['mapYear'] ."/". $val['mapID'] . ".map";
            $temporaryFile  = $mediaDir ."/out/". $val['vin'] ."_temporary.txt";
            $finalMapFile   = $mediaDir ."/out/". $val['vin'] .".txt";

            if (!file_exists($sourceMap)) {
                return $this->send(
                    array(
                        'status'  => 'error',
                        'message' => "Map file: ". $val['mapID'] .".map does not exist.  Can not complete process.",
                    ),
                    'ShareAMap'
                );
            }

            if (file_exists($finalMapFile)) {
                // delete the file if it exists, start fresh
                unlink($finalMapFile);
            }

            copy($sourceMap, $temporaryFile);
            encodeFile( $val['mapID'], $temporaryFile, $val['mapYear'], $val['vin'], $val['ecmFirmware'] );


            //MVC\VanceAndHines::saveNameAndDescriptionInMapFile($temporaryFile, $finalMapFile, TRUE, $val['mapDescription']);
            MVC\VanceAndHines::saveNameAndDescriptionInMapFile($temporaryFile, $finalMapFile, "Shared Map", $val['mapDescription']);

            // cache stuff
            $cachedData = $this->cache->get( $val['vin'] );
            $cachedData['maps']['timestamp'] = time();

            $this->cache->delete( $val['vin'] );
            $this->cache->set( $val['vin'], $cachedData, $this->cacheExpire );


            if (file_exists($temporaryFile)) {
                //delete the file if it exists, start fresh
                unlink($temporaryFile);
            }

        } //foreach


        copy( $mediaDir ."/out/". $mList['sender']['vin'] . ".txt",
            $mediaDir ."/out/". $mList['recipient']['vin'] . ".txt"
        );


        // success message
        return $this->send(
            array(
                'status'  => 'success',
                'message' => "Map has been shared",
            ),
            'ShareAMap'
        );

    }

    private function send($data, $pref)
    {
      header('Access-Control-Allow-Origin: *');
      #$id = uniqid($pref . '_');
      $id = $pref;
      $xml = $id . '.xml';
      $zip = $id . '.zip';

      if ($this->output == 'zip')
      {
        // remove old entries
        $cmd = 'find ' . Core\IO::dir('temp') . ' -atime  1 -exec rm -f {} \; ';
        `$cmd`;
        // archive
        file_put_contents(Core\IO::dir('temp') . '/' . $xml, $data);
        $cmd = $this->zip . ' ' . Core\IO::dir('temp') . '/' . $zip . ' ' . Core\IO::dir('temp') . '/' . $xml;
        `$cmd`;
        $this->headers(array('filename' => $zip, 'size' => filesize(Core\IO::dir('temp') . '/' . $zip)));
        readfile(Core\IO::dir('temp') . '/' . $zip);
      }
      else
      {
        print_r(json_encode($data));
      }
    }

    private function headers(array $params = array())
    {
      if (ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');
      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Cache-Control: private', false); // required for certain browsers
      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename="' . $params['filename'] . '";');
      header('Content-Transfer-Encoding: binary');
    }

    private function api_SearchMap()
    {
      $params = $this->params;

      if ($params['eBaffleType'])
        $params['baffleType'] = $params['eBaffleType'];

      if($params['eManufacturer'] == "All")
        $params['eManufacturer'] = NULL;

      if($params['eName'] == "All")
        $params['eName'] = NULL;

      if($params['baffleType'] == "All")
        $params['baffleType'] = NULL;

      if($params['eSize'] == "All")
        $params['eSize'] = NULL;

      foreach ($params as $key => $value)
      {
        $params[key] = urldecode($params[$key]);
      }



      if ( !isset($params['ecmFirmware']) )
      { // old apps
        $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
      } else {
        $busType = decodeBusType($params['bYear'], $params['cVin'], $params['bModel'], $params['ecmFirmware']);
        if ($busType == 1) {
          $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
        } else {

            $params['cVin'] = substr($params['cVin'],0,15). API_J1850_VERSION;

        }
      }


      // CARB masking
      if (substr($params['cVin'],7,1) != "0") {
        $params['cVin'] = substr($params['cVin'], 0, 7) ."3". substr($params['cVin'], 8);
        //echo $params['cVin'] ."<br />";
      }
      //echo $params['cVin'] ."<br />";



      $maps = foo(new DB\OrchestraMaps)->getSearchMap($params);
      $e = $params['eManufacturer'];
      $x = $params['eName'];
      $b = $params['baffleType'];
      $n = $params['eSize'];

      if ($params['eManufacturer'])
        $eManufacturerOptions = $params['eManufacturer'];
      else
        $eManufacturerOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.eManufacturer', null, $x, $b, $n, 'eManufacturer',$params);
      if($eManufacturerOptions)
      {
        foreach ((array)$eManufacturerOptions as $value) {
          $eManufacturerOption[] = $value;
        }
      }
      else
      {
        $eManufacturerOption = $eManufacturerOptions;
      }
      if ($params['eName'])
        $eNameOptions = $params['eName'];
      else
        $eNameOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.eName', $e, null, $b, $n, 'eName',$params);
      if($eNameOptions)
      {
        foreach ((array)$eNameOptions as $value) {
          $eNameOption[] = $value;
        }
      }
      else
      {
        $eNameOption = $eNameOptions;
      }
      sort($eNameOption);
      if ($params['baffleType'])
        $eBaffleTypeOptions = $params['baffleType'];
      else
        $eBaffleTypeOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.baffleType', $e, $x, null, $n, 'baffleType',$params);
      if($eBaffleTypeOptions)
      {
        foreach ((array)$eBaffleTypeOptions as $value) {
          $eBaffleTypeOption[] = $value;
        }
      }
      else
      {
        $eBaffleTypeOption = $eBaffleTypeOptions;
      }
      if ($params['eSize'])
        $eSizeOptions = $params['eSize'];
      else
        $eSizeOptions  = foo(new DB\OrchestraMaps)->getDDBOptions('m.eSize', $e, $x, $b, null, 'eSize',$params);
      if($eSizeOptions)
      {
        foreach ((array)$eSizeOptions as $value) {
          $eSizeOption[] = $value;
        }
      }
      else
      {
        $eSizeOption = $eSizeOptions;
      }
      $arrCount = count($maps);
      if($arrCount <= 15)
        $aMaps = $maps;

      return $this->send(array('status' => 'success', 'message' => array('mapsCount' => $arrCount, 'maps' => $aMaps, 'eManufacturer' => $eManufacturerOption, 'eName' => $eNameOption,'eBaffleType' => $eBaffleTypeOption, 'eSize' => $eSizeOption)), 'SearchMap');
    }

//"" Yoseph
//dec 2018
 private function api_SearchMapDetail()
    {
      $params = $this->params;

      if ($params['eBaffleType'])
        $params['baffleType'] = $params['eBaffleType'];

      if($params['eManufacturer'] == "All")
        $params['eManufacturer'] = NULL;

      if($params['eName'] == "All")
        $params['eName'] = NULL;

      if($params['baffleType'] == "All")
        $params['baffleType'] = NULL;

      if($params['eSize'] == "All")
        $params['eSize'] = NULL;

      foreach ($params as $key => $value)
      {
        $params[key] = urldecode($params[$key]);
      }



      if ( !isset($params['ecmFirmware']) )
      { // old apps
        $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
      } else {
        $busType = decodeBusType($params['bYear'], $params['cVin'], $params['bModel'], $params['ecmFirmware']);
        if ($busType == 1) {
          $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
        } else {

            $params['cVin'] = substr($params['cVin'],0,15). API_J1850_VERSION;

        }
      }


      // CARB masking
      if (substr($params['cVin'],7,1) != "0") {
        $params['cVin'] = substr($params['cVin'], 0, 7) ."3". substr($params['cVin'], 8);
        //echo $params['cVin'] ."<br />";
      }
      //echo $params['cVin'] ."<br />";



      $maps = foo(new DB\OrchestraMaps)->getSearchMap($params);
      $e = $params['eManufacturer'];
      $x = $params['eName'];
      $b = $params['baffleType'];
      $n = $params['eSize'];

      if ($params['eManufacturer'])
        $eManufacturerOptions = $params['eManufacturer'];
      else
        $eManufacturerOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.eManufacturer', null, $x, $b, $n, 'eManufacturer',$params);
      if($eManufacturerOptions)
      {
        foreach ((array)$eManufacturerOptions as $value) {
          $eManufacturerOption[] = $value;
        }
      }
      else
      {
        $eManufacturerOption = $eManufacturerOptions;
      }
      if ($params['eName'])
        $eNameOptions = $params['eName'];
      else
        $eNameOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.eName', $e, null, $b, $n, 'eName',$params);
      if($eNameOptions)
      {
        foreach ((array)$eNameOptions as $value) {
          $eNameOption[] = $value;
        }
      }
      else
      {
        $eNameOption = $eNameOptions;
      }
      sort($eNameOption);
      if ($params['baffleType'])
        $eBaffleTypeOptions = $params['baffleType'];
      else
        $eBaffleTypeOptions = foo(new DB\OrchestraMaps)->getDDBOptions('m.baffleType', $e, $x, null, $n, 'baffleType',$params);
      if($eBaffleTypeOptions)
      {
        foreach ((array)$eBaffleTypeOptions as $value) {
          $eBaffleTypeOption[] = $value;
        }
      }
      else
      {
        $eBaffleTypeOption = $eBaffleTypeOptions;
      }
      if ($params['eSize'])
        $eSizeOptions = $params['eSize'];
      else
        $eSizeOptions  = foo(new DB\OrchestraMaps)->getDDBOptions('m.eSize', $e, $x, $b, null, 'eSize',$params);
      if($eSizeOptions)
      {
        foreach ((array)$eSizeOptions as $value) {
          $eSizeOption[] = $value;
        }
      }
      else
      {
        $eSizeOption = $eSizeOptions;
      }
      $arrCount = count($maps);
      // if($arrCount <= 15)
       $aMaps = $maps;

      return $this->send(array('status' => 'success', 'message' => array('mapsCount' => $arrCount, 'maps' => $aMaps, 'eManufacturer' => $eManufacturerOption, 'eName' => $eNameOption,'eBaffleType' => $eBaffleTypeOption, 'eSize' => $eSizeOption)), 'SearchMap');
    }



 //serachmapdetail for dynamically gernerated maps
 private function api_SearchMapDetail2()
    {

    $params = $this->params;

  //   if($params['ecmFirmware'] != 617){
  //     $redirectedLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  // $redirectedLink =preg_replace("/SearchMap2/", 'SearchMap', $redirectedLink);
  // header("Location:$redirectedLink") ;
  // die();

  // }

      if ($params['eBaffleType'])
        $params['baffleType'] = $params['eBaffleType'];

      if($params['eManufacturer'] == "All")
        $params['eManufacturer'] = NULL;

      if($params['eName'] == "All")
        $params['eName'] = NULL;

      if($params['baffleType'] == "All")
        $params['baffleType'] = NULL;

      if($params['eSize'] == "All")
        $params['eSize'] = NULL;

      foreach ($params as $key => $value)
      {
        $params[key] = urldecode($params[$key]);
      }

 // return $this->send(array( 'status' => 'error', 'message' => $params ), 'SearchMap2');

      if ( !isset($params['ecmFirmware']) )
      { // old apps
        $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
      } else {
        $busType = decodeBusType($params['bYear'], $params['cVin'], $params['bModel'], $params['ecmFirmware']);
        if ($busType == 1) {
          $params['cVin'] = substr($params['cVin'],0,15). API_VERSION;
        } else {

            $params['cVin'] = substr($params['cVin'],0,15). API_J1850_VERSION;

        }
      }

   
      // CARB masking
      if (substr($params['cVin'],7,1) != "0") {
        $params['cVin'] = substr($params['cVin'], 0, 7) ."3". substr($params['cVin'], 8);
        //echo $params['cVin'] ."<br />";
      }


       $mapCount = foo(new DB\OrchestraFp3guideCan)->getMapCount($params);
       echo " $mapsCounttest ";
     $eManu=foo(new DB\OrchestraFp3guideCan)->getMapInfos("eman",$params);
     $exhaustName=foo(new DB\OrchestraFp3guideCan)->getMapInfos('ex',$params);
     $baffleName=foo(new DB\OrchestraFp3guideCan)->getMapInfos('bf',$params);
     $engineSize=foo(new DB\OrchestraFp3guideCan)->getMapInfos('eng',$params);
     

$arrCount=count($mapCount);
  // if($arrCount <= 15)
        $aMaps = $mapCount;


     //return $this->send(array('status' => 'success', 'message' => $maps), 'SearchMap2');
     return $this->send(array('status' => 'success', 'message' => array('mapsCount' => $arrCount, 'maps' =>$aMaps, 'eManufacturer' => $eManu, 'eName' => $exhaustName,'eBaffleType' => $baffleName, 'eSize' => $engineSize)), 'SearchMap2');

        
    }   



    private function api_GetMap2()
    {



//       error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 'On');
// echo phpinfo();
      

        $params = $this->params;


          $mediaDir = $this->mediaDir;



        if (!$params['mapID']) {
            return $this->send(array('status' => 'error', 'message' => 'mapID is required.'), 'GetMap2');
        }

        $id=$params['mapID'];

        $map = foo(new DB\ServiceFp3guideCan())->getByID($id);
      
      // if (!$map->fp3filename) {

      //  return $this->send(array('status' => 'error', 'message' => 'fp3 mapID does not exist.'), 'GetMap2');
      // }

       
        if (!$map->id) {
            return $this->send(array('status' => 'error', 'message' => 'map with this mapID does not exist.'), 'GetMap2');
        }

     $customer=foo(new \ClickBlocks\DB\OrchestraFp3guideCan())->getVin($params);
 // echo is_null($customer) ? "NOT NEW" : "NEW\n";

     //Grab year and mapID from maps table
 if($customer!="NEW"){
     $stockMapID=foo(new \ClickBlocks\DB\OrchestraFp3guideCan())->getStockMap($params);
     $bikeYearVal=$stockMapID['year'];
     $stockMap=$stockMapID['mapID'];
    
     }
    

    
     

  $v=$params['bVin'];

  $tempMap=$mediaDir . "/$bikeYearVal/" . $stockMap . ".map"; //Get stock map
if($stockMap and file_exists($tempMap)) {
$tempMapC= $mediaDir . "/out/" . $v . "_stk.bin";
  copy($tempMap, $tempMapC);
// curl_close($ch);
}
 else {
  // if new bike and if no stock map found
  $tempMap=$mediaDir ."/out/zero.bin";
}

 
$tempMap2=$mediaDir . "/out/" . $v . ".bin"; //temp file to be flushed


$mapLink="http://dev.vhfp3.com/development/media/out/" . $v . ".bin";
 
$logMapErr="http://dev.vhfp3.com/development/media/out/log/".$v.".log";

      $f = fopen($tempMapC, "rb"); //read map
      $fw =fopen($tempMap2,"wb");//create new map 
      $readMap=fread($f,256*96);//read stock map or default zero map
      $sectors=unpack('C*',$readMap); 



         //set image directory
$imgDir="http://vhfp3.com/development/media/photos/";
$iname=$map->partnum; //find the name of the image from db
$image=explode("/",$iname); //image name looks lie 20432 / 23989
 $imgOpt1=trim($image[0]);
if(isset($image[1])) $imgOpt2=trim($image[1]);
if(isset($image[2]))$imgOpt3=trim($image[2]);
 
//check if one of the images exist in the production media folder
if (@fopen("$imgDir"."$imgOpt1.jpg","r")) 
    $imgUrl="$imgDir"."$imgOpt1.jpg";
elseif (@fopen("$imgDir"."$imgOpt2.jpg","r"))
    $imgUrl="$imgDir"."$imgOpt2.jpg";
  elseif (@fopen("$imgDir"."$imgOpt3.jpg","r"))
    $imgUrl="$imgDir"."$imgOpt3.jpg";
else $imgUrl=$imgDir.'no_img_available.jpg';
        
$airFlow=$map->af=='VO2' ? 'High Flow ' :  $map->af;


$ecm=$params['ecmFirmware']; 

//db PVVGuide
$pvvIdNum=foo(new \ClickBlocks\DB\OrchestraPVVGuide())->pvvID($params['ecmFirmware']);

$pvvInfo = foo(new DB\ServicePVVGuide())->getByID($pvvIdNum);

$yr=strlen($map->year) > 1 ? "20".$map->year : "200".$map->year; // 9 is 2009 and 10 is 2010
$res = array(
  //JUST FOR INFO BELOW CAN BE REMOVED FOR PROD.
'mapURL'        => $mapLink,
'mPhotoUrl'     => $imgUrl,
'mModel'        => $map->family,
'mDescription'  => "eManufacturer :$map->pipemfr
eName : $map->pipename
eBaffleType : $map->baffle
eSize : $map->engnotes 

Exhaust Mfr: $map->pipemfr
Exhaust: $map->pipename
Baffle: $map->baffle 

Year: $yr
Family: $map->family

Engine: $map->engnotes
Cam: $mapLink ecm is $ecm
Engine Mod:Stock $bikeYearVal
Air Filter: $id $airFlow
pvv: $map->fp3Num
Map Id: $map->fp3filename",
            'eManufacturer' => $map->pipemfr,
            'eName'         => $map->pipename,
            'eBaffleType'   => $map->baffle,
            'eSize'         => $map->engnotes
        );


//table names as put in the db
 $tables=array('VE_MAP_based_Front_Cyl','VE_MAP_based_Rear_Cyl','VE_TPS_based_Front_Cyl','VE_TPS_based_Rear_Cyl','Air_Fuel_Ratio_Lambda','Air_Fuel_Ratio_Stoich','Acceleration_Enrichment','Deceleration_Enrichment','Spark_Advance_Front_Cyl','Spark_Advance_Rear_Cyl','Idle_RPM','Engine_Displacement','RPM_Limit','RPM_Limit2');
  $mapStructure = getMapStructure($pvvInfo->ecmFirmware);

  

 foreach ($tables as $n) {

  if($pvvInfo->$n ==1 ) {

  // echo "<br/>*************************<br/> $n <br/>****************************<br/>";
 switch ($n) {
   case 'VE_MAP_based_Front_Cyl':
     $msn='VEFrontCyl';
     $pvvXMLname='VE (MAP based/Front Cyl)';
     break;

      case 'VE_MAP_based_Rear_Cyl':
     $msn='VERearCyl';
     $pvvXMLname='VE (MAP based/Rear Cyl)';
     break;
       case 'VE_TPS_based_Front_Cyl': 
     $msn='VEFrontCyl';
     $pvvXMLname='VE (TPS based/Front Cyl)';
     break;
       case 'VE_TPS_based_Rear_Cyl':
     $msn='VERearCyl';
     $pvvXMLname='VE (TPS based/Rear Cyl)';
     break;

       case 'Air_Fuel_Ratio_Lambda':
     //$msn='PEAir-FuelRatioLambda';
     $msn='AFRatio';
     // $pvvXMLname='Air-Fuel Ratio (Lambda)';
      $pvvXMLname='Air-Fuel Ratio (Lambda)'; //Question pvv db and pvvFile misMatch
     break;

       case 'Air_Fuel_Ratio_Stoich':
     $msn='AFRatio';
     $pvvXMLname='Air-Fuel Ratio (Stoich)';
     break;

       case 'Acceleration_Enrichment':
     $msn='AccelerationEnrichment';
     $pvvXMLname='Acceleration Enrichment';
     break;
       case 'Deceleration_Enrichment':
     $msn='DecelEnleanment';
     $pvvXMLname='Deceleration Enleanment';
     break;
       case 'Spark_Advance_Front_Cyl':
     $msn='SparkAdvanceFrontCyl';
     $pvvXMLname='Spark Advance (Front Cyl)';
     break;
       case 'Spark_Advance_Rear_Cyl':
     $msn='SparkAdvanceRearCyl';
     $pvvXMLname='Spark Advance (Rear Cyl)';
     break;
       case 'Idle_RPM':
     $msn='IdleRPM';
     $pvvXMLname='Idle RPM';
     break; 
         case 'Engine_Displacement':
     $msn='EngineDisplacement';
     $pvvXMLname='Engine Displacement';
     break;

  

   }
 
 if($map->fp3Num !="") { //Check if you have to delete the lines with empty fp3Num value in the db


  $xmlFile="http://dev.vhfp3.com/development/media/pvv/".$map->fp3Num.".xml";

  if(!@fopen($xmlFile,'r')) {
    return $this->send(array('status' => 'error', 'message' => 'pvv not found'), 'GetMap2');
  }

$xmlLoad=simplexml_load_file($xmlFile);
$nameID=getPvvID($pvvXMLname,$xmlLoad);

//patch for 822 H-D that crushes because pvv is Air-Fuel Ratio (Stoich) instead of LAMBDA
if(($nameID=='' or $nameID==NULL) and ($msn=="AFRatio" and $ecm!=205)) {
$pvvXMLname='Air-Fuel Ratio (Stoich)';
  $nameID=getPvvID($pvvXMLname,$xmlLoad);
  $n="Air_Fuel_Ratio_Stoich";
 
}

else if($nameID=='' or $nameID==NULL)  {
//205 error : http://dev.vhfp3.com/development/Services/api.php?method=GetMap2&bVin=1HD4LE230HC40_205&mapID=3239&ecmFirmware=205
  //pvv guide shows tps based map but pvvFile is map based for vefront and verear; so provide tps based pvv
if($ecm=='205') {
  if($msn=="VEFrontCyl") $pvvXMLname='VE (TPS based/Front Cyl)';
  if($msn=="VERearCyl") $pvvXMLname='VE (TPS based/Rear Cyl)';
$xmlFile="http://dev.vhfp3.com/development/media/pvv/FP3-J0127.xml";
 $xmlLoad=simplexml_load_file($xmlFile);
  $nameID=getPvvID($pvvXMLname,$xmlLoad);
}

}

 
 //**********************************************************************
$strMsn=$mapStructure[$msn];

$dataType=$strMsn['dataType'];
      $cols=$strMsn['cols'];
      $rows=$strMsn['rows'];
      $sectorOffset=$strMsn['sectorOffset'];
      $offset=$strMsn['offset'];
      $dataSize=$strMsn['dataSize'];
      $factor=$strMsn['factor'];
      $subtractor=$strMsn['subtractor'];
      $divider=$strMsn['divider'];
      $convmode=$strMsn['convmode'];
  $width=$strMsn['width'];

$srtHeader=$strMsn['headers'];



if(isset($srtHeader['X']['convmode'])) $Xconvmode=$srtHeader['X']['convmode'];
if(isset($srtHeader['X']['divider'])) $Xdivider=$srtHeader['X']['divider'];
if(isset($srtHeader['X']['factor'])) $Xfactor=$srtHeader['X']['factor'];
if(isset($srtHeader['X']['subtractor'])) $Xsubtractor=$srtHeader['X']['subtractor'];

if(isset($srtHeader['Y']['convmode'])) $Yconvmode=$srtHeader['Y']['convmode'];
if(isset($srtHeader['Y']['divider'])) $Ydivider=$srtHeader['Y']['divider'];
if(isset($srtHeader['Y']['factor'])) $Yfactor=$srtHeader['Y']['factor'];
if(isset($srtHeader['Y']['subtractor'])) $Ysubtractor=$srtHeader['Y']['subtractor'];

$sigdig=$strMsn['sigdig'];
if($dataType=='table') $tableVals=$strMsn['table']['titles']['values'];

if(isset($srtHeader['X']['sectorOffset'])) $XsectorOffset=$srtHeader['X']['sectorOffset'];
if(isset($srtHeader['X']['offset'])) $Xoffset=$srtHeader['X']['offset'];

if(isset($srtHeader['Y']['sectorOffset'])) $YsectorOffset=$srtHeader['Y']['sectorOffset'];
if(isset($srtHeader['Y']['offset'])) $Yoffset=$srtHeader['Y']['offset'];

if(isset($srtHeader['X']['offset'])) $XmaxCount=$srtHeader['X']['maxCount'];
else if(isset($strMsn['rows']))$XmaxCount=$rows;
if(isset($srtHeader['Y']['offset'])) $YmaxCount=$srtHeader['Y']['maxCount'];
else if(isset($strMsn['cols'])) $YmaxCount=$cols;

if(isset($strMsn['min'])) $min=$strMsn['min'];
if(isset($strMsn['max'])) $max=$strMsn['max'];


 
 
  
if($dataType=='matrix'){

// $countPVVx=count($xmlLoad->Item[$nameID]->Columns->Col);
$xHeaderCol=$xmlLoad->Item[$nameID]->Columns->Col;
// echo "################ TEST MISMATCH #########################";


$xPvvCount=count($xHeaderCol);
$xPos=256 * $XsectorOffset + $Xoffset +1;

// #################IF THE xHeader count is larger (usually 17) than pvv (usually 15) ########################;
if($xPvvCount < $XmaxCount){//IF*A
 
if ($msn=="VEFrontCyl" or $msn=="VERearCyl"){
 
$diff=$XmaxCount - $xPvvCount;
$diffPos=$xPos + $xPvvCount ;
$pvvDiffPos= $xPvvCount - 1 ;

$Xval= $xHeaderCol[$pvvDiffPos]->attributes();

$Xval=(int)$Xval;

$Xval = conv($Xval,$Xconvmode,$Xsubtractor,$Xdivider,$Xfactor);



for ($i=0; $i <= $diff; $i++) { 
 
  $sectors[$diffPos++]=$Xval;
  
}


  }

}//END IF*A
 

  ##################################################################


//get the min of pvvCount or Xmaxcount ...otherwise $sectors[$xPos++] will be messed up 
 $displayMinCellsX= $XmaxCount <= $xPvvCount ? $XmaxCount : $xPvvCount;



for ($i=0; $i < $displayMinCellsX; $i++) { 

 $Xval= $xHeaderCol[$i]->attributes();

 $Xval=(int)$Xval;
 
  $Xval = conv($Xval,$Xconvmode,$Xsubtractor,$Xdivider,$Xfactor);


switch ($Xconvmode) {
case  1:
$msb = ($Xval >> 8) & 0x00FF;
$lsb = $Xval & 0x00FF;
$sectors[$xPos++]=$msb;
$sectors[$xPos++]=$lsb;
break;

// case 2:
// $bpack=2;
// $msb = $sectors[$xPos];
// $lsb = $sectors[$xPos + 1];
// $sectors[$xPos++] = $msb - ($lsb * 256);
// break;
case 2:
$sectors[$xPos++]=$Xval==256 ? $Xval - 1 : $Xval;
$xPos++;
break;

case 3:
$msb = $sectors[$xPos];
$lsb = $sectors[$xPos + 1];
$sectors[$xPos++] = $msb - ($lsb * 100);
break;

case 4:
$msb = $sectors[$xPos];
$lsb = $sectors[$xPos + 1];
$sectors[$xPos++] = $msb - ($lsb * 255);
break;

case  6:
$sectors[$xPos++]=$Xval;
$xPos++;
break;


case 5:
case  7:
$sectors[$xPos++]=$Xval;

break;
case 8:
$msb = $sectors[$xPos];
$lsb = $sectors[$xPos + 1];
$sectors[$xPos++] = ($msb * 256.0) + $lsb;
        break;


default:
# code...
break;
}

// }
// else{
//   $logF=date('M d-y :h.i.s');
// $logF.= " -> $xPvvCount and $XmaxCount mismatch of xHeaders count for $n \n";
// $myfile = file_put_contents($logMapErr, $logF.PHP_EOL , FILE_APPEND | LOCK_EX);
// }

}

####################################################################################""
$yHeaderCol=$xmlLoad->Item[$nameID]->Rows->Row;

####################################################################################""

//WRITE THE YHEADER
$yPvvCount=count($yHeaderCol);

$yPos=(256 * $YsectorOffset) + $Yoffset +1 ;
$displayMinCellsY= $YmaxCount <= $yPvvCount ? $YmaxCount : $yPvvCount;


// for ($i=0; $i < count($xmlLoad->Item[$nameID]->Rows->Row); $i++) { 
 for ($i=0; $i < $displayMinCellsY; $i++) { 
 $Yval= $xmlLoad->Item[$nameID]->Rows->Row[$i]->attributes();

 $Yval = conv($Yval,$Yconvmode,$Ysubtractor,$Ydivider,$Yfactor);

switch ($Yconvmode) {
case  1:
$msb = ($Yval >> 8) & 0x00FF;
$lsb = $Yval & 0x00FF;
$sectors[$yPos++]=$msb;
$sectors[$yPos++]=$lsb;
break;

case 2:
$bpack=2;
$msb = $sectors[$yPos];
$lsb = $sectors[$yPos + 1];
$sectors[$yPos++] = $msb - ($lsb * 256);
break;

case 3:
$msb = $sectors[$yPos];
$lsb = $sectors[$yPos + 1];
$sectors[$yPos++] = $msb - ($lsb * 100);
break;

case 4:
$msb = $sectors[$yPos];
$lsb = $sectors[$yPos + 1];
$sectors[$yPos++] = $msb - ($lsb * 255);
break;

case  6:
$sectors[$yPos++]=$Yval;
$yPos++;
break;

case 5:
case  7:
$sectors[$yPos++]=$Yval;
break;
case 8:
$msb = $sectors[$yPos];
$lsb = $sectors[$yPos + 1];
$sectors[$yPos++] = ($msb * 256.0) + $lsb;
        break;


default:
# code...
break;
} 

           }//for



  
   if($yPvvCount < $YmaxCount){
    //if there there are more rows than columns, fill the last one
    if($msn=="VEFrontCyl" or $msn=="VERearCyl"){
      $diff=$YmaxCount - $yPvvCount;
     
      $diffPos=$yPos + $yPvvCount ;
 $msb=$sectors[$yPos-2];
 $lsb=$sectors[$yPos-1];

       // echo "<p/>diffdiff". $sectors[$yPos-2] ."diff<p/>";
      // $pvvDiffPos= $yPvvCount - 1 ;
  for ($b=0; $b < $diff ; $b++) { 
            $sectors[$yPos++]=$msb;
            $sectors[$yPos++]=$lsb;


          }        
   }
   }


$bPos=(256 * $sectorOffset) + $offset +1;
$xCellCount=count($xmlLoad->Item[$nameID]->Rows->Row);
for ($i=0; $i < $xCellCount; $i++) { 
  // for ($j=0; $j < count($xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell); $j++) { 
  for ($j=0; $j < $displayMinCellsX; $j++) { 
    $rawval= $xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell[$j]->attributes();
        $rawval=(double)$rawval;

        //added nameID because for 822 it is stoich not lambda
        if($n=='Air_Fuel_Ratio_Lambda') $rawval=$rawval * 14.7; 
      
       $bval = conv($rawval ,$convmode,$subtractor,$divider,$factor);
       // if($msn=="VEFrontCyl") echo "<p/> $bval  at the postion is $delPos <p/>";

switch ($convmode) {
case  1:
$msb = ($bval >> 8) & 0x00FF;
$lsb = $bval & 0x00FF;
$sectors[$bPos++]=$msb;
$sectors[$bPos++]=$lsb;
break;

case 2:
$bpack=2;
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 256);
break;

case 3:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 100);
break;

case 4:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 255);       
break;

case  6:
$sectors[$bPos++]=$bval;
$bPos++;
break;

case 5:
case  7:
$sectors[$bPos++]=$bval;
break;
case 8:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = ($msb * 256.0) + $lsb;
        break;


default:
# code...
break;


}


}//for



  // continue here
if($xPvvCount < $XmaxCount){

    if($msn=='VEFrontCyl' or $msn=="VERearCyl"){
      $diff= $XmaxCount-$xPvvCount;
  $diffVal=$sectors[$bPos-1];

  for ($d=0; $d < $diff ; $d++) { 
  $sectors[$bPos++]=$diffVal;
  }


}
}


}

          if($yPvvCount < $YmaxCount){
            //patch for 822; more y columns than pvv
            //http://dev.vhfp3.com/development/Services/api.php?method=GetMap2&bVin=1HD4LE230HC40_822&mapID=10080&ecmFirmware=822
             $diff= $YmaxCount - $yPvvCount;
          if($msn=="VEFrontCyl" or $msn=="VERearCyl"){
            for ($i=0; $i < $diff ; $i++) { 
              # code...
           
                 for ($w=0; $w < $XmaxCount; $w++) { 
                   $t= $xmlLoad->Item[$nameID]->Rows->Row[$yPvvCount-1]->Cell[$w]->attributes();
                   $rawval=(double)$t;
                   $bval = conv($rawval ,$convmode,$subtractor,$divider,$factor);
                 
                   $sectors[$bPos++]=$bval;
               }
               }
               }
 }//for

 $logF=date('M d-y :h.i.s');
$logF.= " -> Map created successfully";
$myfile = file_put_contents($logMapErr, $logF.PHP_EOL , FILE_APPEND | LOCK_EX);



}//matrix


//TABLE
if($dataType=='table'){
  $bCount=count($xmlLoad->Item[$nameID]->Rows->Row);
  $bCountCell=count($xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell);
  //patching the cells
$bPos=(256 * $sectorOffset) + $offset +1;
for ($i=0; $i < $bCount; $i++) { 
  for ($j=0; $j < $bCountCell; $j++) { 
    $rawval= $xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell[$j]->attributes();
    $rawval=(double)$rawval;
       $bval = conv($rawval ,$convmode,$subtractor,$divider,$factor);

switch ($convmode) {
case  1:
$msb = ($bval >> 8) & 0x00FF;
$lsb = $bval & 0x00FF;
$sectors[$bPos++]=$msb;
$sectors[$bPos++]=$lsb;
break;

case 2:
$bpack=2;
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 256);
break;

case 3:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 100);
break;

case 4:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 255);
break;

case  6:
$sectors[$bPos++]=$bval;
$bPos++;
break;

case 5:
case  7:
$sectors[$bPos++]=$bval;
break;
case 8:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = ($msb * 256.0) + $lsb;
        break;


default:
# code...
break;
}
  }
 }
}

//FLAT
if($dataType=='flat'){
  //patching the cells
  $bCountFlat=count($xmlLoad->Item[$nameID]->Rows->Row);
  $bCountFlatCell=count($xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell);
$bPos=(256 * $sectorOffset) + $offset +1;
for ($i=0; $i < $bCountFlat; $i++) { 
  for ($j=0; $j < $bCountFlatCell; $j++) { 
    $rawval= $xmlLoad->Item[$nameID]->Rows->Row[$i]->Cell[$j]->attributes();
    //$rawval=(double)$rawval;
    $bval = conv($rawval ,$convmode,$subtractor,$divider,$factor);
  
    
switch ($convmode) {
case  1:
$msb = ($bval >> 8) & 0x00FF;
$lsb = $bval & 0x00FF;
$sectors[$bPos++]=$msb;
$sectors[$bPos++]=$lsb;
break;

case 2:
$bpack=2;
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 256);
break;

case 3:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 100);
break;

case 4:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = $msb - ($lsb * 255);
break;

case  6:
$sectors[$bPos++]=$bval;
$bPos++;
break;

case 5:
case  7:
$sectors[$bPos++]=$bval;
break;
case 8:
$msb = $sectors[$bPos];
$lsb = $sectors[$bPos + 1];
$sectors[$bPos++] = ($msb * 256.0) + $lsb;
        break;


default:
# code...
break;
}



       //$bPos= $bPos +1;
  }
 }
}


}//end if ($map->fp3Num !="")

  }
 }//END FOREACH


//if pairing for the first time
 if(!$stockMap){
  $sectors[4]=2;

if($params['ecmFirmware'] > 255){// asfelagi aymeslegnim
$ecm1 = ($params['ecmFirmware'] >> 8) & 0x00FF;
$ecm2 = $params['ecmFirmware'] & 0x00FF;

if(canORJ1850($params['ecmFirmware'])=='CAN'){
$sectors[17]=$ecm1;
$sectors[18]=$ecm2;
}

else {
  $sectors[9]=$ecm1;
$sectors[10]=$ecm2;
// echo "<br>$ecm1 $ecm2 is ecmfm <br>";

}
}
else  {
if(canORJ1850($params['ecmFirmware'])=='CAN') $sectors[18]= $params['ecmFirmware']; //if less than 256 no need msb and lsb
else $sectors[9]= $params['ecmFirmware'];
}

// //Patch the phycical address
 $se=getSectorPos($params['ecmFirmware']);
if(canORJ1850($params['ecmFirmware'])=='CAN'){

   // echo '<br/> can captured<br/>';
$sectors[5]=hexdec(substr($se[0],0,2));//start index CAN
$sectors[6]=hexdec(substr($se[0],2,2));
$sectors[7]=hexdec(substr($se[0],4,2));
$sectors[8]=hexdec(substr($se[0],6,2));

$sectors[9]=hexdec(substr($se[1],0,2));
$sectors[10]=hexdec(substr($se[1],2,2));
$sectors[11]=hexdec(substr($se[1],4,2));
$sectors[12]=hexdec(substr($se[1],6,2));//end index CAN
}
else{
  // $se=getSectorPos($params['ecmFirmware']);
  // echo '<br/>j1850 captured<br/>';
$sectors[11]=hexdec(substr($se[0],0,2)); //START J1850
$sectors[12]=hexdec(substr($se[0],2,2));
$sectors[13]=hexdec(substr($se[0],4,2));
$sectors[14]=hexdec(substr($se[0],6,2));

$sectors[15]=hexdec(substr($se[1],0,2)); 
$sectors[16]=hexdec(substr($se[1],2,2));
$sectors[17]=hexdec(substr($se[1],4,2));
$sectors[18]=hexdec(substr($se[1],6,2)); //END J1850
}



}


     
//********* FP3 HEADER******* 
$fp3="FP3:";
$codeidIndexPos=canORJ1850($params['ecmFirmware'])=='CAN' ? 21 : 5 ;
// $replaceDash=str_replace('-',':',$map->fp3Num);
$codeidIndexName=str_split($fp3);
for ($i=0; $i < strlen($fp3); $i++) { 
$value = unpack('C*', $codeidIndexName[$i]);
$sectors[$codeidIndexPos++] = $value[1];
     }

//**********FP3 map name index***********
$mapNameIdIndexPos=canORJ1850($params['ecmFirmware'])=='CAN' ? 25 : 19 ;

$mapIdIndexName=str_split($map->fp3filename);

for ($i=0; $i < strlen($map->fp3filename); $i++) { 
$value = unpack('C*', $mapIdIndexName[$i]);
$sectors[$mapNameIdIndexPos++] = $value[1];

      }

//*********sector 95  



$sector95Str="$map->fp3filename\0$map->pipename $map->baffle $map->engnotes\0";
if(strlen($sector95List) > 128){
$sector95Str=str_replace("Standard","Std",$sector95List);
$sector95Str=str_replace("Competition","Comp",$sector95List);
$sector95Str=str_replace("Quiet","Q",$sector95List);
} 
elseif(strlen($sector95List) > 128) $sector95Str=str_replace("Straightshots","ST",$sector95List);
elseif(strlen($sector95List) > 128) $sector95Str=str_replace("Chrome /Black","Chr/Blk",$sector95List);
elseif(strlen($sector95List) > 128) $sector95Str=str_replace("Slip-ons","SO",$sector95List);
elseif(strlen($sector95List) > 128) $sector95Str=str_replace("Catalytic","Cat",$sector95List);
else if(strlen($sector95List) > 128) $sector95Str=str_replace("Standard","Std",$sector95List);
//else if(strlen($sector95List) > 128) echo "String is too big";

$sector95OffPos=(256*95) +1;

$split95=str_split($sector95Str);
// echo "$sector95Str<p>";
// echo count($split95);
$spliCount=count($split95);
for ($i=0; $i < $spliCount; $i++) { 
$value = unpack('C*', $split95[$i]);
$sectors[$sector95OffPos++] = $value[1];

}

//CALCULATE CHECKSUM
if(canORJ1850($params['ecmFirmware'])=='CAN'){
$s1=dechex($sectors[5]);//start index CAN
$s2=dechex($sectors[6]);
$s3=dechex($sectors[7]);
$s4=dechex($sectors[8]);

$e1=dechex($sectors[9]);
$e2=dechex($sectors[10]);
$e3=dechex($sectors[11]);
$e4=dechex($sectors[12]);//end index CAN
}
else{

$s1=dechex($sectors[11]); //START J1850
$s2=dechex($sectors[12]);
$s3=dechex($sectors[13]);
$s4=dechex($sectors[14]);

$e1=dechex($sectors[15]); 
$e2=dechex($sectors[16]);
$e3=dechex($sectors[17]);
$e4=dechex($sectors[18]); //END J1850
}



// TO GET THE RIGHT 010101 MAY BECOME 0111 
$e1=substr($e1,0,1)=="0" ? "0".$e1 : $e1 ;
$e2=substr($e2,0,1)=="0" ? "0".$e2 : $e2 ;
$e3=substr($e3,0,1)=="0" ? "0".$e3 : $e3 ;
$e4=substr($e4,0,1)=="0" ? "0".$e4 : $e4 ;



$s1=substr($s1,0,1)=="0" ? "0".$s1 : $s1 ;
$s2=substr($s2,0,1)=="0" ? "0".$s2 : $s2 ;
$s3=substr($s3,0,1)=="0" ? "0".$s3 : $s3 ;
$s4=substr($s4,0,1)=="0" ? "0".$s4 : $s4 ;



$s=hexdec("$s1$s2$s3$s4");
$e=hexdec("$e1$e2$e3$e4") +1;
//$sectorSize= ($e - $s)/256;
$sectorSize=($e - $s) +1 ;



 
$sum=0;
// $checkSumSize=(256 * $sectorSize)+1;
// $checkSumArray=array();

for ($i=3; $i < $sectorSize; $i++) { 
$sum+=$sectors[$i];
// array_push($checkSumArray,$sectors[$i]);
}

 // $sum=array_sum($checkSumArray);

$checksum = 0 - ($sum & 0xFFFF);
$sectors[1] = ($checksum & 0xFF00) >> 8;
$sectors[2] = ($checksum & 0xFF);


//PATCH CHANGES
$mapSize=24576;
for ($j=1; $j <  $mapSize + 1 ; $j++) { 
$p2=$sectors[$j];
$p.=pack("C*", $p2);

}
fwrite($fw, $p);


 fclose($fw); 
 fclose($f); 


      return $this->send(array('status' => 'success', 'message' =>$res), 'GetMap2');

    }


    private function api_GetMap()
    {
        $mediaDir = $this->mediaDir;
   
        $params = $this->params;

        if (!$params['mapID']) {
            return $this->send(array('status' => 'error', 'message' => 'mapID is required.'), 'GetMap');
        }

        $map = foo(new DB\ServiceMaps())->getByID($params['mapID']);
        if (!$map->mapID) {
            return $this->send(array('status' => 'error', 'message' => 'map with this mapID does not exist.'), 'GetMap');
        }


        $templateMapFile = $mediaDir . "/vh/" . $map->apiVersion . "/" . $map->mapID . ".map";

  
        $desMapFile = $mediaDir . "/out/map" . $map->mapID . ".txt";
        copy($templateMapFile, $desMapFile);

        $mapUrl = $_SERVER['HTTP_HOST'] . str_replace('../', '', Core\IO::url($this->config->dirs['MEDIA'])) . '/out/map' . $map->mapID . ".txt";

        $res = array(
            'mapURL'        => $mapUrl,
            'mPhotoUrl'     => $map->photoUrl,
            'mModel'        => $map->model,
            'mDescription'  => $map->description,
            'eManufacturer' => $map->eManufacturer,
            'eName'         => $map->eName,
            'eBaffleType'   => $map->baffleType,
            'eSize'         => $map->eSize
        );
        return $this->send(array('status' => 'success', 'message' => $res), 'GetMap');
    }

    private function api_LiveData()
    {
      $params = $this->params;
      if (!$params['vin'])
      {
        return $this->send(array('status' => 'error', 'message' => 'vin is required.'), 'LiveData');
      }
      $cachedData = $this->cache->get($params['vin']);
      if(!$cachedData['LiveData'])
      {
        return $this->send(array('status' => 'success', 'message' => array('isContinue' => 0)), 'LiveData');
      }
      else
      {
        $isContinue = 1;
        $fields = array('BattVoltage' , 'TargetAFR' , 'FrontAFR' , 'RearAFR' , 'ManifoldPressure' , 'ThrottlePosition' , 'ManifoldAirTemperature' , 'EngineRPM' , 'WheelSpeedition' , 'Front02SensorVoltage' , 'Rear02SensorVoltage' , 'FrontSparkTiming' , 'RearSparkTiming' , 'FrontAdaptiveFuel' , 'RearAdaptiveFuel');
        foreach($fields as $field)
        {
          $data[$field] = $params[$field];
        }
        $cachedData['LiveDataParams'] = $data;
        $this->cache->set($params['vin'], $cachedData, $this->cacheExpire);
        return $this->send(array('status' => 'success', 'message' => array('isContinue' => 1)), 'LiveData');
      }
    }

    private function api_LiveDataPing()
    {
        // called by the apps when doing a Customer Support->Download
        $params = $this->params;
        if (!$params['vin']) {
            return $this->send(array('status' => 'error', 'message' => 'vin is required.'), 'LiveDataPing');
        }

        $cachedData = $this->cache->get($params['vin']);
        if ($cachedData['maps'])
            $isPushedMap = array(
                'timestamp' => $cachedData['maps']['timestamp'],
                'mapUrl' => $_SERVER['HTTP_HOST'] . str_replace('../', '', Core\IO::url($this->config->dirs['MEDIA'])) . '/out/' . $params['vin'] . '.txt');
        else
            $isPushedMap = array(
                'timestamp' => 'None',
                'mapUrl'    => 'None'
            );
        if (!$cachedData['LiveData']) {
            return $this->send(array('status' => 'success', 'message' => array('isWaitingLiveData' => 0, 'map' => $isPushedMap)), 'LiveDataPing');
        } else {
            return $this->send(array('status' => 'success', 'message' => array('isWaitingLiveData' => 1, 'map' => $isPushedMap)), 'LiveDataPing');
        }
    }



//Author "" Y
//June 2017

// private function api_CamSettings()
//     { 
//       //takes either 'can' or 'j1850' params
//      $protocol=$this->params;
//       $data = foo(new \ClickBlocks\DB\OrchestraCamSettings())->getKeys($protocol['type']);
//       if (!is_array($data) || !count($data))
//       {
//         return $this->send(array('status' => 'error', 'message' => 'No Data found.'), 'CamSettings');
//       }
//        //if($protocol['type']==='can' or $protocol['type']==='j1850' ){
//       return $this->send(array('status' => 'success', 'message' => $data ), 'CamSettings');
//         //}
       
//     }


// "" Y
//MAY 2019
    private function api_CamSettings()
    { 
      //takes either 'can' or 'j1850' params
     $params=$this->params;
    if(!$params['vin']){
      $data = foo(new \ClickBlocks\DB\OrchestraCamSettings())->getKeys($params['type']);
      if (!is_array($data) || !count($data))
      {
        return $this->send(array('status' => 'error', 'message' => 'No Data found.'), 'CamSettings');
      }
       //if($protocol['type']==='can' or $protocol['type']==='j1850' ){
      return $this->send(array('status' => 'success', 'message' => $data ), 'CamSettings');
        //}
}


        $vin=$params['vin'];

       $vinPart=substr($vin,4,2);
       $year=getYearFromVin($vin);
       $data = foo(new \ClickBlocks\DB\OrchestraCamSettings())->getYearModel($vinPart, $year);
       // var_dump($data);
     // echo "year is $year and vinPart is $vinPart";
       $model=$data['model'];
       
      if($model=='Sportster') $motor= 'Sportster';
      elseif(($model=='Touring' and $year < 2017) || ($model=='CVO' and $year < 2017)|| ($model=='Dyna' and $year < 2017) || ($model=='S Series' and $year < 2017)) $motor= 'Twin Cam';
      elseif(($model=='Touring' and $year >= 2017) || ($model=='CVO' and $year >=  2017) || ($model=='Dyna' and $year >= 2017) || ($model=='S Series' and $year >= 2017)) $motor= 'Milwaukee 8';
      elseif($model=='Softail' and $year < 2018) $motor= 'Twin Cam';
      else if($model=='Softail' and $year >= 2018) $motor= 'Milwaukee 8';

      $data = foo(new \ClickBlocks\DB\OrchestraCamSettings())->getCamShaft($motor);
      return $this->send(array('status' => 'success', 'message' => $data ), 'CamSettings');
    }

public function api_stockRecovery(){
  $params = $this->params;
  $vin=$params['vin'];
   if (!$vin)
      {
        return $this->send(array('status' => 'error', 'message' => 'vin is required.'), 'stockRecovery');
      }

      $data = foo(new DB\OrchestraCustomers())->recoveryDetails( $vin );
        return $this->send(array('status' => 'success', 'message' => $data), 'stockRecovery');
}

    // private function api_DTC()
    // {
    //   $params = $this->params;
    //   if (!$params['code'])
    //   {
    //     return $this->send(array('status' => 'error', 'message' => array('description'=>'Code is required.')), 'DTC');
    //   }

    //   $data = foo(new \ClickBlocks\DB\OrchestraDtc())->getKeys($params['code']);
    //   if (!is_array($data) || !count($data))
    //   {
    //     return $this->send(array('status' => 'error', 'message' => array('description'=>'No additional information.')), 'DTC');
    //   }
    //   $description = $data[0]['description'];

    //   return $this->send(array('status' => 'success', 'message' => array('description' => $description)), 'DTC');
    // }

//"" y
//April 2018

private function api_redirectStripe(){
$params=$this->params;

// if(isset($params['token'])) $token = $params['token'];
// if(isset($params['quantity'])) $qty = $params['quantity'];
// if(isset($params['orderTotal'])) $total = $params['orderTotal'];
// if(isset($params['subTotal'])) $subtotal = $params['subTotal'];
// if(isset($params['unitPrice'])) $uprice = $params['unitPrice'];
// if(isset($params['fullName'])) $fn=$params['fullName'];
// if(isset($params['address'])) $address=$params['address'];
// if(isset($params['apt'])) $apt=$params['apt'];
// if(isset($params['zip'])) $zip=$params['zip'];
// if(isset($params['city'])) $city=$params['city'];
// if(isset($params['state'])) $state=$params['state'];

// if(isset($params['country'])) $country=$params['country'];
// if(isset($params['useShipAddrAsBillingAddr'])) $usaBilling=$params['useShipAddrAsBillingAddr'];

// //IF BILLING ADDRESS IS DIFFERENT THAN MAILING 

// // if(isset($params['billaddress'])) $billad=$params['billad'];
// // if(isset($params['billapt'])) $billapt=$params['billapt'];
// // if(isset($params['billzip'])) $billzip=$params['billzip'];
// // if(isset($params['billcity'])) $billcity=$params['billcity'];
// // if(isset($params['billstate'])) $billstate=$params['billstate'];
// // if(isset($params['billcountry'])) $billcountry=$params['billcountry'];

// if(isset($params['totTaxes'])) $taxRate=$params['totTaxes'];
// if(isset($params['shippingCost'])) $shipCost=$params['shippingCost'];
// if(isset($params['tel'])) $tel=$params['tel']; 
// if(isset($params['email'])) $email=$params['email']; 




  // $post = array('token' => 'tok_visa','quantity' => 2,'unitPrice' => 99, 'subTotal' => 198,'orderTotal' => "294.00",'fullName' => 'John Doe','address' => '313 Main st','apt' => '15','zip' => '90344',
  // 'city' => 'Manasas','state' => 'CA','country' => 'USA','totTaxes' =>"9.50",'shippingCost'=>6,'useShipAddrAsBillingAddr'=>'same', 'tel' => '714-567-9867', 'email' => 'samyonas@gmail.com');

$ch = curl_init( 'http://dev.vhfp3.com/prototype/stripe/index.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_SSLVERSION, 6);

// execute!
// 10.230.200.124
$response = curl_exec($ch);

// close the connection, release resources used
curl_close($ch);

// do anything you want with your response
// echo $response;
}


 private function api_DTC()
    {
      $params = $this->params;
       $data = foo(new \ClickBlocks\DB\OrchestraDtc())->getDtc($params['code']);
     $code = $data['code'];
     $shortDes = $data['short'];
      $longDes = $data['description'];
      $video=$data['video'];
    
    // // /var_dump($data);
//       if(!$params['code']){
//     for ($i=0; $i < count($data) ; $i++) { 
//      $data[$i]['videoUrl']= explode(',',$data[$i]['videoUrl']);
//      // var_dump( $data['videoUrl']);
//     }
// }

    if ($params['code'])
      {
     
      if (!is_array($data) || !count($data))
      {
        return $this->send(array('status' => 'error', 'message' => array('description'=>'No additional information.')), 'DTC');
      }
    
     //  $oneVideo=explode(",",$video);
     // $oneVideo=$oneVideo[0];

      return $this->send(array('status' => 'success', 'message' => array('code'=>$code,'shortDescr' => $shortDes,'description'=>$longDes, 'videoUrl'=>$oneVideo)), 'DTC');
       // return $this->send(array('status' => 'success', 'message' => array('codeRead' => $data )), 'DTC');

    }
     return $this->send(array('status' => 'success', 'message' => array('codeList' => $data )), 'DTC');
    }




  private function api_DTC2()
    {

      //this api was dupicated from DTC to allow more videos on the app;
      //the only difference with the DTC api is this api displays an array of video urls wile DTC displays one vid
      $params = $this->params;
       $data = foo(new \ClickBlocks\DB\OrchestraDtc())->getDtc($params['code']);
     $code = $data['code'];
     $shortDes = $data['short'];
      $longDes = $data['description'];
      $video=$data['video'];


      if(!$params['code']){
    for ($i=0; $i < count($data) ; $i++) { 
     $data[$i]['videoUrl']= explode(',',$data[$i]['videoUrl']);
      if(count($data[$i]['videoUrl'])==1 and $data[$i]['videoUrl'][0]=="") $data[$i]['videoUrl']=array();
     // var_dump( $data['videoUrl']);
    }
  }

    if ($params['code'])
      {
     
      if (!is_array($data) || !count($data))
      {
        return $this->send(array('status' => 'error', 'message' => array('description'=>'No additional information.')), 'DTC');
      }
    
    $video=explode(",",$video);
      if(count($video)==1 and $video[0]=="") $video=array();
      return $this->send(array('status' => 'success', 'message' => array('code'=>$code,'shortDescr' => $shortDes,'description'=>$longDes, 'videoUrl'=>$video)), 'DTC');
       // return $this->send(array('status' => 'success', 'message' => array('codeRead' => $data )), 'DTC');

    }
     return $this->send(array('status' => 'success', 'message' => array('codeList' => $data )), 'DTC');
    }


     private function api_SeedKey2020()
     {  

      $params = $this->params;

        if ( !$params['seed'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'seed is required.' ), 'SeedKey2020');
        }

        if ( !$params['asciikey'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'asciikey is required.' ), 'SeedKey2020');
        }



        foo(new \ClickBlocks\DB\OrchestraSeedKeys2020())->updateKey2020($params['asciikey'], $params['seed']);
      return $this->send(array('status' => 'success', 'message' => 'updated'), 'SeedKey2020');  
      }


    private function api_KeyFromSeed()
     {  

      $params = $this->params;

        if ( !$params['seed'] ) {
          return $this->send(array( 'status' => 'error', 'message' => 'seed is required.' ), 'KeyFromSeed');
        }

      $asciikey=foo(new \ClickBlocks\DB\OrchestraSeedKeys2020())->keyFromSeed($params['seed']);

       return $this->send(array('status' => 'success', 'message' => $asciikey), 'KeyFromSeed');  

        }


private function api_KeyFromSeedCan()
     {  

      $params = $this->params;

        if ( !$params['seed'] ) {
          return $this->send(array( 'status' => 'error', 'message' => 'seed is required.' ), 'KeyFromSeedCan');
        }

      $asciikey=foo(new \ClickBlocks\DB\OrchestraSeedKeys())->keyFromSeed($params['seed']);

       return $this->send(array('status' => 'success', 'message' => $asciikey), 'KeyFromSeedCan');  

        }



  private function api_SeedKey()
    {
        $params = $this->params;
        if ( !$params['seed'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'seed is required.' ), 'SeedKey');
        }

        if ( !$params['mask'] ) {
            return $this->send(array( 'status' => 'error', 'message' => 'mask is required.' ), 'SeedKey');
        }

        
           
      $params['firmw']=isset($params['firmw']) && !empty($params['firmw']) ? $params['firmw'] : ""  ;
      $ecmpn = ( isset($params['ecmpn']) && !empty($params['ecmpn']) ) ? $params['ecmpn'] : "";
      $vin   = ( isset($params['vin']) && !empty($params['vin']) ) ? $params['vin'] : "1HD1KRM1XFB633719";

     $year=getYearFromVin($vin);


// #$boot_path=Core\Register::getInstance()->config->dirs['bootloader'];
//       //A path to the bootloader files
      $boot_path='http://vhfp3.com/prototype/bootloader';



if(canORJ1850($params['firmw'])=='CAN'){
if($params['firmw']==241 or $params['firmw']==242) $bikeType='olderCAN'; 
else $bikeType='newerCAN';
}
else{
  //if ecmFW is corrupted..makes general assumption that the bike is can and not 2011 and newer bike
  if(canORJ1850($params['firmw'])!='J1850' and $year >=2012)  $bikeType='newerCAN';

}


if($bikeType=='olderCAN'){
              $bootRead=$boot_path.'/2011/boot_read_2011.bin';
              $bootWrite=$boot_path.'/2011/boot_write_2011.bin';
              $config=$boot_path.'/2011/config_rdwr_2011.bin';
}

else{
  if($bikeType=='newerCAN'){
               $bootRead=$boot_path.'/2014/boot_rdwr_2014.bin';
               $bootWrite=$boot_path.'/2014/boot_rdwr_2014.bin';//Write file expected to change in the future
               $config=$boot_path.'/2014/config_rdwr_2014.bin';
                             } 
}

//only newerCAN have recovery files
$recoveryRW=$boot_path.'/recovery/boot_recovery_2014.bin';
$recoveryConfig=$boot_path.'/recovery/config_recovery_2014.bin';
      //return $this->send(array('year' => $year, '10th'=>$vin[9], 'message' => array('key' => $asciiKey)), 'SeedKey');

      $ecmfw =$params['firmw'];
      $dbTable    = decodeVinSeedTable($vin, $year, $ecmpn, $ecmfw);

      // if($this->debug_oop==1) {
      //   $this->logger_oop('log_SeedKey_'. date('m_d_Y') .'.txt', array(
      //                       'seed'    => $params['seed'],
      //                       'mask'    => $params['mask'],
      //                       'vin'     => $params['vin'],
      //                       'ecmpn'   => $params['ecmpn'],
      //                       'year'    => $year,
      //                       '10th'    => $vin[9],
      //                       'dbTable' => $dbTable,
      //                     ));
      // }

       $decodeMask = decodeVinMask($vin, $year, $ecmpn, $ecmfw);

      // patch:  Even though this is a J1850 bike, return a CAN Key
      if ( $ecmpn == '34246-08B' || $ecmpn == '32534-11' ) {
          $dbTable = "SeedKeys";
          $decodeMask = 0x004A;
      }


 // patch:
      if ($ecmpn == '34245-11' && $vin == '1HD1PZ81XAB957936') {
          $dbTable = "SeedKeys";
          $decodeMask = 0x004A;
      }

 
      // patch for customer having problems
      // 2012 1HD1KBM12CB621003 "fVersion":"6.1.4"
      if ($ecmpn == '34245-10') {
          $dbTable = "SeedKeys_J1850";
          $decodeMask = 0x0000;
      }


//add vin in the array to to give customer different key

$new_Seed=array('1HD1LP316LB406574','1HD1YWK21LB029268','1HD1YHK19LB025049','1HD1YWK19LB023466','1HD1YWK10LB022948','1HD1YWK14LB021639','1HD1YWK18LB023782','1HD1YWK12LB021882','1HD1YWK15LB022055','5HD1YMJ44LB026669','1HD1YJJ19LB023486','1HD1YWK20LB028936','5HD1LP349LB408323','1HD1YWK26LB027077','5HD1YWK56LB010931','5HD1YWK43LB026270','5HD1YWK68LB021678','1HD1YWK19LB029669','1HD1YWK1XLB026568','1HD1YHK10LB033430','1HD1YWK22LB034785','5HD4LE243LB409213','1HD1YWK17LB025717','1HD1YWK13LB034589');

$seed_before2020=array('5HD1GV4CXAC328518','1HD1GV412BC328312','1HD1FC4198Y669936','1HD1TBH13LB952499','1HD1MAF1XLB850028','1HD1KEM17AB646820','1HD1MAF10LB852340');

$J1850_Seed=array('5HD4CR2C0CC410036','1HD1CT332CC448132');

if(in_array($vin, $new_Seed)) {

$dbTable = "SeedKeys2020";
     $decodeMask = 0x004A;
    }

if(in_array($vin, $seed_before2020)) {
   $dbTable = "SeedKeys";
     $decodeMask = 0x004A;
    
    }

if(in_array($vin, $J1850_Seed)) {
    $dbTable = "SeedKeys_J1850";
     $decodeMask = 0x0000;
    
}



      $keyMask = (intval(substr($params['mask'], 14, 4), 16));
      $keyMask = str_pad(dechex($keyMask & $decodeMask), 4, "0", STR_PAD_LEFT);


      if ( $dbTable == "SeedKeys" || $dbTable == "SeedKeys2020" ) {

            $data = foo(new \ClickBlocks\DB\OrchestraSeedKeys())->getKeys($params['seed']); 
            $data2020 = foo(new \ClickBlocks\DB\OrchestraSeedKeys2020())->getKeys($params['seed']);
   
                 }



        if ( $dbTable == "SeedKeys_J1850" ) {
            $data = foo(new \ClickBlocks\DB\OrchestraSeedKeys_J1850())->getKeys($params['seed']);
        }

        if ( !is_array($data) || !count($data) ) {
            return $this->send(array( 'status' => 'error', 'message' => 'seed does not exist.' ), 'SeedKey');
        }

        $rawKey = pack('H*', $data[0]['asciiKey']);
        $rawKey = dechex(intval($rawKey, 16) ^ intval($keyMask, 16));
        $finalKey = strtoupper($rawKey) . "<br>";
        $finalKey = str_pad($finalKey, 8, "0", STR_PAD_LEFT);
        $asciiKey = '';
        for ($i = 0; $i < 4; $i = $i + 1) {
            $asciiKey .= dechex(ord(substr($finalKey, $i, 1)));
        }


        //for 2020

         $rawKey2020 = pack('H*', $data2020[0]['asciiKey']);
         
        $rawKey2020 = dechex(intval($rawKey2020, 16) ^ intval($keyMask, 16));
        $finalKey2020 = strtoupper($rawKey2020) . "<br>";

        $finalKey2020 = str_pad($finalKey2020, 8, "0", STR_PAD_LEFT);

        $asciiKey2020 = '';
        for ($i = 0; $i < 4; $i = $i + 1) {
            $asciiKey2020 .= dechex(ord(substr($finalKey2020, $i, 1)));
        }

    

       if($year < 2020) $debug_asciiKey= $asciiKey;
        
        elseif(($params['firmw'] < 921  && $year==2020)&& !in_array($vin, $new_Seed)) { 
          $debug_asciiKey= $asciiKey;
        }

         elseif(($params['firmw'] == 415  && $year==2020)&& !in_array($vin, $new_Seed)) { 
          $debug_asciiKey= $asciiKey;
        }
      
       else $debug_asciiKey= $asciiKey2020;

     if($this->debug_oop==1) {
            $this->logger_oop('log_SeedKey_'. date('m_d_Y') .'.txt', array(
                'seed'    => $params['seed'],
                'mask'    => $params['mask'],
                'vin'     => $params['vin'],
                'ecmpn'   => $params['ecmpn'],
                'year'    => $year,
                '10th'    => $vin[9],
                'dbTable' => $dbTable,
                 'Key'     => $debug_asciiKey,
                'ecmFirmware'     => $params['firmw'],
            ));
        }



      /*
       *      Vance & Hines Modification
       *      Replacement Code
       */
      $passcode = "faradaycage";
       $before2020= $asciiKey ^  $passcode;
      $onOrAfter2020= $asciiKey2020 ^  $passcode;
      $keyForCompatability = $year < 2020 ? $before2020 :  $onOrAfter2020 ; 
       $finalKey=$year < 2020 ? array($before2020, $onOrAfter2020 ) :  array($onOrAfter2020,$before2020);
       
        if(($params['firmw'] >= 921 && $year==2020) && in_array($vin, $seed_before2020)) { 
          $keyForCompatability =  $before2020 ; 
          $finalKey= array($before2020, $onOrAfter2020 );
        }
      
      //415 should get old key unless patched for new key
        if(($params['firmw'] == 415 && $year==2020) && !in_array($vin, $new_Seed)) { 
          $keyForCompatability =  $before2020 ; 
          $finalKey= array($before2020,$onOrAfter2020 );
        }
       
         if(($params['firmw'] < 921 && $year==2020) && !in_array($vin, $new_Seed)) { 
          $keyForCompatability =  $before2020 ; 
          $finalKey= array($before2020, $onOrAfter2020 );
        }
       
    /*
    *
    */

//for yerko to test
    if($vin=='1HD1TEH15LY951708'){
      $finalKey = array($before2020, $onOrAfter2020);
    }


 $vinToByPass=foo(new DB\OrchestraOta())->customCksBoot($vin,'btloader');


        // if($params['vin']==$vinToByPass && $bikeType=='newerCAN'){
    // if(in_array($params['vin'], $vinToByPass) && $bikeType=='newerCAN'){
  if($vinToByPass && $bikeType=='newerCAN'){
        
        return $this->send(array('status' => 'success', 'message' => array('key'=>$keyForCompatability,'keyArr' => array($finalKey),'bootloaderAvailable'=>'true','bootRead'=>$recoveryRW, 'bootWrite'=>$recoveryRW, 'bootConfig'=>$recoveryConfig)), 'SeedKey');
        }      

    //if CAN bikes with specified firmware
  
       elseif(($bikeType=='newerCAN' or  $bikeType=='olderCAN') and !$vinToByPass) {
       
      return $this->send(array('status' => 'success', 'message' => array('key'=>$keyForCompatability,'keyArr' => array($finalKey),'bootloaderAvailable'=>'true','bootRead' => $bootRead, 'bootWrite'=>$bootWrite, 'bootConfig'=>$config)), 'SeedKey');
    }
   

    //otherwise j1850 bike
    
     else {
        return $this->send(array('status' => 'success', 'message' => array('key'=>$keyForCompatability,'keyArr' => array($finalKey), 'bootloaderAvailable'=>'false')), 'SeedKey');
      }
   

    }

    private function api_getMapCases()
    {
      $params = $this->params;
      $fields = array('eManufacturer', 'eName', 'baffleType', 'eSize');
      foreach ($fields as $field)
      {
        $ret[$field] = foo(new DB\OrchestraMaps())->getPossibleValues($field);
      }
      return $this->send(array('status' => 'success', 'message' => $ret), 'getMapCases');
    }

    /**
     * Used to log all key/value pairs sent from the Apps
     * @return type string
     */
    private function api_SendLogError()
    {
      $params = $this->params;

      if($this->debug_oop==1) {
        $this->logger_oop('log_SendLogError_'. date('m_d_Y') .'.txt', $params);
      }

      return $this->send(array('status' => 'success', 'message' => $params), 'SendLogError');

    }

    private function api_SendMap()
    {

        /*
        $params['vin']           = 'map_2014_touring_2';
        $params['eBaffleType']   = 'PlaceHolder';
        $params['mMake']         = 'PlaceHolder';
        $params['hVersion']      = '0.0.0';
        $params['bModel']        = 'Touring';
        $params['eManufacturer'] = 'PlaceHolder';
        $params['bMake']         = 'Harley Davidson';
        $params['aVersion']      = '1.1.4 build:1.2.4098 iPhone 7.0.2';
        $params['eSize']         = 'PlaceHolder';
        $params['mDescription']  = '41000188D - \r\nV&H, 2014 Touring, Big Radius 2-1, Standard, 103';
        $params['mModel']        = 'Touring';
        $params['mYear']         = '2014';
        $params['fVersion']      = '0.0.0';
        $params['isOriginalMap'] = '0';
        $params['bYear']         = '2014';
        $params['ecmPart']       = 'ECM-00000000';
        $params['ecmFirmware']   = '0.0';
        $params['eName']         = 'PlaceHolder';
        $params['mapStream']     = '';
        $params['calID']
        $params['method']
        */

        $params   = $this->params;
        $mediaDir = $this->mediaDir;


         //SY Checks if a calibrationID is ADCII or not max calID in db had length of 12
        $params['calID']= trim(urldecode($params['calID']));
        if (preg_match('/[^A-Za-z0-9-]/', $params['calID']) or strlen($params['calID']) > 16){
          $params['calID'] ='UNKNOWN';
        }


        if (!$params['vin']) {
            $status  = 'error';
            $message = 'vin is required.';

            if($this->debug_oop==1) {
                $this->logger_oop('log_SendMap_'. date('m_d_Y') .'.txt',
                array(
                    'status'        => $status,
                    'message'       => $message,
                    'vin'           => $params['vin'],
                    'eBaffleType'   => $params['eBaffleType'],
                    'mMake'         => $params['mMake'],
                    'hVersion'      => $params['hVersion'],
                    'bModel'        => $params['bModel'],
                    'eManufacturer' => $params['eManufacturer'],
                    'bMake'         => $params['bMake'],
                    'aVersion'      => $params['aVersion'],
                    'eSize'         => $params['eSize'],
                    'mDescription'  => $params['mDescription'],
                    'mModel'        => $params['mModel'],
                    'mYear'         => $params['mYear'],
                    'fVersion'      => $params['fVersion'],
                    'isOriginalMap' => $params['isOriginalMap'],
                    'bYear'         => $params['bYear'],
                    'ecmPart'       => $params['ecmPart'],
                    'ecmFirmware'   => $params['ecmFirmware'],
                    'eName'         => $params['eName'],
                    'calID'         => $params['calID']
                ));
            }

            return $this->send(array('status' => $status, 'message' => $message, 'mapid' => '0000'), 'SendMap');
        }

        if (!$params['mapStream']) {
            $status  = 'error';
            $message = 'mapStream is required.';

            if($this->debug_oop==1) {
                $this->logger_oop('log_SendMap_'. date('m_d_Y') .'.txt',
                    array(
                        'status'        => $status,
                        'message'       => $message,
                        'vin'           => $params['vin'],
                        'eBaffleType'   => $params['eBaffleType'],
                        'mMake'         => $params['mMake'],
                        'hVersion'      => $params['hVersion'],
                        'bModel'        => $params['bModel'],
                        'eManufacturer' => $params['eManufacturer'],
                        'bMake'         => $params['bMake'],
                        'aVersion'      => $params['aVersion'],
                        'eSize'         => $params['eSize'],
                        'mDescription'  => $params['mDescription'],
                        'mModel'        => $params['mModel'],
                        'mYear'         => $params['mYear'],
                        'fVersion'      => $params['fVersion'],
                        'isOriginalMap' => $params['isOriginalMap'],
                        'bYear'         => $params['bYear'],
                        'ecmPart'       => $params['ecmPart'],
                        'ecmFirmware'   => $params['ecmFirmware'],
                        'eName'         => $params['eName'],
                        'calID'         => $params['calID']
                    ));
            }

            return $this->send(array('status' => $status, 'message' => $message, 'mapid' => '0000'), 'SendMap');
        }
 //SY added logstream for fp3 error log
 //feb 2020
  if ($params['logStream']) {
    $params['logStream'] = base64_decode($params['logStream']);
    $fileName = $params['vin'] . '_' . \date('m-d-Y_h.i.s') . '.txt';
    $ErrPath = $this->mediaDir."/fp3ErrorLog/".$params['mYear']."/";
    if(!file_exists($ErrPath)) mkdir($ErrPath);
    file_put_contents($ErrPath . $fileName, $params['logStream']);

 }//END logstream

        //save map stream to a file
        $params['mapStream'] = base64_decode($params['mapStream']);

        $fileName = $params['vin'] . '_' . \time() . '.map';
        $path = \ClickBlocks\Core\IO::dir('temp') . '/';
        file_put_contents($path . $fileName, $params['mapStream']);
        $jc = false;


        $fsize = filesize($path . $fileName);
        // file size check, s/b at least 96x256 bytes
        if ( $fsize < (96*256) ) {

            $status  = 'error';
            $message = "Incorrect file length: {$fsize}";

            if($this->debug_oop==1) {
                $this->logger_oop('log_SendMap_'. date('m_d_Y') .'.txt',
                    array(
                        'status'        => $status,
                        'message'       => $message,
                        'vin'           => $params['vin'],
                        'eBaffleType'   => $params['eBaffleType'],
                        'mMake'         => $params['mMake'],
                        'hVersion'      => $params['hVersion'],
                        'bModel'        => $params['bModel'],
                        'eManufacturer' => $params['eManufacturer'],
                        'bMake'         => $params['bMake'],
                        'aVersion'      => $params['aVersion'],
                        'eSize'         => $params['eSize'],
                        'mDescription'  => $params['mDescription'],
                        'mModel'        => $params['mModel'],
                        'mYear'         => $params['mYear'],
                        'fVersion'      => $params['fVersion'],
                        'isOriginalMap' => $params['isOriginalMap'],
                        'bYear'         => $params['bYear'],
                        'ecmPart'       => $params['ecmPart'],
                        'ecmFirmware'   => $params['ecmFirmware'],
                        'eName'         => $params['eName'],
                        'calID'         => $params['calID']
                    ));
            }

            return $this->send(array('status' => $status, 'message' => $message, 'vin' => $params['vin']), 'SendMap');
        }


        if ( $params['bModel'] == 'Unknown' || $params['mModel'] == 'Unknown' || $params['bYear'] == "0" || $params['mYear'] == "0" ) {
            $status  = 'error';
            $message = 'Model is Unknown And/Or the Year is invalid';

            if($this->debug_oop==1) {
                $this->logger_oop('log_SendMap_'. date('m_d_Y') .'.txt',
                    array(
                        'status'        => $status,
                        'message'       => $message,
                        'vin'           => $params['vin'],
                        'eBaffleType'   => $params['eBaffleType'],
                        'mMake'         => $params['mMake'],
                        'hVersion'      => $params['hVersion'],
                        'bModel'        => $params['bModel'],
                        'eManufacturer' => $params['eManufacturer'],
                        'bMake'         => $params['bMake'],
                        'aVersion'      => $params['aVersion'],
                        'eSize'         => $params['eSize'],
                        'mDescription'  => $params['mDescription'],
                        'mModel'        => $params['mModel'],
                        'mYear'         => $params['mYear'],
                        'fVersion'      => $params['fVersion'],
                        'isOriginalMap' => $params['isOriginalMap'],
                        'bYear'         => $params['bYear'],
                        'ecmPart'       => $params['ecmPart'],
                        'ecmFirmware'   => $params['ecmFirmware'],
                        'eName'         => $params['eName'],
                        'calID'         => $params['calID']
                    ));
            }

            return $this->send(array('status' => $status, 'message' => $message, 'mapid' => '0000'), 'SendMap');
        }



        if ( strtolower($params['mModel']) == 'touring' || strtolower($params['mModel']) == 'cvo' ) {
            $isTouring = true;
        }


        $params['mYear'] = $params['bYear'];




        if ( !compareChecksum($path . $fileName, $params['mYear'], $jc, $isTouring, $params['vin']) ) {
            // 1HD1PXN14FB960735  allow
      
            $status = 'error';
            $message = "mapStream was corrupted. Checksum is wrong.";
  
            if ( $this->debug_oop == 1 ) {
                $this->logger_oop('log_SendMap_' . date('m_d_Y') . '.txt',
                    array(
                        'status'        => $status,
                        'message'       => $message,
                        'vin'           => $params['vin'],
                        'eBaffleType'   => $params['eBaffleType'],
                        'mMake'         => $params['mMake'],
                        'hVersion'      => $params['hVersion'],
                        'bModel'        => $params['bModel'],
                        'eManufacturer' => $params['eManufacturer'],
                        'bMake'         => $params['bMake'],
                        'aVersion'      => $params['aVersion'],
                        'eSize'         => $params['eSize'],
                        'mDescription'  => $params['mDescription'],
                        'mModel'        => $params['mModel'],
                        'mYear'         => $params['mYear'],
                        'fVersion'      => $params['fVersion'],
                        'isOriginalMap' => $params['isOriginalMap'],
                        'bYear'         => $params['bYear'],
                        'ecmPart'       => $params['ecmPart'],
                        'ecmFirmware'   => $params['ecmFirmware'],
                        'eName'         => $params['eName'],
                        'calID'         => $params['calID'],
                    ));
            }

   //bypass
            // $vinToByPass=array('','');
 
         //     if ($params['vin'] != '1HD1GZM35EC324073' ) {
         // // if(!in_array($params['vin'], $vinToByPass)){
         //    return $this->send(array('status' => $status, 'message' => $message, 'mapid' => '0000'), 'SendMap');
         //    }
                     $vinToByPass=foo(new DB\OrchestraOta())->customCksBoot($params['vin'],'chksum');
                    $vinToByPass=filter_var($vinToByPass, FILTER_VALIDATE_BOOLEAN); // true;

               
            if (!$vinToByPass) {
             
            return $this->send(array('status' => $status, 'message' => $message, 'mapid' => '0000'), 'SendMap');
            }

        }

        $mapArray     = decodeFile( $path . $fileName, $params['mYear'], strtolower($params['mModel']), $params['ecmFirmware'] );
        $mapStructure = getMapStructure( $params['ecmFirmware'] );


        //Log All parameters at this point
        if($this->debug_oop==1) {
            $this->logger_oop('log_SendMap_END_'. date('m_d_Y') .'.txt',
            array(
                'method'        => $params['method'],
                'vin'           => $params['vin'],
                'eBaffleType'   => $params['eBaffleType'],
                'mMake'         => $params['mMake'],
                'hVersion'      => $params['hVersion'],
                'bModel'        => $params['bModel'],
                'eManufacturer' => $params['eManufacturer'],
                'bMake'         => $params['bMake'],
                'aVersion'      => $params['aVersion'],
                'eSize'         => $params['eSize'],
                'mDescription'  => $params['mDescription'],
                'mModel'        => $params['mModel'],
                'mYear'         => $params['mYear'],
                'fVersion'      => $params['fVersion'],
                'isOriginalMap' => $params['isOriginalMap'],
                'bYear'         => $params['bYear'],
                'ecmPart'       => $params['ecmPart'],
                'ecmFirmware'   => $params['ecmFirmware'],
                'eName'         => $params['eName'],
                'calID'         => $params['calID'],
                'map fileName'  => $fileName,
                'file path'     => $path,
                'REQUEST'       => (isset($_SERVER["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
            ));
        }


        //ip address
        if ( isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        //intercept parameter stream
        //param gets saved in the Customers table even though it's not specified further down in the Customers section.
        $params['demoMode'] = strtoupper($params['demoMode']);

        $encodedFields = array(
            'bMake',
            'bModel',
            'bYear',
            'mMake',
            'mModel',
            'mYear',
            'eManufacturer',
            'eName',
            'eBaffleType',
            'eSize',
            'mDescription',
            'mPhotoUrl',
            'aVersion',
            'hVersion',
            'fVersion',
        );

        foreach ($encodedFields as $field) {
            $params[ $field ] = urldecode($params[ $field ]);
            $params[ $field ] = trim(str_replace('%20', ' ', $params[ $field ]));
            $params[ $field ] = preg_replace('/[[:blank:]]{2,}/', ' ', $params[ $field ]);
        }

        if ( $params['mPhotoUrl'] ) {
            $params['mPhotoUrl'] = 'http://' . $_SERVER['HTTP_HOST'] . Core\IO::url($this->config->dirs['MEDIA']) . '/photos/' . $params['mPhotoUrl'];
        } elseif ( $params['photo'] ) {

            $photo = base64_decode($params['photo']);
            $photoFileName = md5(time()) . '.jpg';
            $photoFile = $mediaDir . '/photos/' . $photoFileName;
            file_put_contents($photoFile, $photo);
            $params['mPhotoUrl'] = 'http://' . $_SERVER['HTTP_HOST'] . Core\IO::url($this->config->dirs['MEDIA']) . '/photos/' . $photoFileName;
        }


        $apiVersion = isset($params['apiVersion']) ? trim($params['apiVersion']) : 0;
        settype($apiVersion, int);


      // Create & save Customer maps
      // saves a map record with isOriginalMap=0,
      // isOriginalMap=0,
      // since isOriginalMap is not specified here, its value defaults to zero in the db
      $map = foo(new DB\ServiceMaps())->getByID();
      $map->name          = date('Y-m-d H:i:s') . '_' . $params['vin'];
      $map->description   = $params['mDescription'];
      $map->make          = $params['mMake'];
      $map->model         = $params['mModel'];
      $map->year          = $params['mYear'];
      $map->eManufacturer = $params['eManufacturer'];
      $map->eName         = $params['eName'];
      $map->baffleType    = $params['eBaffleType'];
      $map->eSize         = $params['eSize'];
      $map->ecmFirmware   = $params['ecmFirmware'];
      $map->updated       = 'NOW()';
      $map->apiVersion    = $apiVersion;


      // isCustomerMap = 0 is used to identify internal maps
      // flag is not passed in by customers
      if(isset($params['isCustomerMap']))
      {
          $map->isCustomerMap = $params['isCustomerMap'];
      } else {
          $map->isCustomerMap = 1;
      }

      $map->photoUrl = $params['mPhotoUrl'];
      $map->save();


      // apiVersion check to determine where to save internal maps
      // When used, should be a positive non-zero int.  Can be up to 3 digits.
      if( isset($apiVersion) && $apiVersion != 0 ){
          //internal maps
          $dir = $mediaDir .'/vh/'. $apiVersion;
          if (file_exists($dir)) {
              $mapFile = $dir .'/'. $map->mapID . '.map';
              copy($path . $fileName, $mapFile);
          } else {
              mkdir($dir, 0777, TRUE);
              $mapFile = $dir .'/'. $map->mapID . '.map';
              copy($path . $fileName, $mapFile);
          }
      } else {
          //customer maps
          $dir = $mediaDir .'/'. $params['mYear'];
          if (file_exists($dir)) {
              $mapFile = $dir .'/'. $map->mapID . '.map';
              copy($path . $fileName, $mapFile);
          } else {
              mkdir($dir, 0777, TRUE);
              $mapFile = $dir .'/'. $map->mapID . '.map';
              copy($path . $fileName, $mapFile);
          }
      }


      //Create or update and save customer
      $customerID = foo(new DB\OrchestraCustomers())->getCustomerIDByVin($params['vin']);
      $customer = new DB\Customers($customerID);
      $customer->currentMap = $map->mapID;
      $customer->make       = $params['bMake'];
      $customer->model      = $params['bModel'];
      $customer->year       = $params['bYear'];
      $customer->updated    = 'NOW()';
      $customer->calID      = isset( $params['calID'] ) ? $params['calID'] : "UNKNOWN";
      $customer->ipAddress  = $ip;
      $customer->setValues($params)->save();
      //$customer->save();

      //saves original map record with isOriginalMap=1
      if ($params['isOriginalMap'])
      {
          if (foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($customer->customerID)) {
              if($this->debug_oop==1)
              {
                $this->logger_oop('log_SendMap_'. date('m_d_Y') .'.txt',
                    array(
                      'customerID' => $customer->customerID,
                      'mapid'      => $map->mapID,
                      'message'    => 'customer already has isOriginalMap=1',
                    ));
              }
          }

          // method clears the isOriginalMap flag
          foo(new DB\OrchestraMaps())->clearOriginalMaps($customer->customerID);

          $oMap = foo(new DB\ServiceMaps())->getByID();
          $oMap->name = date('Y-m-d H:i:s') . '_' . $params['vin'];
          $oMap->description    = $params['mDescription'];
          $oMap->make           = $params['mMake'];
          $oMap->model          = $params['mModel'];
          $oMap->year           = $params['mYear'];
          $oMap->eManufacturer  = $params['eManufacturer'];
          $oMap->eName          = $params['eName'];
          $oMap->baffleType     = $params['eBaffleType'];
          $oMap->eSize          = $params['eSize'];
          $oMap->ecmFirmware    = $params['ecmFirmware'];
          $oMap->updated        = 'NOW()';
          $oMap->apiVersion     = $apiVersion;
          $oMap->isCustomerMap  = 1;
          $oMap->isOriginalMap  = 1;
          $oMap->customerID     = $customer->customerID;
          $oMap->save();

          //$oMapFile = $mediaDir . '/' . $oMap->mapID . '.map';
          //copy($path . $fileName, $oMapFile);

          if( isset($apiVersion) && $apiVersion != 0 ){
              //internal maps
              $dir = $mediaDir .'/vh/'. $apiVersion;
              if (file_exists($dir)) {
                  $oMapFile = $dir .'/'. $oMap->mapID . '.map';
                  copy($path . $fileName, $oMapFile);
              } else {
                  mkdir($dir, 0777, TRUE);
                  $oMapFile = $dir .'/'. $oMap->mapID . '.map';
                  copy($path . $fileName, $oMapFile);
              }
          } else {
              //customer maps
              $dir = $mediaDir .'/'. $params['mYear'];
              if (file_exists($dir)) {
                  $oMapFile = $dir .'/'. $oMap->mapID . '.map';
                  copy($path . $fileName, $oMapFile);
              } else {
                  mkdir($dir, 0777, TRUE);
                  $oMapFile = $dir .'/'. $oMap->mapID . '.map';
                  copy($path . $fileName, $oMapFile);
              }
          }

      }

      $map->customerID = $customer->customerID;
      $map->save();


      //save map parts to DB
      foreach ($mapStructure as $pName => $pValue)
      {
        unset($mPart);
        $tableName = '\ClickBlocks\DB\\' . $pValue['DBTableName'];
        $mPart = new $tableName;
        $mPart->mapID = $map->mapID;  //$map


        //Bug Fix: if data is a scalar and a number, json_encode will not turn it into a string
        // causing the csr not to display values in "flat" table types with a zero value.
        // Solution: db column type is a string so typecast it to a string if its a scalar and a number.
        if(is_numeric($mapArray[$pName]))
        {
          $mPart->data = $mapArray[$pName];
          settype($mPart->data, "string");
        } else {
          $mPart->data = json_encode($mapArray[$pName]);
        }

        $mPart->updated = 'NOW()';
        $mPart->save();
        if ($params['isOriginalMap'])
        {
          unset($mPart);
          $mPart = new $tableName;
          $mPart->mapID = $oMap->mapID;  //$oMap



          //Bug Fix: if data is a scalar and a number, json_ecode will not turn it into a string
          // causing the csr not to display values in "flat" table types with a zero value.
          // Solution: db column type is a string so typecast it to a string if its a scalar and a number
          if(is_numeric($mapArray[$pName]))
          {
            $mPart->data = $mapArray[$pName];
            settype($mPart->data, "string");
          } else {
            $mPart->data = json_encode($mapArray[$pName]);
          }


          $mPart->updated = 'NOW()';
          $mPart->save();
        }
      }

      if (foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($customer->customerID))
          $ret['hasOriginalMap'] = 1;
      else
          $ret['hasOriginalMap'] = 0;

      $this->send(array('status' => 'success', 'message' => $ErrPath, 'mapid' => $map->mapID), 'SendMap');
      return;
    }



private function api_testPath(){
    $fileName = tew . '_' . \date('m-d-Y_h.i') . '.map';
        $path = Core\IO::dir($this->config->dirs['log']);
         $path = $path."/fp3_ecm/";
         echo $path . $fileName;
         // echo $path;
}

    private function requireParams($list, $notEmpty = true)
    {
      $diff = array();
      foreach ($list as $field)
      {
        if (!isset($this->params[$field]) || ($notEmpty && !(bool)$this->params[$field]))
          $diff[] = $field;
      }
      if (count($diff))
      {
        return $this->send(array('status' => 'error', 'message' => implode(", ",$diff).' is required.'),'Require');
      }
      else return false;
    }

    private function getMapData()
    {
      $params = $this->params;
      return $mapData;

      $mapStructure = getMapStructure();
      foreach ($mapStructure['VEFrontCyl']['axis']['X']['values'] as $x)
        foreach ($mapStructure['VEFrontCyl']['axis']['Y']['values'] as $y)
        {
          $vefc[$x][$y] = 1;
          $verc[$x][$y] = 2;
        }
      foreach ($mapStructure['AFRatio']['axis']['X']['values'] as $x)
        foreach ($mapStructure['AFRatio']['axis']['Y']['values'] as $y)
        {
          $afr[$x][$y] = 1;
        }
      $data = array(
        'EngineDisplacement' => 103.1,
        'IACWarmupSteps' => array(
            -16 => 64,
            0   => 64,
            16  => 56,
            32  => 47,
            48  => 39,
            64  => 25,
            80  => 20,
            96  => 15,
            112  => 10,
            160  => 10,
            176  => 10,
            192  => 10
          ),
        'IdleRPM' => array(
            -16 => 1400,
            0   => 1400,
            16  => 1304,
            32  => 1216,
            48  => 1136,
            64  => 1064,
            80  => 1000,
            96  => 1000,
            112  => 1000,
            160  => 1000,
            176  => 1000,
            192  => 1000
          ),
        'VEFrontCyl' => $vefc,
        'VERearCyl' => $verc,
        'EITMSoffTemperature' => 180,
        'EITMSonTemperature' => 185,
        'AccelerationEnrichment' => array(
            -16 => 2.50,
            0   => 2.25,
            16  => 1.89,
            32  => 1.53,
            48  => 1.16,
            64  => 0.84,
            80  => 0.61,
            96  => 0.48,
            112  => 0.38,
            144  => 0.33,
            160  => 0.33
          ),
        'AFRatio' => $afr
        );
      return $data;
    }

    private function api_UpdateInfo()
    {

      $params = $this->params;

      if (!$params['vin'])
      {
        return $this->send(array('status' => 'error', 'message' => 'vin is required.'), 'UpdateInfo');
      }
      if (!$params['fVersion'])
      {
        return $this->send(array('status' => 'error', 'message' => 'firmware version is required.'), 'UpdateInfo');
      }
      if (!$params['hVersion'])
      {
        return $this->send(array('status' => 'error', 'message' => 'hardware version is required.'), 'UpdateInfo');
      }
      if (!$params['aVersion'])
      {
        return $this->send(array('status' => 'error', 'message' => 'app version is required.'), 'UpdateInfo');
      }

          //SY May 2019
      $slotRestore=$params['slot0Restore'];
if($slotRestore=='success'){

 foo(new DB\OrchestraMaps())->slotRestoreUpdate($params['vin']);
}
//end SY


      //ip address
      if ( isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) ){
        $ip = $_SERVER['REMOTE_ADDR'];
      } else {
        $ip = '';
      }
      $params['ipAddress'] = $ip;


      if (isset($params['calID'])) {
        $params['demoMode']  = strtoupper($params['demoMode']);
        $encodedFields = array('aVersion', 'hVersion', 'fVersion', 'calID', 'ipAddress', 'demoMode');
      } else {
        $encodedFields = array('aVersion', 'hVersion', 'fVersion');
      }

      foreach ($encodedFields as $field)
      {
    #      print_r($field . ' = ' . $params[$field] . endl);

        $params[$field] = urldecode($params[$field]);
        $params[$field] = trim( str_replace('%20', ' ', $params[$field]) );
        $params[$field] = preg_replace('/[[:blank:]]{2,}/',' ', $params[$field]);
      }


      $customerID = foo(new DB\OrchestraCustomers())->getCustomerIDByVin($params['vin']);
      if($customerID){
          $customer = new DB\Customers($customerID);
          $customer->setValues($params)->save();

          $ret='success';
          $this->send(array('status' => 'success', 'message' => $ret), 'UpdateInfo');

          if($this->debug_oop==1) {
            $this->logger_oop('log_UpdateInfo_'. date('m_d_Y') .'.txt', array(
                'date'         => date("m_d_Y h:i:s A"),
                'status'       => 'success',
                'message'      => $ret,
                'params'       => $params,
            ));
          }

      } else {
          $ret='Unknown Customer';
          $this->send(array('status' => 'error', 'message' => $ret), 'UpdateInfo');

          if($this->debug_oop==1) {
            $this->logger_oop('log_UpdateInfo_'. date('m_d_Y') .'.txt', array(
                'date'         => date("m_d_Y h:i:s A"),
                'status'       => 'error',
                'message'      => $ret,
                'params'       => $params,
            ));
          }
      }

    }

}

return foo(new API())->handle($_REQUEST);


