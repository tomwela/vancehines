<?php

namespace ClickBlocks\DB;

use ClickBlocks\Cache;
use ClickBlocks\Core;

class OrchestraOta extends \ClickBlocks\DB\Orchestra
{
    public function __construct()
    {
        parent::__construct('\ClickBlocks\DB\Ota');
    }


    public function getCANData()
    {   // top n query - do NOT remove the limit clause
        $sql = "SELECT DISTINCT 
                    REPLACE(REPLACE(filename,'_.zip',''), '_', '') numb,
                    url,
                    filename
                FROM ota
                WHERE REPLACE(hardware, '\.','') < ?
                ORDER BY numb DESC
                LIMIT 1 ";

        return $this->db->row($sql, array(500));
    }


    public function getJ1850Data()
    {
        $sql = "SELECT DISTINCT 
                    REPLACE(REPLACE(filename,'_.zip',''), '_', '') numb,
                    url,
                    filename
                FROM ota
                WHERE REPLACE(hardware, '\.','') >= ?
                ORDER BY numb DESC
                LIMIT 1 ";

        return $this->db->row($sql, array(500));
    }


    public function getFirmwareData($os, $app, $hardware, $userFirmware)
    {
        $sql = "SELECT CONCAT(url, filename) url
                FROM ota 
                WHERE vin is null 
                AND os = ? 
                AND app = ? 
                AND hardware = ? 
                AND CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->row($sql, array($os, $app, $hardware, $userFirmware, $userFirmware));
    }

    public function getCustomFirmwareData($os, $app, $hardware, $userFirmware, $vin)
    {
        $sql = "SELECT CONCAT(url, filename) url
                FROM ota 
                WHERE vin = ? 
                AND os = ? 
                AND app = ? 
                AND hardware = ? 
                AND CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->row($sql, array($vin, $os, $app, $hardware, $userFirmware, $userFirmware));
    }


    public function getWBFirmwareData($os, $app, $hardware, $userFirmware)
    {
        $sql = "SELECT CONCAT(wb_url, wb_filename) url
                FROM ota 
                WHERE wb_vin is null 
                AND os = ? 
                AND app = ? 
                AND wb_hardware = ? 
                AND CAST(REPLACE(wb_max_firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(wb_min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->row($sql, array($os, $app, $hardware, $userFirmware, $userFirmware));
    }

    public function getCustomWBFirmwareData($os, $app, $hardware, $userFirmware, $vin)
    {
        $sql = "SELECT CONCAT(wb_url, wb_filename) url
                FROM ota 
                WHERE wb_vin = ? 
                AND os = ? 
                AND app = ? 
                AND wb_hardware = ? 
                AND CAST(REPLACE(wb_max_firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(wb_min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->row($sql, array($vin, $os, $app, $hardware, $userFirmware, $userFirmware));
    }

    //  public function autoTuneFlag($os, $app, $hardware, $userFirmware)
    // {


    //     $sql = "SELECT autotune
    //             FROM ota 
    //             WHERE vin is null 
    //             AND os = ? 
    //             AND app = ? 
    //             AND hardware = ? 
    //             AND CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >= ? 
    //             AND CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <= ? 
    //             AND autotune=1 ";

    //     return $this->db->col($sql, array($os, $app, $hardware, $userFirmware, $userFirmware));
    // }


       public function customAutoTuneFlag($os, $app, $hardware, $userFirmware, $vin)
    {
        $sql = "SELECT autotune
                FROM ota 
                WHERE vin = ? 
                AND os = ? 
                AND app = ? 
                AND hardware = ? 
                AND CAST(REPLACE(firmware, '.', '') AS UNSIGNED) >= ? 
                AND CAST(REPLACE(min_firmware, '.', '') AS UNSIGNED) <= ? ";

        return $this->db->col($sql, array($vin, $os, $app, $hardware, $userFirmware, $userFirmware));
    }
      public function customCksBoot($vin,$type)
    {
        $sql = "SELECT $type
                FROM ota 
                WHERE vin = ? 
               
                ";

        return $this->db->col($sql, array($vin));
    }

}

?>