<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraCustomers extends Orchestra
{
  private $blockTime = 12;

  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Customers');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\Customers';
   }

  public  function getWidgetMapQueueData($type, array $params)
  {
    if ($params['searchvalue'])
      $w[] = '(fullName LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR vin LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR make LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR model LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ')';;
    if ($w)
    {
        $where = ' WHERE ' . implode(' AND ', $w);
    }
    else
    {
      $where = ' WHERE ' . 'ecmPart > 0.0';
    }

    switch ($type)
    {
      case 'count':
        return $this->db->col('SELECT COUNT(*) FROM Customers ' . $where);
      case 'rows':
        $fields = array(
            'vin',
            'DATE_FORMAT(updated,"%m/%d/%Y") AS updatedDate',
            'LOWER(DATE_FORMAT(updated,"%h:%i %p")) AS updatedTime',
            'fullName',
            'fVersion',
            'model',
            '`year`',
            'aVersion'
        );
        // Special case
        switch (abs($params['sortBy']))
        {
          case 2:
            $sortBy = 'updated';
            break;
          case 3:
            $sortBy = 'time(updated)';
            break;
          default:
            $sortBy = $fields[abs($params['sortBy']) - 1];

        }
        $sortBy .= ($params['sortBy'] > 0 ? " DESC" : " ASC");
        $o = ' ORDER BY ' . $sortBy;
        $fields = implode(', ', $fields);
        $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];

//        $block = "IF(UNIX_TIMESTAMP() - c.lastPing < {$this->blockTime}, true, false) AS isBlock,";
//      Turning off row access locking - force it to always return false so it doesnt lock rows.
//      The code to lock rows was already removed from the HTML but this is to prevent any unknowns.
        $block = "FALSE AS isBlock,";

        $rows = $this->db->rows("SELECT $block customerID, $fields  FROM Customers " . $where. ' ' . $o . $limit);

        //count maps per vin, add count to $rows array for map queue
        for($i = 0; $i <count($rows); $i++) {
          foreach($rows[$i] as $key => $value) {
            if($key == 'customerID' ) {
              $mapCount = $this->db->col('SELECT COUNT(*) FROM Maps WHERE customerID = '. $value);
              $mapCount = abs($mapCount-1); //dont count isOriginalMap=1 map
              array_push($rows[$i], array('mapCount'=>$mapCount));
            } //if
          } //foreach
        } //for

        return $rows;

    }//switch
  }

  public  function isEmailUnique($email)
  {
    return ($this->db->col('SELECT COUNT(*) FROM Customers WHERE email = ?', array($email)) == 0);
  }



  public  function getNoteFlagByVin($vin)
  {
    return $this->db->row('SELECT note, noteFlag from Customers where vin = ?', array($vin));
  }


  public  function updateCNoteByVin($vin)
  {
    $sql = 'UPDATE Customers SET note = NULL, noteFlag = 0 WHERE vin = ? ';
    $this->db->execute($sql, array($vin));
  }


  public function getCustomerContactInfo($vin)
  {
    $sql = ' SELECT fullName, address, address2, city, state, zip, country, phone, email, contactPreferences FROM Customers WHERE vin = ? ';
    return $this->db->row($sql, array($vin));
  }


  public function updateCustomerContactInfo( $userInfo )
  {
    $sql = "UPDATE Customers
            SET fullName = ? ,
                address  = ? ,
                address2 = ? ,
                city     = ? ,
                state    = ? ,
                zip      = ? ,
                country  = ? ,
                phone    = ? ,
                email    = ? ,
                contactPreferences = ?
            WHERE vin =  ? ";

    return $this->db->row($sql, array(  $userInfo['fullName'],
                                        $userInfo['address'],
                                        $userInfo['address2'],
                                        $userInfo['city'],
                                        $userInfo['state'],
                                        $userInfo['zip'],
                                        $userInfo['country'],
                                        $userInfo['phone'],
                                        $userInfo['email'],
                                        $userInfo['contactPreferences'],
                                        $userInfo['vin']
                                    ));
  }

  public function getECMFirmwareByMapID($mapID)
  {
    return $this->db->col('SELECT ecmFirmware FROM Customers WHERE customerID = (SELECT customerID FROM Maps WHERE mapID = ? )', array($mapID));
  }


  public  function getCustomerIDByVin($vin)
  {
    return $this->db->col('select customerID from Customers where vin = ?', array($vin));
  }
  
  public  function getCurrentMapByVin($vin)
  {
    return $this->db->col('select currentMap from Customers where vin = ?', array($vin));
  }

  public function getFlashData($customerID)
  {
    $sql = "SELECT c.customerID,
                   c.vin,
                   m.mapID,
                   c.year,
                   c.fullName,
                   m.model,
                   m.isOriginalMap,
                   c.ecmFirmware, 
                   IF(c.fVersion LIKE '%CARB%',TRUE, FALSE) carb 
                   FROM Customers c 
                   INNER JOIN Maps m 
                   ON c.customerID = m.customerID 
                   WHERE c.customerID = ? 
                   AND m.isOriginalMap = ? ";

    return $this->db->row($sql, array($customerID, '1'));
  }

 

  public  function updateLastPing($customerID)
  {
    $sql = 'UPDATE Customers SET lastPing = UNIX_TIMESTAMP() WHERE customerID = ?';
    $this->db->execute($sql, array($customerID));
  }

  public  function checkMap($mapID)
  {
    return (bool)$this->db->col('SELECT count(*) > 0 FROM Customers WHERE currentMap = ?', array($mapID));
  }

  public  function recoveryDetails($vin)
  {
    return $this->db->row('SELECT make,model,year,ecmFirmware, ecmPart,fVersion,hVersion,aVersion FROM Customers WHERE vin = ?', array($vin));
  }

}  //end class OrchestraCustomers

?>