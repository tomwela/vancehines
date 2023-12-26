<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core;
use ClickBlocks\DB;
use ClickBlocks\Utils;
use ClickBlocks\Web;
use ClickBlocks\Web\UI\Helpers;
use ClickBlocks\Web\UI\POM;
use ClickBlocks\MVC\VanceAndHines;

class PageCustomers extends Backend
{

    public function __construct()
    {
        parent::__construct('customers/customers.html');

        $this->customer = foo(new DB\ServiceCustomers())->getByID($this->fv['customerID']);
        $this->map      = foo(new DB\ServiceMaps())->getByID($this->customer->currentMap);
        $this->originalMapID = foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($this->customer->customerID);

        // cache
        $this->cache = Core\Register::getInstance()->cache;
        $this->cacheExpire = ini_get('session.gc_maxlifetime');
        if ($this->cacheExpire < CACHE_EXPIRE_DAY) {
            $this->cacheExpire = 2*CACHE_EXPIRE_WEEK; // two weeks
        }

        $this->mediaDir = Core\IO::dir($this->config->dirs['MEDIA']);
    }

    public function access()
    {
        return parent::access();
        //$this->noAccessURL = $this->config->cms . '/login';
        //return $this->customer->customerID;
    }

    public function init()
    {
        parent::init();
        $this->head->name = 'Customers';

        $fields = array('BattVoltage', 'TargetAFR', 'FrontAFR', 'RearAFR', 'ManifoldPressure', 'ThrottlePosition', 'ManifoldAirTemperature', 'EngineRPM', 'WheelSpeedition', 'Front02SensorVoltage', 'Rear02SensorVoltage', 'FrontSparkTiming', 'RearSparkTiming', 'FrontAdaptiveFuel', 'RearAdaptiveFuel');

        $namesLiveData = array('Battery Voltage', 'Air Fuel Ratio (Unavailable)', 'Front AFR (Unavailable)', 'Rear AFR (Unavailable)', 'Manifold Absolute Pressure', 'Throttle Position', 'Intake Air Temperature', 'Engine RPM', 'Wheel Speed', 'Front 02 Sensor Voltage (Unavailable)', 'Rear 02 Sensor Voltage (Unavailable)', 'Front Spark Timing', 'Rear Spark Timing', 'Front Adaptive Fuel % (Unavailable)', 'Rear Adaptive Fuel % (Unavailable)');

        $map = foo(new DB\ServiceCustomers)->getByID($this->fv['customerID'])->maps;
        $this->tpl->vin = $this->customer->vin;
        $this->tpl->nameMap = $map->name;
        $this->tpl->descMap = $map->description;

        $this->get('mapSearch')->otherMaps = true;
        $this->get('mapSearch')->perem = true;
        $this->get('mapSearch')->customerID = $this->customer->customerID;
        $this->get('mapSearch')->currentMap = $this->map->mapID;

        // set view attributes
        $this->get('allMapNotes')->customerID = $this->customer->customerID;
        $this->get('allMapNotes')->mapID      = $this->map->mapID;
        // pass data to html (view)
        $this->get('allMapNotes')->tpl->noteCustomerID = $this->customer->customerID;
        $this->get('allMapNotes')->tpl->noteMapID      = $this->map->mapID;

        $this->tpl->fields        = $fields;
        $this->tpl->namesLiveData = $namesLiveData;
        $this->tpl->mapID         = $this->map->mapID;
        $this->tpl->mYear         = $this->map->year;
        $this->tpl->apiVersion    = $this->map->apiVersion;

        $this->initCustomerDetail();
        $this->updateLastPing();
        $this->updateCurrentMapData();

        if ($this->fv['checkout'] == 1) {
            $this->updateHistory();
        }

        $originalMapID = foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($this->customer->customerID);
        //$this->ShowPopupMessage($originalMapID);

        $originalMap = foo(new DB\ServiceMaps())->getByID($originalMapID);
        //$this->ShowPopupMessage($originalMap->mapID);

        if (!$originalMap->mapID) {
            $this->get('mapSearch')->get('searchMapBlock')->get('originalTuneButton')->addStyle('display', 'none');
        } else {
            $this->get('mapSearch')->get('searchMapBlock')->get('originalTuneButton')->addStyle('display', '');
        }

        //Get a count of all map notes
        $mapNotesCount = foo(new DB\OrchestraMaps())->countMapNotes( $this->customer->customerID );
        $this->tpl->mapNotesCount = $mapNotesCount;

    }

    private function initCustomerDetail()
    {
        $this->tpl->customerID = $this->customer->customerID;

        // Display customer detail
        $panelCustomerDetail = $this->get('customerDetail');
        $panelCustomerDetail->get('customerDetailDisplay')->visible = 1;
        $panelCustomerDetail->get('customerDetailEdit')->visible = 0;
        $this->get('mainform')->get('lnkCustomerEdit')->onclick = "ajax.doit('->editCustomer')";

        // Fill data
        $this->fillCustomerDetailData($this->customer);
    }

    private function fillCustomerDetailData($objCustomer, $isEdit = false)
    {
        $panelCustomerDetail = $this->get('customerDetail');
        $panelData = $panelCustomerDetail->get('customerDetailDisplay');
        $name = 'Label';
        $type = 'text';

        if ($isEdit) {
            $panelData = $panelCustomerDetail->get('customerDetailEdit');
            $name = '';
            $type = 'value';

            $panelData->get('customerCustomerID' . 'Label')->text = $objCustomer->customerID;

            $panelData->get('customerFullName' . $name)->{$type} = $objCustomer->fullName;
            $panelData->get('customerAddress' . $name)->{$type} = $objCustomer->address;
            $panelData->get('customerAddress2' . $name)->{$type} = $objCustomer->address2;
            $panelData->get('customerCity' . $name)->{$type} = $objCustomer->city;
            $panelData->get('customerState' . $name)->{$type} = $objCustomer->state;
            $panelData->get('customerZip' . $name)->{$type} = $objCustomer->zip;
            $panelData->get('customerCountry' . $name)->{$type} = $objCustomer->country;
            $panelData->get('customerPhone' . $name)->{$type} = $objCustomer->phone;
            $panelData->get('customerEmail' . $name)->{$type} = $objCustomer->email;
            $panelData->get('customerRAN' . $name)->{$type} = $objCustomer->RAN;
            $panelData->get('customerCustomerNumber' . $name)->{$type} = $objCustomer->customerNumber;
            $panelData->get('customerContactPreferences' . $name)->{$type} = $objCustomer->contactPreferences;

        } else {

            $panelData->get('customerOriginalMap' . $name)->{$type} = $this->originalMapID;
            $panelData->get('customerCustomerID' . $name)->{$type} = $objCustomer->customerID;
            $panelData->get('customerFullName' . $name)->{$type} = $objCustomer->fullName;
            $panelData->get('customerAddress' . $name)->{$type} = $objCustomer->address;
            $panelData->get('customerAddress2' . $name)->{$type} = $objCustomer->address2;
            $panelData->get('customerCity' . $name)->{$type} = $objCustomer->city;
            $panelData->get('customerState' . $name)->{$type} = $objCustomer->state;
            $panelData->get('customerZip' . $name)->{$type} = $objCustomer->zip;
            $panelData->get('customerCountry' . $name)->{$type} = $objCustomer->country;
            $panelData->get('customerPhone' . $name)->{$type} = $objCustomer->phone;
            $panelData->get('customerEmail' . $name)->{$type} = $objCustomer->email;
            $panelData->get('customerMake' . $name)->{$type} = $objCustomer->make;
            $panelData->get('customerModel' . $name)->{$type} = $objCustomer->model;
            $panelData->get('customerYear' . $name)->{$type} = $objCustomer->year;
            $panelData->get('customerVin' . $name)->{$type} = $objCustomer->vin;
            $panelData->get('customerEcmFirmware' . $name)->{$type} = $objCustomer->ecmFirmware;
            $panelData->get('customerEcmPart' . $name)->{$type} = $objCustomer->ecmPart;
            $panelData->get('customerFVersion' . $name)->{$type} = $objCustomer->fVersion;
            $panelData->get('customerHVersion' . $name)->{$type} = $objCustomer->hVersion;
            $panelData->get('customerAVersion' . $name)->{$type} = $objCustomer->aVersion;
            $panelData->get('customerIPAddress' . $name)->{$type} = $objCustomer->ipAddress;
            $panelData->get('customerCalID' . $name)->{$type} = $objCustomer->calID;
            $panelData->get('customerRAN' . $name)->{$type} = $objCustomer->RAN;
            $panelData->get('customerCustomerNumber' . $name)->{$type} = $objCustomer->customerNumber;
            $panelData->get('customerContactPreferences' . $name)->{$type} = $objCustomer->contactPreferences;
        }

        $this->get('liveData.liveDataConnect.customerMakeLabel')->text = $objCustomer->make;
        $this->get('liveData.liveDataConnect.customerModelLabel')->text = $objCustomer->model;
        $this->tpl->buttonName = 'Connect';


    } //updateCurrentMapData

    public function updateLastPing()
    {
        // Update lastPing for blocking customer
        foo(new DB\OrchestraCustomers())->updateLastPing($this->customer->customerID);
    }

    public function updateCurrentMapData()
    {
        $map = $this->map;
        $mapStructure = getMapStructure( $this->customer->ecmFirmware );


        $tableInfo = array();
        $groupList = array();

        foreach ($mapStructure as $k => $v) {
            unset($mPart);
            $tableName = '\ClickBlocks\DB\Orchestra' . $v['DBTableName'];
            $mPart = foo(new $tableName)->getByQuery('mapID = ' . $map->mapID, null, null, 'row');
            //$val = 'cm' . $k . 'Updated';
            //$this->tpl->$val = date('m/d/Y', strtotime($mPart['updated']));


            $groupList[ $k ] = $v['tablegroup'];

            //dynamically create an array of mapStructure and db related info and pass to customers.html
            $tableInfo[ $k ]['name'] = $k;
            $tableInfo[ $k ]['group'] = $v['tablegroup'];
            $tableInfo[ $k ]['dataType'] = $v['dataType'];
            $tableInfo[ $k ]['title'] = $v['title'];
            $tableInfo[ $k ]['ShowInCSR'] = $v['ShowInCSR'];

            //If there is no date in the db, the date function will return dec 1969 which is not what we want.
            //Only pass records that have dates.
            //This field is used to filter tables displayed in the customer detail page.
            if (isset($mPart['updated'])) {
                $tableInfo[ $k ]['Updated'] = date('m/d/Y', strtotime($mPart['updated']));
            }

        } //foreach

        //pass data to customers.html -> variable name is  $tableInfo which is an array
        //ksort($tableInfo);
        $info = 'tableInfo';
        $this->tpl->$info = $tableInfo;

        $groupInfo = 'groupList';
        $this->tpl->$groupInfo = array_unique($groupList);

        /*
            foreach(array_unique($groupList) as $index => $value){
              echo $value ."<br />";

            }
        */

    }

    public function updateHistory()
    {
        $saveData = foo(new DB\ServiceHistory())->getByID($id);
        $saveData->mapID = $this->map->mapID;
        $saveData->customerID = $this->customer->customerID;
        $saveData->date = 'NOW()';
        $saveData->userID = $this->user->userID;
        $saveData->save();
        $this->get('csrHIstory')->update();
    }

    public function editCustomer()
    {
        // Display customer detail
        $panelCustomerDetail = $this->get('customerDetail');

        if ($panelCustomerDetail->get('customerDetailEdit')->visible) {
            return;
        }

        $panelCustomerDetail->get('customerDetailDisplay')->visible = 0;
        $panelCustomerDetail->get('customerDetailEdit')->visible = 1;

        // Fill customer detail
        $this->fillCustomerDetailData($this->customer, true);
    }

    public function connectLiveData($buttonName)
    {

        if ($buttonName == 'Connect') {
            $this->tpl->buttonName = 'Disconnect';
            $vin = $this->customer->vin;
            $cachedData = $this->cache->get($vin);
            $cachedData['LiveData'] = 1;
            $this->cache->delete($vin);
            $this->cache->set($vin, $cachedData, $this->cacheExpire);
//      $_SESSION['__VH__']['LiveData'][$vin]['started'] = 1;
            $this->ajax->script("setTimeout(function(){ ajax.doit('->updateLiveData')}, 500)");
        } else {
            $this->tpl->buttonName = 'Connect';
            $vin = $this->customer->vin;
            $cachedData = $this->cache->get($vin);
            unset($cachedData['LiveData']);
            $this->cache->delete($vin);
            $this->cache->set($vin, $cachedData, $this->cacheExpire);
        }
        $this->get('liveDataConnect')->update();
    }

    public function updateLiveData()
    {
        $vin = $this->customer->vin;
        $cachedData = $this->cache->get($vin);
        if (!$cachedData['LiveData']) {
            return;
        }
        $this->tpl->cachedData = $cachedData;
        $this->ajax->script("setTimeout(function(){ ajax.doit('->updateLiveData')}, 500)");
        $this->get('liveDataConnect')->update();
        $this->get('liveDataTable')->update();
    }

    public function cancelEditCustomer()
    {
        // Display customer detail
        $panelCustomerDetail = $this->get('customerDetail');
        $panelCustomerDetail->get('customerDetailDisplay')->visible = 1;
        $panelCustomerDetail->get('customerDetailEdit')->visible = 0;
    }

    public function saveCustomer($fv)
    {
        if (!$this->validators->isValid('customerEdit')) {
            return;
        }

        // Update
        $customer = foo(new DB\ServiceCustomers())->getByID($fv['customerID']);

        if ($customer->customerID) {
            //$customer->customerID  = $fv['customerCustomerID'];
            $customer->fullName = trim($fv['customerFullName']);
            $customer->address = trim($fv['customerAddress']);
            $customer->address2 = trim($fv['customerAddress2']);
            $customer->city = trim($fv['customerCity']);
            $customer->state = trim($fv['customerState']);
            $customer->zip = trim($fv['customerZip']);
            $customer->country = trim($fv['customerCountry']);
            $customer->phone = trim($fv['customerPhone']);
            $customer->email = trim($fv['customerEmail']);
            $customer->contactPreferences = trim($fv['customerContactPreferences']);

// Since the following fields are commented out in html to prevent editing,
// they must be commented out here or else they will nullify other fields in the database!
//      $customer->make      = $fv['customerMake'];
//      $customer->model     = $fv['customerModel'];
//      $customer->year      = $fv['customerYear'];
//      $customer->vin       = $fv['customerVin'];
//      $customer->ecmFirmware = $fv['customerEcmFirmware'];
//      $customer->ecmPart   = $fv['customerEcmPart'];
//      $customer->fVersion  = $fv['customerFVersion'];
//      $customer->hVersion  = $fv['customerHVersion'];
//      $customer->aVersion  = $fv['customerAVersion'];
//      $customer->ipAddress = $fv['customerIPAddress'];
//      $customer->calID     = $fv['customerCalID'];
            $customer->RAN = trim($fv['customerRAN']);
            $customer->customerNumber = trim($fv['customerCustomerNumber']);
            $customer->save();

            // Fill data label
            $this->fillCustomerDetailData($customer);
        }

        $this->updateHistory();

        // Display customer detail
        $panelCustomerDetail = $this->get('customerDetail');
        $panelCustomerDetail->get('customerDetailDisplay')->visible = 1;
        $panelCustomerDetail->get('customerDetailEdit')->visible = 0;
    }

    public function checkEmail()
    {
        $email = $panelCustomerDetail = $this->get('customerDetail')->get('customerDetailEdit')->get('customerEmail')->value;
        if ($this->customer->email == $email) {
            return true;
        }
        if (!foo(new DB\OrchestraCustomers())->isEmailUnique($email)) {
            $this->ajax->script('controls.focus(\'' . $this->get('customerEmail')->uniqueID . '\')');

            return false;
        }

        return true;
    }

    public function showPopupWithValues($valKey, $otherMapID = null)
    {
        if (!$otherMapID) {
            $mapID = $this->map->mapID;
        } else {
            $mapID = $otherMapID;
        }

        $m = foo(new DB\ServiceMaps())->getByID($mapID);
        $mStructure = getMapStructure( $this->customer->ecmFirmware );
        $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];
        $value = foo(new $orchestraName)->getByQuery('mapID = ' . $mapID, null, null, 'row');
        $data = json_decode($value['data'], 1);
        //$this->ShowPopupMessage($data);
        //return;
        //var_dump($data);


        // count all elements of a multidimensional array
        $thisNumbCols = count($data);
        $thisNumbRows = (count($data, 1) - $thisNumbCols) / $thisNumbCols;
        $gridSize = $thisNumbRows ."x". $thisNumbCols;
        //$this->ShowPopupMessage($gridSize);
        //return;



        //bug fix - force data type
        if (is_float($data) || is_int($data) || is_numeric($data)) {
            settype($data, "string");
        }

        $dot = (is_float($mStructure[ $valKey ]['min']) || is_float($mStructure[ $valKey ]['max'])) ? '.' : '';

        $minus = ($mStructure[ $valKey ]['min'] < 0 || $mStructure[ $valKey ]['max'] < 0) ? '-' : '';
        $expression = "/[^0-9{$dot}{$minus}]/g";


        //FLAT
        if ($mStructure[ $valKey ]['dataType'] == 'flat') {
            $popup = $this->get('mapSingleInput');
            $popup->tpl->enDisID = reset($value);
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            //$popup->tpl->base = $mStructure[ $valKey ]['default'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];
            $popup->get('popupContainer')->get('reg_currValue')->onkeyup = "this.value = this.value.replace($expression,'');";
            $popup->get('popupContainer')->get('reg_currValue')->value = $data;
            $popup->get('popupContainer')->get('reg_currValue')->removeClass('error');
            $popup->get('saveButton')->visible = 1;
            $popup->get('compareLink')->visible = 1;
            $popup->get('popupContainer')->get('reg_currValue')->readonly = false;
            if ($otherMapID) {
                $popup->get('saveButton')->visible = 0;
                $popup->get('popupContainer')->get('reg_currValue')->readonly = true;
                $popup->get('compareLink')->visible = 0;
            } else {
                $this->ajax->script("$('#{$popup->get('popupContainer')->get('reg_currValue')->uniqueID}').select();", 100);
            }
        } //TABLE
        elseif ($mStructure[ $valKey ]['dataType'] == 'table') {

            $popup = $this->get('mapMultiInput');
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->yAxisTitle = $mStructure[ $valKey ]['table']['titles']['title'];
            $popup->tpl->valTitle = $mStructure[ $valKey ]['table']['values']['title'];
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];

            $popup->tpl->onKeyUpScript = "this.value = this.value.replace($expression,'');";
            $popup->tpl->maxLength = strlen($mStructure[ $valKey ]['max']) + 2;
            $popup->tpl->rows = $data;
            $popup->get('saveButton')->visible = 1;
            $popup->get('compareLink')->visible = 1;
            $this->ajax->script("$('.multiInputData').removeAttr('readonly')");

            if ($otherMapID) {
                $popup->get('saveButton')->visible = 0;
                $this->ajax->script("$('.multiInputData').attr('readonly', 'readonly')");
                $popup->get('compareLink')->visible = 0;
            } else {
                $this->ajax->script("$('.multiInputData').first().select();", 100);
            }

        } // MATRIX
        elseif ($mStructure[ $valKey ]['dataType'] == 'matrix') {
            $popup = $this->get('mapSSInput');

            $popup->get('popupContainer.xHeader')->text = $mStructure[ $valKey ]['axis']['X']['title'];
            $popup->get('popupContainer.xHeader')->update();
            $popup->get('popupContainer.yHeader')->text = $mStructure[ $valKey ]['axis']['Y']['title'];
            $popup->get('popupContainer.yHeader')->update();
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title    = $mStructure[ $valKey ]['title'];
            $popup->tpl->min      = $mStructure[ $valKey ]['min'];
            $popup->tpl->max      = $mStructure[ $valKey ]['max'];

            $popup->tpl->gridSize = $gridSize;




            if (!$value) {
                $dataThr = '{" 0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"2.5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}," 5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"35":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"45":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"60":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
                $data = json_decode($dataThr, 1);
            }
            $popup->tpl->rows = $data;


            $popup->tpl->arrMatrixCol  = array_keys($data);
            $arrMatrixRow              = array_values($data);
            $popup->tpl->arrMatrixRow  = array_keys($arrMatrixRow[0]);
            $popup->tpl->onKeyUpScript = "this.value = this.value.replace($expression,'');";
            ($mStructure[ $valKey ]);

            $popup->tpl->maxLength = strlen($mStructure[ $valKey ]['max']) + 2;

            $popup->get('popupContainer')->get('valCols')->value = implode(',', $popup->tpl->arrMatrixCol);
            $popup->get('saveButton')->visible = 1;
            $popup->get('compareLink')->visible = 1;
            $this->ajax->script("$('.ssInputData').removeAttr('readonly')");

            $this->ajax->script("$.getScript('/Application/Backend/common/jetColorMap.min.js');");

            if ($otherMapID) {
                $popup->get('saveButton')->visible = 0;
                $this->ajax->script("$('.ssInputData').attr('readonly', 'readonly')");
                $this->ajax->script("$('.ssInputDataTd').attr('onclick','').unbind('click')", 100);
                $popup->get('compareLink')->visible = 0;

                $popup->get('shiftClick')->visible = 0;
            } else {
                $this->ajax->script("$.getScript('/Application/Backend/common/shiftClick.min.js');");
                $this->ajax->script("$('.ssInputData').first().select();", 100);

                $popup->get('shiftClick')->visible = 1;
            } // else

        } //elseif

        $originalMapID = foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($this->customer->customerID);
        $originalMap = foo(new DB\ServiceMaps())->getByID($originalMapID);
        $popup->get('saveButton')->get('restoreOriginalLink')->onclick = "ajax.doit('->restoreToOriginal', '" . $valKey . "');";
        $popup->get('saveButton')->get('restoreOriginalLink')->update();

        if ($originalMap->mapID == $this->customer->currentMap) {
            $popup->get('saveButton')->get('restoreOriginalLink')->visible = 0;
        } else {
            $popup->get('saveButton')->get('restoreOriginalLink')->visible = 1;
        }

        $popup->get('popupContainer')->get('valKey')->value = $valKey;
        $popup->show();

    } //showPopupWithValues

    /**
     *  "Save" Button Method for "table" dataTypes.
     */
    public function saveMapMultiInput($vs)
    {
        $popup = $this->get('mapMultiInput');
        $popup->get('compareLink')->visible = 1;

        // Get params
        $valKey = $popup->get('popupContainer')->get('valKey')->value;
        $arrData = array_combine($vs['mapMultiInputKeys'], $vs['mapMultiInputValues']);

        $mStructure = getMapStructure( $this->customer->ecmFirmware );

        //data validation
        if (!$this->validateMapMultiInput(array(
            'min' => $mStructure[ $valKey ]['min'],
            'max' => $mStructure[ $valKey ]['max']
        ), $arrData)
        ) {
            return;
        }


        // Cam Key processing & Save to sector 94
        if ($mStructure[ $valKey ]['DBTableName'] == 'IntakeValveOpen') {
            $camData['CAMKey'] = 999;
            $camData['CAMIntakeValveOpenFront'] = $this->camIntakeLookup($arrData);

            foreach ($camData as $key => $val) {
                $orchestraClass = '\ClickBlocks\DB\Orchestra' . $key;
                $serviceClass = '\ClickBlocks\DB\Service' . $key;

                $id = foo(new $orchestraClass)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');

                $saveData = foo(new $serviceClass)->getByID($id);
                $saveData->mapID = $this->map->mapID;
                $saveData->data = json_encode($val);
                $saveData->updated = 'NOW()';
                $saveData->updatedBy = $this->user->userID;
                $saveData->save();
            }

        } elseif ($mStructure[ $valKey ]['DBTableName'] == 'IntakeValveClose') {
            $camData['CAMKey'] = 999;
            $camData['CAMIntakeValveCloseFront'] = $this->camIntakeLookup($arrData);

            foreach ($camData as $key => $val) {
                $orchestraClass = '\ClickBlocks\DB\Orchestra' . $key;
                $serviceClass = '\ClickBlocks\DB\Service' . $key;

                $id = foo(new $orchestraClass)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');

                $saveData = foo(new $serviceClass)->getByID($id);
                $saveData->mapID = $this->map->mapID;
                $saveData->data = json_encode($val);
                $saveData->updated = 'NOW()';
                $saveData->updatedBy = $this->user->userID;
                $saveData->save();
            }
        }

        $orchestraClass = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];
        $serviceClass = '\ClickBlocks\DB\Service' . $mStructure[ $valKey ]['DBTableName'];

        $id = foo(new $orchestraClass)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');

        $saveData = foo(new $serviceClass)->getByID($id);
        $saveData->mapID = $this->map->mapID;
        $saveData->data = json_encode($arrData);
        $saveData->updated = 'NOW()';
        $saveData->updatedBy = $this->user->userID;
        $saveData->save();

        $popup->hide();
        $this->updateHistory();
    }

    protected function validateMapMultiInput($arrInfo, $arr)
    {
        $flag = true;
        $i = 0;
        foreach ($arr as $value) {
            if ($value < $arrInfo['min'] || $value > $arrInfo['max'] || !is_numeric($value) || strpos($value, '.') && $value[ (strpos($value, '.') + 1) ] == '') {
                $this->ajax->script("$($('.multiInputData')[$i]).addClass('error');");
                $flag = false;
            }
            $i++;
        }
        if (!$flag) {
            $this->ShowPopupMessage("Value must be between {$arrInfo['min']} to {$arrInfo['max']}.");
        }

        return $flag;
    }

    protected function ShowPopupMessage($msg)
    {
        $popup = $this->get('msg');
        $popup->tpl->text = $msg;
        $popup->get('btnYes')->visible = 0;
        $popup->get('btnNo')->visible = 0;
        //$popup->show(0.5, true, 200, 2000);
        $popup->show(0.5, true, 200, 3000);
    }

    private static function camIntakeLookup($val)
    {

        if ($val['Open Front']) {
            $front = $val['Open Front'];

            // must add 25 to each return to compensate for negative numbers
            if ($front < 67.5) {
                return 56 + 25;

            } elseif ($front >= 67.5 && $front < 78.75) {
                //return ((45-34)/2) + 34;
                return 40 + 25;

            } elseif ($front >= 78.75 && $front < 90) {
                //return ((33-23)/2) + 23;
                return 28 + 25;

            } elseif ($front >= 90 && $front < 101.25) {
                //return ((22-12)/2) + 12;
                return 17 + 25;

            } elseif ($front >= 101.25 && $front < 112.5) {
                //return ((11-1)/2) + 1;
                return 6 + 25;

            } elseif ($front >= 112.5 && $front < 123.75) {
                //return ((0-10)/2 - 0;
                return -5 + 25;

            } elseif ($front >= 123.75) {
                //return ((11-20)/2 - 11;
                return -15 + 25;
            }

        } elseif ($val['Close Front']) {
            $close = $val['Close Front'];

            if ($close < 326) {
                // return (abs(10-22)/2) + 10;
                return 16 + 25;

            } elseif ($close >= 326 && $close < 338) {
                // return (abs(23-33)/2) + 23;
                return 28 + 25;

            } elseif ($close >= 338 && $close < 349) {
                // return (abs(34-44)/2) + 34;
                return 39 + 25;

            } elseif ($close >= 349) {
                // return (abs(45-90)/2) + 45;
                return 68 + 25;
            }

        }
    }

    public function saveMapSSInput($vs)
    {
        $popup = $this->get('mapSSInput');

        // Get params
        $valKey = $popup->get('popupContainer')->get('valKey')->value;

        $mStructure = getMapStructure( $this->customer->ecmFirmware );

        /*
            if($valKey == 'ThrtottleProgrsivity')
            {
              $ThrProgArray = array('ThrtottleProgrsivity','ThrtottleProgrsivity2');
              $arrData = array();
              $arrCols = array();
              $arrColsThrProg = array();
              $arrDataThrProg = array();
              foreach ($ThrProgArray as $valKey) {
                if($valKey == 'ThrtottleProgrsivity')
                {
                  $arrCols = explode(',', $vs['valCols']);
                  foreach ($arrCols as $col)
                  {
                    $arrData[$col] = array_combine($vs["mapSSInputKeys_{$col}"], $vs["mapSSInputValues_{$col}"]);
                  }
                }
                else
                {
                  $arrColsThrProg = explode(',', $vs['valColsThrProg']);
                  foreach ($arrColsThrProg as $col)
                  {
                    $arrDataThrProg[$col] = array_combine($vs["mapSSInputKeysThrProg_{$col}"], $vs["mapSSInputValuesThrProg_{$col}"]);
                  }
                }
              }
              if (!$this->validateMapSSInput(array(
                          'min' => $mStructure[$valKey]['min'],
                          'max' => $mStructure[$valKey]['max']
                              ),$arrCols, $arrData, $arrColsThrProg, $arrDataThrProg, $valKey))
                return;
              foreach ($ThrProgArray as $valKey){
                $orchestraClass = '\ClickBlocks\DB\Orchestra' . $mStructure[$valKey]['DBTableName'];
                $serviceClass = '\ClickBlocks\DB\Service' . $mStructure[$valKey]['DBTableName'];
                $id = foo(new $orchestraClass)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');
                $saveData = foo(new $serviceClass)->getByID($id);
                $saveData->mapID = $this->map->mapID;
                if($valKey == 'ThrtottleProgrsivity') {
                  $saveData->data = json_encode($arrData);
                }
                else{
                  $saveData->data = json_encode($arrDataThrProg);
                }
                $saveData->updated = 'NOW()';
                $saveData->updatedBy = $this->user->userID;
                $saveData->save();
              }
            }
            else
            {
        */
        $arrData = array();
        $arrCols = explode(',', $vs['valCols']);
        foreach ($arrCols as $col) {
            $arrData[ $col ] = array_combine($vs["mapSSInputKeys_{$col}"], $vs["mapSSInputValues_{$col}"]);
        }

        if (!$this->validateMapSSInput(array(
            'min' => $mStructure[ $valKey ]['min'],
            'max' => $mStructure[ $valKey ]['max']
        ), $arrCols, $arrData)
        ) {
            return;
        }
        $orchestraClass = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];
        $serviceClass = '\ClickBlocks\DB\Service' . $mStructure[ $valKey ]['DBTableName'];

        $id = foo(new $orchestraClass)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');
        $saveData = foo(new $serviceClass)->getByID($id);
        $saveData->mapID = $this->map->mapID;
        $saveData->data = json_encode($arrData);
        $saveData->updated = 'NOW()';
        $saveData->updatedBy = $this->user->userID;
        $saveData->save();
        /*     } */

        $popup->hide();
        $this->updateHistory();

    }

    protected function validateMapSSInput($arrInfo, $arrCols, $arrData, $arrColsThrProg = null, $arrDataThrProg = null, $valKey = null)
    {
        $flag = true;
        $i = 0;
        foreach ($arrCols as $col) {
            $j = 0;
            foreach ($arrData[ $col ] as $val) {

                if (isset($arrInfo['min']) && $arrInfo['min'] > $val || isset($arrInfo['max']) && $arrInfo['max'] < $val || !is_numeric($val) || $val === '-0' || strpos($val, '.') && $val[ (strpos($val, '.') + 1) ] == '') {
                    $pos = $i + $j * count($arrCols);
                    $this->ajax->script("$($('.ssInputData')[$pos]).show().addClass('error');");
                    $this->ajax->script("$($('.ssInputData')[$pos]).prev('span').hide();");
                    $flag = false;
                }
                $j++;
            }
            $i++;
        }
        if ($arrColsThrProg) {
            $i = 0;
            foreach ($arrColsThrProg as $col) {
                $j = 0;
                foreach ($arrDataThrProg[ $col ] as $val) {
                    if (isset($arrInfo['min']) && $arrInfo['min'] > $val || isset($arrInfo['max']) && $arrInfo['max'] < $val || !is_numeric($val) || $val === '-0' || strpos($val, '.') && $val[ (strpos($val, '.') + 1) ] == '') {
                        $pos = $i + $j * count($arrCols);
                        $this->ajax->script("$($('.ssInputDataThrProg')[$pos]).show().addClass('error');");
                        $this->ajax->script("$($('.ssInputDataThrProg')[$pos]).prev('span').hide();");
                        $flag = false;
                    }
                    $j++;
                }
                $i++;
            }
        }
        if (!$flag) {
            if (isset($arrInfo['min']) && isset($arrInfo['max'])) {
                $this->ShowPopupMessage("Value must be between {$arrInfo['min']} to {$arrInfo['max']}.");
            }
        }

        return $flag;
    }

    public function saveMapSingleInput()
    {
        if (!$this->validators->isValid()) {
            return false;
        }
        $popup = $this->get('mapSingleInput');
        $valKey = $popup->get('popupContainer')->get('valKey')->value;
        $singleData = $this->get('popupContainer.reg_currValue')->value;

        $mStructure = getMapStructure( $this->customer->ecmFirmware );

        if (!$this->validateMapSingleInput(array(
            'min'    => $mStructure[ $valKey ]['min'],
            'max'    => $mStructure[ $valKey ]['max'],
            'sigdig' => $mStructure[ $valKey ]['sigdig']
        ), $singleData)
        ) {
            return;
        }

        $tableName = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];
        $tableName1 = '\ClickBlocks\DB\Service' . $mStructure[ $valKey ]['DBTableName'];

        $ids = foo(new $tableName)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');
        $saveData = foo(new $tableName1)->getByID($ids);
        //$saveData->ids = $popup->tpl->enDisID;
        $saveData->mapID = $this->map->mapID;
        $saveData->data = json_encode($singleData);
        $saveData->updated = 'NOW()';
        $saveData->updatedBy = $this->user->userID;
        //print_r($saveData->getValues());
        $saveData->save();
        $this->get('mapSingleInput')->hide();
        $this->updateHistory();
    }

    protected function validateMapSingleInput($arrInfo, $value)
    {
        // validation for flat

        $flag = true;

        if ($value < $arrInfo['min']) {
            $this->ShowPopupMessage('Current value should not be less than Min');
            $flag = false;
        } elseif ($value > $arrInfo['max']) {
            $this->ShowPopupMessage('Current value should not be more than Max');
            $flag = false;
        }

        // Check:  make sure we have a number
        //  But this is secondary to JS validation on the page!
        if (!is_numeric($value) || $value === '-0') {
            $this->ShowPopupMessage('Current value should be a number');
            $flag = false;
        }

        // Ruben Sep 2015
        // check number of significant digits if value contains a decimal
        if (strpos($value, '.')) {

            // number of total digits
            $sl = strlen($value);

            // position of decimal point
            $sp = strpos($value, '.') + 1;

            // number of digits after decimal point
            $digitsAfterDecimal = abs($sl - $sp);

            if ($digitsAfterDecimal > $arrInfo['sigdig']) {
                $this->ShowPopupMessage("Please limit value to {$arrInfo['sigdig']} decimal places.");
                $flag = false;
            }

        } //significant digits

        if (!$flag) {
            $popupContainer = $this->get('mapSingleInput')->get('popupContainer');
            $popupContainer->get('reg_currValue')->addClass('error');
            $this->ajax->script("$('#{$popupContainer->get('reg_currValue')->uniqueID}').select();", 100);
        }

        return $flag;
    }

    public function restoreToOriginal($valKey)
    {
        // get Customer's original Map ID
        $mapID = foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($this->customer->customerID);
        if (!$mapID) {
            $this->ShowPopupMessage('This customer has no original map.');

            return;
        }

        // no need to test as it always returns a value
        $m = foo(new DB\ServiceMaps())->getByID($mapID);

        // get map structure by year and model
        $mStructure = getMapStructure( $this->customer->ecmFirmware );

        // $valKey is passed in
        $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];

        // db query to retrieve data for this table
        $value = foo(new $orchestraName)->getByQuery('mapID = ' . $mapID, null, null, 'row');

        // decode json that was stored in the db
        $data = json_decode($value['data'], 1);

        if ($mStructure[ $valKey ]['dataType'] == 'flat') {
            $popup = $this->get('mapSingleInput');
            $popup->tpl->enDisID = reset($value);
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            //$popup->tpl->base = $mStructure[ $valKey ]['default'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];


            if ($popup->get('popupContainer')->get('reg_currValue')->value) {
                $popup->get('popupContainer')->get('reg_currValue')->value = $data;
            }

            if ($popup->get('popupContainer')->get('saveButton')->visible) {
                $popup->get('popupContainer')->get('saveButton')->visible = 1;
            }

            if ($popup->get('popupContainer')->get('compareLink')->visible) {
                $popup->get('popupContainer')->get('compareLink')->visible = 1;
            }

            if ($popup->get('popupContainer')->get('reg_currValue')->readonly) {
                $popup->get('popupContainer')->get('reg_currValue')->readonly = false;
            }

        } elseif ($mStructure[ $valKey ]['dataType'] == 'table') {

            $popup = $this->get('mapMultiInput');
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['DBTableName'];
            $this->tpl->maxLength = strlen((string)$mStructure[ $valKey ]['max']);
            $popup->tpl->rows = $data;
            $popup->get('saveButton')->visible = 1;
            $popup->get('compareLink')->visible = 1;
            $this->ajax->script("$('.multiInputData').removeAttr('readonly')");

        } elseif ($mStructure[ $valKey ]['dataType'] == 'matrix') {
            // matrix is mapSSInput
            $popup = $this->get('mapSSInput');

            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['DBTableName'];

            if (!$value) {
                $dataThr = '{" 0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"2.5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}," 5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"35":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"45":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"60":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
                $data = json_decode($dataThr, 1);
            }

            $popup->tpl->rows = $data;
            $popup->tpl->arrMatrixCol = array_keys($data);
            $arrMatrixRow = array_values($data);
            $popup->tpl->arrMatrixRow = array_keys($arrMatrixRow[0]);
            $popup->get('popupContainer')->get('valCols')->value = implode(',', $popup->tpl->arrMatrixCol);

            // after a restore is complete, load the js resource again or else it wont work
            $this->ajax->script("$.getScript('/Application/Backend/common/jetColorMap.min.js');");
            $this->ajax->script("$.getScript('/Application/Backend/common/shiftClick.min.js');");
        }


        if ($popup->get('valKey')->value) {
            $popup->get('valKey')->value = $valKey;
        }

        $popup->get('popupContainer')->get('valKey')->value = $valKey;
        $popup->tpl->valKey = $valKey;
        if ($popup->get('popupContainer')) {
            $popup->get('popupContainer')->update();
        }
#    $popup->hide();
#    $popup->update();
#    $popup->show();
    }


    
public function copyTableFromMapID($valKey, $sourceMap)
    {
        /*  $valKey:  VEFrontCyl or VERearCyl
         *  $this->map->mapID    the map ID of the record being viewed (current map)
         *  $sourceMap  is the map entered into the input
         *  $mapID     is the same mapID as $sourceMap
         *
         */

            $this->ShowPopupMessage('JEY!');

            return;
       

     

      
    }




    public function editMap()
    {
        $map = $this->map;
        $this->get('editInputMap')->value = $map->name;
        $this->get('mapDescription')->text = $map->description;
        $this->get('editMaps1')->update()->visible = 1;
        $this->get('editMaps')->update()->visible = 0;
        //$this->ajax->script("var text = $('#" . $this->get('mapDescription')->uniqueID . "').val(); $('#" . $this->get('mapDescription')->uniqueID . "').val(text.replace(/<br \/>/g, \"\r\n\"));");
        $this->ajax->script('replaceMapDescription();');
    }

    public function editSendMap()
    {
        $this->get('editSendMap')->update()->visible = 1;
    }


    public function editSendOtherMap()
    {
        $this->get('editSendOtherMap')->update()->visible = 1;
    }

    public function saveMap($id = null)
    {
        $map = $this->map;
        $map->name = $this->get('editInputMap')->value;
        $map->description = str_replace("\n", "", nl2br($this->get('mapDescription')->text));
        if (strlen($map->description) > 65) {
            $this->ShowPopupMessage("Please limit the description to 65 characters.");
            return;
        }

        $map->updatedBy = $this->user->userID;
        foo(new DB\ServiceMaps())->save($map);
        $this->get('editMaps1')->update();
        $this->get('editMaps1')->update()->visible = 0;
        $this->get('editMaps')->update()->visible = 1;
        $this->tpl->nameMap = $this->map->name;
        $this->tpl->descMap = $this->map->description;
        $this->updateHistory();
    }

    public function cancelEditMap()
    {
        $this->get('editMaps1')->update()->visible = 0;
        $this->get('editMaps')->update()->visible = 1;
    }

    public function cancelSendMap()
    {
        $this->get('editSendMap')->update()->visible = 0;
    }

    public function cancelSendOtherMap()
    {
        $this->get('editSendOtherMap')->update()->visible = 0;
    }

    public function updateTab($index)
    {
        $this->tpl->activetab = $index;

        return true;
    }


    public function showTablesOtherMap($mapID)
    {
        $this->get('mapSearch')->visible = 0;
        $this->get('otherMapsTable')->visible = 1;

        $map = foo(new DB\ServiceMaps)->getByID($mapID);
        $this->tpl->otherMapName       = $map->name;
        $this->tpl->otherMapDesc       = $map->description;
        $this->tpl->otherMapID         = $mapID;
        $this->tpl->otherMapYear       = $map->year;
        $this->tpl->otherMapApiVersion = $map->apiVersion;

        $mapStructure = getMapStructure( $this->customer->ecmFirmware );


        $omTableInfo = array();
        $groupList = array();
        foreach ($mapStructure as $k => $v) {
            unset($mPart);
            $tableName = '\ClickBlocks\DB\Orchestra' . $v['DBTableName'];
            $mPart = foo(new $tableName)->getByQuery('mapID = ' . $map->mapID, null, null, 'row');
            //$val = 'om' . $k . 'Updated';
            //$this->tpl->$val = date('m/d/Y', strtotime($mPart['updated']));


            $groupList[ $k ] = $v['tablegroup'];

            //dynamically create an array of mapStructure and db related info and pass to customers.html
            $omTableInfo[ $k ]['name'] = $k;
            $omTableInfo[ $k ]['group'] = $v['tablegroup'];
            $omTableInfo[ $k ]['dataType'] = $v['dataType'];
            $omTableInfo[ $k ]['title'] = $v['title'];
            $omTableInfo[ $k ]['ShowInCSR'] = $v['ShowInCSR'];


            //If there is no date in the db, the date function will return dec 1969 which is not what we want.
            //Only pass records that have actual dates.
            //This field is used to filter tables displayed in the customer detail page.
            if (isset($mPart['updated'])) {
                $omTableInfo[ $k ]['Updated'] = date('m/d/Y', strtotime($mPart['updated']));
            }

        } //foreach

        //pass data to customers.html -> variable name is  $omTableInfo which is an array
        //ksort($omTableInfo);
        $info = 'omTableInfo';
        $this->tpl->$info = $omTableInfo;

        $groupInfo = 'groupList';
        $this->tpl->$groupInfo = array_unique($groupList);

    }


    public function backTootherMaps()
    {
        $this->get('mapSearch')->visible = 1;
        $this->get('otherMapsTable')->visible = 0;
    }

    public function restoreOriginalMap()
    {
        $originalMapID = foo(new DB\OrchestraMaps())->getOriginalMapForCustomer($this->customer->customerID);
        //$originalMap = foo(new DB\ServiceMaps())->getByID($originalMapID);
        if (!$originalMapID) {
            $this->ShowPopupMessage('This customer has no original map.');

            return;
        }

        $mapStructure = getMapStructure( $this->customer->ecmFirmware );


        foreach ($mapStructure as $pName => $pValue) {
            unset($mPart);
            $orchestraName = '\ClickBlocks\DB\Orchestra' . $pValue['DBTableName'];
            $serviceName = '\ClickBlocks\DB\Service' . $pValue['DBTableName'];
            $mPartArray = foo(new $orchestraName)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');
            $mPart = foo(new $serviceName)->getByID(reset($mPartArray));
            $mPartArrayOriginal = foo(new $orchestraName)->getByQuery('mapID = ' . $originalMapID, null, null, 'row');
            $mPartOriginal = foo(new $serviceName)->getByID(reset($mPartArrayOriginal));
            $mPart->data = $mPartOriginal->data;
            $mPart->updated = 'NOW()';
            $mPart->updatedBy = $this->user->userID;
            foo(new $serviceName)->save($mPart);
        }
        $popup = $this->get('msg');
        $popup->tpl->text = "Original tune has been restored.";
        $popup->get('btnYes')->visible = 0;
        $popup->get('btnNo')->visible = 0;
        $popup->show(0.5, true, 200, 2000);
        $this->updateCurrentMapData();
        $this->ajax->script('location.reload();');
    }


    public function slotZeroRestore()
    {
        $mediaDir = $this->mediaDir;

        $originalMapID = $this->originalMapID;
        if (!$originalMapID) {
            $this->ShowPopupMessage('This customer has no original map in the database.');
            return;
        }

        $originalMapModel = foo(new DB\ServiceMaps())->getByID( $originalMapID );

        $originalMapFile  = $mediaDir ."/". $originalMapModel->year ."/". $originalMapID .".map";
        $choppedFile1     = $mediaDir ."/out/". $originalMapID .".map__chopped1";
        $choppedFile2     = $mediaDir ."/out/". $originalMapID .".map__chopped2";
        $temporaryFile1   = $mediaDir ."/out/". $this->customer->vin ."_temporary1.txt";
        $temporaryFile2   = $mediaDir ."/out/". $this->customer->vin ."_temporary2.txt";
        $finalMapFile     = $mediaDir ."/out/". $this->customer->vin .".txt";


        if (!file_exists($originalMapFile)) {
            $this->ShowPopupMessage("No original Map file found in the media folder!");
            return;
        }


        // start fresh, remove old files
        if (file_exists($finalMapFile)) {
            // customer file
            unlink($finalMapFile);
        }

        if (file_exists($choppedFile1)) {
            unlink($choppedFile1);
        }

        if (file_exists($choppedFile2)) {
            unlink($choppedFile2);
        }

        if (file_exists($temporaryFile1)) {
            unlink($temporaryFile1);
        }

        if (file_exists($temporaryFile2)) {
            unlink($temporaryFile2);
        }


        //start slot n
        //name field:  "CSR Download" text overwrites the harley davidson number
        VanceAndHines::saveNameAndDescriptionInMapFile($originalMapFile, $temporaryFile1, "CSR Download", $originalMapModel->description);

        $source1      = fopen($temporaryFile1, "rb");
        $destination1 = fopen($choppedFile1, "xb+");

        //chopp the extra bytes from customers map file by reading only the first 96x256 bytes
        $contents1 = fread($source1, 96 * 256);
        fwrite($destination1, $contents1);
        fclose($destination1);
        fclose($source1);


        $cal1 = fopen($choppedFile1, "rb");

        //create Buffer for the new slot zero file. filesize = (96*256)*2
        $contentsCal = fread($cal1, 94 * 256 + 64);
        $contentsCal .= chr(85);   //0x55
        $contentsCal .= chr(170);  //0xAA

        fseek($cal1, 94 * 256 + 66);
        $contentsCal .= fread($cal1, filesize($choppedFile1));  // get the rest of the file
        // end of slotn


        // start slot 0 - 2nd half of file
        // name field:  original harley davidson number (41000...)
        VanceAndHines::saveNameAndDescriptionInMapFile($originalMapFile, $temporaryFile2, FALSE, $originalMapModel->description);
        $source2      = fopen($temporaryFile2, "rb");
        $destination2 = fopen($choppedFile2, "xb+");

        $contents2 = fread($source2, 96 * 256);
        fwrite($destination2, $contents2);
        fclose($destination2);
        fclose($source2);



        // #########################

        $contentsCal .= file_get_contents($choppedFile2);
        file_put_contents($finalMapFile, $contentsCal);
        fclose($cal1);


        //cache
        $cachedData = $this->cache->get($this->customer->vin);
        $cachedData['maps']['timestamp'] = time();
        $this->cache->delete($this->customer->vin);
        $this->cache->set($this->customer->vin, $cachedData, $this->cacheExpire);


        $this->ShowPopupMessage("Process Complete!<br><br>". $this->customer->vin . ".txt");

        // don't leave temp files around, delete them
        // but don't delete the customer file either
        if (file_exists($choppedFile1)) {
            //delete the file if it exists, start fresh
            unlink($choppedFile1);
        }

        if (file_exists($choppedFile2)) {
            //delete the file if it exists, start fresh
            unlink($choppedFile2);
        }

        if (file_exists($temporaryFile1)) {
            //delete the file if it exists, start fresh
            unlink($temporaryFile1);
        }

        if (file_exists($temporaryFile2)) {
            //delete the file if it exists, start fresh
            unlink($temporaryFile2);
        }

        return;

    }



    public function uploadToServer($mID, $location)
    {

        $mediaDir = $this->mediaDir;
        $isCustomerMap = NULL;

        if ($location==1) {
            $server = "dev.vhfp3.com";
        } elseif ($location==2) {
            $server = "vhfp3.com";
        } else {
            // prevent hacking
            return;
        }


        if( $mID == $this->map->mapID ){
            //current map
            $map = $this->map;

        } else {
            //other map
            $map = foo(new DB\ServiceMaps())->getByID( $mID );
        }


        $apiVersion = $map->apiVersion;
        $year       = $map->year;


        if( $apiVersion == 0) {
            $mapFile = $mediaDir ."/". $year ."/". $mID .".map";

            if (!file_exists($mapFile)) {
                $this->ShowPopupMessage("The file $mapFile doesn't exists");
                return;
            }

        } else {
            $this->ShowPopupMessage("Internal maps are not allowed for this operation.");
            return;
        }


        $curl = 'curl -skF "mapStream=$(openssl base64 < '. $mapFile .')"';
        $script = " 'http://". $server ."/development/Services/api.php?";


        $vin           = 'vin='.        urlencode($this->customer->vin)."&";
        $bMake         = 'bMake='.      urlencode($this->customer->make)."&";
        $bModel        = 'bModel='.     urlencode($this->customer->model)."&";
        $bYear         = 'bYear='.      urlencode($this->customer->year)."&";
        $calID         = 'calID='.      urlencode($this->customer->calID)."&";
        $ecmFirmware   = 'ecmFirmware='.urlencode($this->customer->ecmFirmware)."&";
        $ecmPart       = 'ecmPart='.    urlencode($this->customer->ecmPart)."&";
        $fVersion      = 'fVersion='.   urlencode($this->customer->fVersion)."&";
        $hVersion      = 'hVersion='.   urlencode($this->customer->hVersion)."&";
        $aVersion      = 'aVersion='.   urlencode($this->customer->aVersion)."&";

        $method        = 'method=SendMap'."&";
        $demoMode      = 'demoMode=N'."&";
        $BaffleType    = 'eBaffleType=PlaceHolder'."&";
        $eManufacturer = 'eManufacturer=PlaceHolder'."&";
        $eName         = 'eName=PlaceHolder'."&";
        $eSize         = 'eSize=PlaceHolder'."&";

        // normal customer uploads
        $isOriginalMap = 'isOriginalMap=1'."&";
        // normal uploads don't pass in the isCustomerMap param.

        // setting for internal map uploads - Python upload en masse
        ////$isOriginalMap = 'isOriginalMap=0'."&";
        // $isCustomerMap = 'isCustomerMap=0'."&";
        // $apiVersion    = 'apiVersion=80'."&";
        // ************************************

        $mDescription = 'mDescription='.urlencode($map->description)."&";
        $mMake        = 'mMake='.       urlencode($map->make)."&";
        $mModel       = 'mModel='.      urlencode($map->model)."&";
        $mYear        = 'mYear='.       urlencode($map->year)."&";


        $parameters = $vin.$bMake.$bModel.$bYear.$calID.$ecmFirmware.$ecmPart.$fVersion.$hVersion.$aVersion.$method.$demoMode.$BaffleType.$eManufacturer.$eName.$eSize.$isOriginalMap.$mDescription.$mMake.$mModel.$mYear.$isCustomerMap.$apiVersion."' ";

        $end = " -H 'Authorization: Basic YXBpdXNlcjpOSFleYmd0NQ==' -H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: en-US,en;q=0.8' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.6 Safari/537.36' -H 'HTTPS: 1' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Cache-Control: max-age=0' -H 'Connection: keep-alive' --compressed ";

        $cmd = $curl . $script . $parameters . $end;
        //$this->ShowPopupMessage($cmd);

        passthru($cmd);
        $this->ShowPopupMessage("Done!");

        return;

    }


    public function sendMap($mID, $sendToVin = null)
    {
        $mediaDir = $this->mediaDir;

        unset($mList);
        unset($recipientVin);


        if( $mID == $this->map->mapID ){
            $mapDescription = $this->map->description;
        } else {
            $mapModel = foo(new DB\ServiceMaps())->getByID($mID);
            $mapDescription = $mapModel->description;
        }

        $mList = array();
        $mList['Customer']['vin']            = $this->customer->vin;
        $mList['Customer']['ecmFirmware']    = $this->customer->ecmFirmware;
        $mList['Customer']['mapID']          = $mID;
        $mList['Customer']['mapYear']        = $this->customer->year;
        $mList['Customer']['mapDescription'] = $mapDescription;


        if( isset($sendToVin) ){
            $recipientVin = strtoupper( trim($sendToVin) );

            $recipientCustomerID    = foo(new DB\OrchestraCustomers())->getCustomerIDByVin( $recipientVin );
            if (!$recipientCustomerID) {
                $this->ShowPopupMessage("<strong>". $recipientVin ."</strong><br/>Cannot find the VIN in the Database!<br/>Does this customer exist?" );
                return;
            }

            $recipientCustomerModel = foo(new DB\ServiceCustomers())->getByID( $recipientCustomerID );
            $recipientMapModel      = foo(new DB\ServiceMaps())->getByID( $recipientCustomerModel->currentMap );

            $mList['Recipient']['vin']            = $recipientVin;
            $mList['Recipient']['ecmFirmware']    = $recipientCustomerModel->ecmFirmware;
            $mList['Recipient']['mapID']          = $recipientCustomerModel->currentMap;
            $mList['Recipient']['mapYear']        = $recipientCustomerModel->year;
            $mList['Recipient']['mapDescription'] = $recipientMapModel->description;

            // validation check
            // Can only do a "send to another vin" if ecm firmware is the same for both maps.
            if( $mList['Customer']['ecmFirmware'] != $mList['Recipient']['ecmFirmware'] ) {
                $this->ShowPopupMessage("You can only send to another vin with the same ECM Firmware.");
                return;
            }
        }

        foreach ($mList as $maps => $val) {

            $sourceMap      = $mediaDir ."/". $val['mapYear'] ."/". $val['mapID'] . ".map";
            $temporaryFile  = $mediaDir ."/out/". $val['vin'] ."_temporary.txt";
            $finalMapFile   = $mediaDir ."/out/". $val['vin'] .".txt";

            if (!file_exists($sourceMap)) {
                $this->ShowPopupMessage( $val['mapID'] . ".map file does not exist.  Can not send Map!" );
                return;
            }

            if (file_exists($finalMapFile)) {
                // customer file
                // delete the file if it exists, start fresh
                unlink($finalMapFile);
            }

            copy($sourceMap, $temporaryFile);
            encodeFile( $val['mapID'], $temporaryFile, $val['mapYear'], $val['vin'], $val['ecmFirmware'] );


            VanceAndHines::saveNameAndDescriptionInMapFile($temporaryFile, $finalMapFile, "CSR Download", $val['mapDescription']);

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

        if( isset($mList['Recipient']['vin']) ){

            copy( $mediaDir ."/out/". $mList['Customer']['vin'] . ".txt",
                  $mediaDir ."/out/". $mList['Recipient']['vin'] . ".txt"
            );

            $this->ShowPopupMessage("Map has been successfully sent!<br><br>". $mList['Recipient']['vin'] . ".txt<br><br>map ID: ". $mList['Recipient']['mapID']   );
        } else {
            $this->ShowPopupMessage("Map has been successfully sent!<br><br>". $mList['Customer']['vin'] . ".txt<br><br>map ID: ". $mList['Customer']['mapID']   );
        }

        // close the panel interface
        $this->cancelSendMap();
        $this->cancelSendOtherMap();

    }



    public function setAsOriginalMap($cID, $mID)
    {
        foo(new DB\OrchestraMaps())->clearOriginalMaps($cID);
        //$this->ShowPopupMessage( $cID );

        foo(new DB\OrchestraMaps())->setOriginalMap($mID);
        $this->ShowPopupMessage($mID . " is now OM");

        // update the content of the 'other Maps' tab after reassigning the isOriginalMap flag
        $this->get('mapSearch')->update();
    }


    public function sendNote($customerID)
    { //RL
        $popup = $this->get('sendNote');
        $popup->get('customerID')->value = $customerID;

        $popup->get('note')->text = '';
        $this->ajax->script("$('#{$popup->get('note')->uniqueID}').select();", 100);
        $popup->show();
    }

    public function saveCustomerNotes($fv)
    {  //RL
        $svcNotes = foo(new \ClickBlocks\DB\ServiceCustomers())->getByID($fv['customerID']);
        $svcNotes->updated = 'NOW()';
        $svcNotes->updatedBy = $this->user->userID;

        $svcNotes->note = $fv['note'];
        $svcNotes->noteFlag = 1;
        $svcNotes->save();

        // Hide popup
        $popup = $this->get('sendNote');
        $popup->hide();

    }

    public function showNotes($mapID)
    {
        $popup = $this->get('notes');
        $popup->get('mapNotes')->tpl->noteMapID = $mapID;
        $popup->get('mapNotes')->get('searchText')->value = '';
        // Show popup after 100 ms
        $popup->show(0.5, true, 100, 0, 500);
        // Refresh -and filter the results by id
        $this->ajax->script("$('#" . $popup->get('mapNotes')->get('refresh')->uniqueID . "').click();");
    }

    public function showAddNote($mapID, $noteID = 0)
    {
        $popup = $this->get('noteAdd');
        $popup->get('mapID')->value = $mapID;
        $popup->get('noteID')->value = $noteID;
        $popup->get('note')->text = '';
        $this->ajax->script("$('#{$popup->get('note')->uniqueID}').select();", 100);
        $popup->show();
    }

    public function showEditNote($noteID)
    {
        $popup = $this->get('noteAdd');
        $svcNotes = foo(new \ClickBlocks\DB\ServiceNotes())->getByID($noteID);
        $popup->assign($svcNotes->getValues());
        $this->ajax->script("$('#{$popup->get('note')->uniqueID}').select();", 100);
        $popup->show();
    }

    public function saveNote($fv)
    {
        $svcNotes = foo(new \ClickBlocks\DB\ServiceNotes())->getByID($fv['noteID']);

        $svcNotes->setValues($fv);
        $svcNotes->updated = 'NOW()';
        $svcNotes->createdBy = $this->user->userID;
        $svcNotes->save();

        // Hide popup
        $popup = $this->get('noteAdd');
        $popup->hide();

        // Refresh
        $popupNotes = $this->get('notes')->get('mapNotes');
        $popupNotes->get('searchText')->value = '';
        $this->ajax->script("$('#" . $popupNotes->get('refresh')->uniqueID . "').click();");

        // update the content of the 'all map notes' tab after adding another note
        $this->get('allMapNotes')->update();
    }

    public function deleteNote($noteID, $flag = false)
    {
        $popup = $this->get('msg');
        $svcNotes = foo(new \ClickBlocks\DB\ServiceNotes())->getByID($noteID);
        if (!$flag) {
            $popup->tpl->title = "Delete Note";
            $popup->tpl->text = 'You are going to delete this note. <br/><br/> ' . \ClickBlocks\Utils\Strings::cut(nl2br($svcNotes->note), 30, false) . '<br/><br/>Continue?';
            $popup->get('btnYes')->onclick = "ajax.doit('->deleteNote', $noteID, true)";
            $popup->show();
        } else {
            $svcNotes->delete();
            // Refresh
            $popupNotes = $this->get('notes')->get('mapNotes');
            $this->ajax->script("$('#" . $popupNotes->get('refresh')->uniqueID . "').click();");
            $popup->hide();

            $this->get('allMapNotes')->update();
        }
    }


}

?>
