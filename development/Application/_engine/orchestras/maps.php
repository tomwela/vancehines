<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

    //use ClickBlocks\Cache;

class OrchestraMaps extends Orchestra
{

  public $debug_oop = 1;  //1 or 0


  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Maps');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\Maps';
  }

  public  function getWidgetMapSearchData($type, array $params)
  {
      // Used by CSR Map Search and Customer Details Other Maps
      // ecmFirmware is not passed in
      // show internal maps only with api version
      // show customer's other maps by customerID


      if ($params['customerID'])
      {
          $w[] = 'customerID = '. $this->db->quote($params['customerID']);
          $w[] = 'mapID <> '. $this->db->quote($params['currentMap']);
      } else {
          $w[] = "(apiVersion = " . API_VERSION . " OR apiVersion = " . API_J1850_VERSION . ") ";
          $w[] = "CAST(ecmFirmware AS UNSIGNED) > 0 ";

          if ( $params['searchvalue'] ) {
              $like = $this->db->quote('%' . $params['searchvalue'] . '%');
              $or = array(
                  'ecmFirmware',
                  'year',
                  'model',
                  'name',
              );

              foreach ($or as $key => &$v) {
                  $v = "$v LIKE $like";
              }
              $w[] = " ( " . implode(' OR ', $or) . " ) ";
          }
      }


      if($params['ExhastManufacturer']) $w[] = "eManufacturer = ". $this->db->quote($params['ExhastManufacturer']);
      if($params['ExhastName']) $w[]         = "eName = ".         $this->db->quote($params['ExhastName']);
      if($params['BaffleType']) $w[]         = "baffleType = ".    $this->db->quote($params['BaffleType']);
      if($params['EngineSize']) $w[]         = "eSize = ".         $this->db->quote($params['EngineSize']);

      if($w) $where = ' WHERE ' . implode(' AND ', $w);
      switch ($type)
      {
        case 'count':
           return $this->db->col('SELECT COUNT(*) FROM Maps '  . $where);
        case 'rows':
        	$fields = array(
                'mapID',
                'eManufacturer',
                'substring(name, -13,3) fitment',
                'CAST(ecmFirmware AS UNSIGNED) ecmFirmware',
                'eName',
                'baffleType',
                'year',
                'model',
                'eSize',
                'SUBSTRING_INDEX(description, \': \', -1) Filename',
          );


           $rl_value = explode(' ',$fields[ abs($params['sortBy']) - 1 ]);
           $sortBy = end($rl_value) . ($params['sortBy'] > 0 ? " DESC" : " ASC");

           $o = ' ORDER BY ' . $sortBy;
           $fields = implode(', ', $fields);
           $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];

           $rows = $this->db->rows("SELECT $fields  FROM Maps " . $where . $o . $limit);

           if($this->debug_oop==1)
            $this->logger_oop('log_getWidgetMapSearchData_'. date('m_d_Y') .'.txt', "SELECT $fields  FROM Maps " . $where . $o . $limit);

           return $rows;
      }

  }


  public  function getSearchMap($params)
  {

      if ( !isset($params['ecmFirmware']) ) {
          // old apps
          $w[] = "m.ecmFirmware IS NULL ";
      } else {
          // new apps
          $w[] = "m.ecmFirmware = " . $params['ecmFirmware'];
      }

    if($params['eManufacturer'])  $w[] = "m.eManufacturer = " .     $this->db->quote($params['eManufacturer']);
    if($params['eName'])          $w[] = "m.eName = " .             $this->db->quote($params['eName']);
    if($params['baffleType'])     $w[] = "m.baffleType = " .        $this->db->quote($params['eBaffleType']);
    if($params['eSize'])          $w[] = "m.eSize = " .             $this->db->quote($params['eSize'] );
    if($params['bmodel'])         $w[] = "m.description LIKE (" .   $this->db->quote('%' . $params['bmodel'] . '%') . ")";
    if($params['bMake'])          $w[] = "c.make = " .              $this->db->quote($params['bMake']);
    //if($params['bModel'])         $w[] = "m.model = " .             $this->db->quote($params['bModel']);
    if($params['bYear'])          $w[] = "m.year = " .              $params['bYear'];
    if($params['cName'])          $w[] = "c.fullName like (" .      $this->db->quote('%' . $params['cName'] . '%') . ")";
    if($params['cEmail'])         $w[] = "c.email like (" .         $this->db->quote('%' . $params['cEmail'] . '%') . ")";
    if($params['cAddress'])       $w[] = "c.address like (" .       $this->db->quote('%' . $params['cAddress'] . '%') . ")";
    if($params['cPhone'])         $w[] = "c.phone like (" .         $this->db->quote('%' . $params['cPhone'] . '%') . ")";
    //if($params['cVin'])           $w[] = "c.vin = " .               $this->db->quote($params['cVin']);

    // 883 bike upgrade processing
    $vinMasked = "0". substr($params['cVin'],1,17);
    $w[] = "(c.vin = ". $this->db->quote($params['cVin']) ." OR c.vin = ". $this->db->quote($vinMasked) .") ";

    if($params['isCurrentMap'])   $w[] = 'm.mapID = c.currentMap';
    if ($w)
      $w = 'WHERE ' . implode(' AND ', $w);

    //sort by
    //$sb = ' order by m.eManufacturer Desc ';

    if($this->debug_oop==1)
        $this->logger_oop('log_getSearchMap_'. date('m_d_Y') .'.txt', "SELECT m.mapID, m.model as mModel, m.description as mDescription, name FROM Maps as m INNER JOIN Customers as c ON c.customerID = m.customerID $w");

    // return $this->db->rows("SELECT m.mapID, m.model as mModel, m.description as mDescription, name FROM Maps as m INNER JOIN Customers as c ON c.customerID = m.customerID $w");

      //SY added in Jan 2019 for searchMapdetails api
 return $this->db->rows("SELECT m.mapID, m.model as mModel, m.description as mDescription, name, m.eSize, m.eSize,m.eManufacturer, m.baffleType as eBaffleType, m.eName FROM Maps as m INNER JOIN Customers as c ON c.customerID = m.customerID $w");

  }


    public function getDDBOptions($flag, $eManufacturer = null, $eName = null, $baffleType = null,
                                $eSize = null, $apiFlag = null, $params = null, $searchValue = null)
    {
        $w = array();

        if ( !isset($params['ecmFirmware']) ) {
          // old apps
          $nothing = NULL;
        } else {
          // new apps
          $w[] = "m.ecmFirmware = " . $params['ecmFirmware'];
        }


        if($eManufacturer)  $w[] = 'm.eManufacturer = ' . $this->db->quote($eManufacturer);
        if($eName)          $w[] = 'm.eName = ' .         $this->db->quote($eName);
        if($baffleType)     $w[] = 'm.baffleType = ' .    $this->db->quote($baffleType);
        if($eSize)          $w[] = 'm.eSize = ' .         $this->db->quote($eSize);


        if(!$apiFlag) {
            // show internal maps only by api version  - for CSR Map Search only
            $w[] = "(apiVersion = ". API_VERSION ." OR apiVersion = ". API_J1850_VERSION .") ";
            $w[] = "CAST(ecmFirmware AS UNSIGNED) > 0 ";


            if ( $searchValue ) {
                $like = $this->db->quote('%' . $searchValue . '%');
                $or = array(
                    'ecmFirmware',
                    'year',
                    'model',
                    'name',
                );

                foreach ($or as $key => &$v) {
                    $v = "$v LIKE $like";
                }
                $w[] = " ( " . implode(' OR ', $or) . " ) ";
            }

            if ( $w ) {
                $w = ' WHERE ' . implode(' AND ', $w);
            } else {
                $w = NULL;
            }

        } else {
            // search by mobile apps, not the CSR site

            if($params['bmodel']) $w[] = "m.description LIKE (" .   $this->db->quote('%' . $params['bmodel'] . '%') . ")";
            if($params['bMake'])  $w[] = "c.make = " .  $this->db->quote($params['bMake']);
            if($params['bYear'])  $w[] = "m.year = " .  $params['bYear'];

            // 883 bike upgrade processing
            $vinMasked = "0" . substr($params['cVin'], 1, 17);
            $w[] = "(c.vin = " . $this->db->quote($params['cVin']) . " OR c.vin = " . $this->db->quote($vinMasked) . ") ";


            if ( $w ) {
                $w = ' WHERE ' . implode(' AND ', $w);
            } else {
                $w = NULL;
            }
            $w = ' INNER JOIN Customers AS c ON c.customerID = m.customerID ' . $w;

        }


        $sb = ' order by 1 ASC ';
        if ( $flag == 'eManufacturer' || $flag == 'm.eManufacturer' ) {
            $sb = ' order by 1 DESC ';
        }

        // logging
        if ( $this->debug_oop == 1 ) {
          $this->logger_oop('log_getDDBOptions_' . date('m_d_Y') . '.txt',
              "SELECT DISTINCT $flag, $flag FROM Maps AS m" . $w . $sb);
        }


        //return results
        if ( in_array($flag, array( 'eManufacturer', 'eName', 'baffleType', 'eSize' )) ) {
            return $this->db->couples("SELECT DISTINCT $flag, $flag FROM Maps AS m" . $w . $sb);

        } else {
            if ( in_array($flag, array( 'm.eManufacturer', 'm.eName', 'm.baffleType', 'm.eSize' )) ) {
                return $this->db->couples("SELECT DISTINCT $flag, $flag FROM Maps AS m" . $w . $sb);

            } else {
                return array();
            }
        }

    }

    public  function getCustomersOtherMapsData($type, array $params)
    {
        if ($params['customerID'])
        {
            $w[] = 'm.customerID = '. $this->db->quote($params['customerID']);
            $w[] = 'm.mapID <> '. $this->db->quote($params['currentMap']);
        }


        if($params['isCustomer']) $w[]         = "isCustomerMap = ". $this->db->quote($params['isCustomer']);
        if($params['ExhastManufacturer']) $w[] = "eManufacturer = ". $this->db->quote($params['ExhastManufacturer']);
        if($params['ExhastName']) $w[]         = "eName = ".         $this->db->quote($params['ExhastName']);
        if($params['BaffleType']) $w[]         = "baffleType = ".    $this->db->quote($params['BaffleType']);
        if($params['EngineSize']) $w[]         = "eSize = ".         $this->db->quote($params['EngineSize']);

        if($w) $where = ' WHERE ' . implode(' AND ', $w);
        switch ($type)
        {
            case 'count':
                return $this->db->col('SELECT COUNT(*) FROM Maps AS m '  . $where);
            case 'rows':
                $fields = array(
                    'm.name',
                    'DATE_FORMAT(m.updated,"%m/%d/%Y") AS updatedDate',
                    'LOWER(DATE_FORMAT(m.updated,"%h:%i %p")) AS updatedTime',
                    'm.description',
                    'm.isOriginalMap',
                    'm.mapID'
                );

                $rl_value = explode(' ',$fields[ abs($params['sortBy']) - 1 ]);
                $sortBy = end($rl_value) . ($params['sortBy'] > 0 ? " ASC" : " DESC" );

                $o = ' ORDER BY ' . $sortBy;
                $fields = implode(', ', $fields);
                $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];

                $rows = $this->db->rows("SELECT $fields  FROM Maps AS m " . $where . $o . $limit);

                if($this->debug_oop==1)
                    $this->logger_oop('log_getCustomersOtherMapsData_'. date('m_d_Y') .'.txt', "SELECT $fields  FROM Maps AS m " . $where . $o . $limit);

                return $rows;
        }

    }


    public function doesMapExist($mapID)
    {
        return $this->db->col('select mapID from Maps where mapID = ? ', array( $mapID ));
    }

    public function getOriginalMapForCustomer($customerID)
    {
        return $this->db->col('select mapID from Maps where customerID = ? and isOriginalMap = 1',
            array( $customerID ));
    }

    public function clearOriginalMaps($customerID)
    {
        $this->db->execute('update Maps set isOriginalMap = 0 where customerID = ' . $this->db->quote($customerID));
    }

    public function setOriginalMap($mapID)
    {
        $this->db->execute('update Maps set isOriginalMap = 1 where mapID = ' . $this->db->quote($mapID));
    }

    public function getSearchCustomer($mapID)
    {
        $w[] = 'currentMap = ' . $this->db->quote($mapID);

        return $this->db->rows("SELECT * FROM Customers $w");
    }

    public function deleteMap($mapID)
    {
        foreach (array(
                     'Acceleration', 'AFRatios', 'EITMSof', 'EITMSon', 'EngineDisplacements', 'IAC', 'IdleRPM',
                     'Deceleration', 'SparkFront', 'SparkRear', 'VEfc', 'VErc', 'ThrtottleProgrsivity1',
                     'ThrtottleProgrsivity2', 'History', 'Notes', 'Maps',
                 ) as $t) {
            $this->db->execute("DELETE FROM $t WHERE mapID = $mapID");
        }
    }

    public function getPossibleValues($field)
    {
        return $this->db->cols('select distinct ' . $field . ' from Maps');
    }

    public function countMapNotes($customerID)
    {
        return $this->db->col('SELECT count(*) FROM Maps m INNER JOIN Notes n ON m.mapID = n.mapID WHERE m.customerID = ?  AND n.mapID IS NOT NULL',
            array( $customerID ));
    }

    public function logger_oop($fname, $fdata)
    {
        $logPath = Core\IO::dir($this->config->dirs['log']);
        $logFile = $logPath . "/" . $fname;

        $lsm = fopen($logFile, "a");
        fwrite($lsm, date("m/d/Y h:i:s A") . "\n");
        fwrite($lsm, json_encode($fdata) . " \n\n");
        fclose($lsm);
    }

  public function slot0Restore($vin)
    {
//SY MAY 2019
$slotRestore=$this->db->row("SELECT mapID, year, eSize ecm FROM Maps WHERE SUBSTRING_INDEX(`name`,'_',-1)= ? AND  isCustomerMap=? AND isOriginalMap=? AND eManufacturer=?",array( $vin,1,1,'SLOT0'));
return $slotRestore;
      }

     public function slotRestoreUpdate($vin)
    {

$slotUpdate=$this->db->row("UPDATE Maps SET eManufacturer=? WHERE SUBSTRING_INDEX(`name`,'_',-1)= ? AND isCustomerMap=? AND isOriginalMap=? AND eManufacturer=?",array( 'restored', $vin,1,1,'SLOT0'));
return $slotRestore;
      }

}

?>
