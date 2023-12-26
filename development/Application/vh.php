<?php

namespace ClickBlocks\MVC;

use ClickBlocks\Core;
use ClickBlocks\DB;
use ClickBlocks\Utils;
use ClickBlocks\Web;
use ClickBlocks\Web\UI\Helpers;
use ClickBlocks\Web\UI\POM;

use ClickBlocks\MVC\Backend;

class VanceAndHines extends Page
{
    protected $ie = null;
    protected $uri = null;

    public function __construct($template = null)
    {
        parent::__construct($template);
        preg_match('/MSIE ([0-9]?)/i', $_SERVER['HTTP_USER_AGENT'], $arr);
        $this->ie = (int)$arr[1];
        $this->uri = $this->reg->uri = new Utils\URI();
    }

    public function init()
    {
        parent::init();
        $this->tpl->config = $this->config;
    }

    /**
     * @param      $sourceFile
     * @param      $outputFile
     * @param bool $processName: can be true, false or the name of the field1 (name)
     * @param      $mapDescription
     */
    public static function saveNameAndDescriptionInMapFile($sourceFile, $outputFile, $processName = false, $mapDescription)
    {
        $cal1 = fopen($sourceFile, "rb");

        if($processName){

            $contentsCal = fread($cal1, 95 * 256 + 0);

            $calIDName = unpack("C*", $processName);
            //$calIDName = unpack("C*", "CSR Download");
            foreach($calIDName as $byte){
                $contentsCal .= chr($byte);
                $f1f2        .= chr($byte);  //f1f2 represents contents of field1=name and field2=description with null termination
            }

            $contentsCal .= chr(0); //End contents with null termination
            $f1f2        .= chr(0);

        } else {
            // don't overwrite the first field containing the harley number
            $contentsCal = fread($cal1, 95 * 256 + 13 + 1);
        }


        // When a map is sent back to the CSR from the mobile apps, it will
        // contain the 'CSR Download' text as part of the description (not name)
        // so we remove it.
        $description = str_replace('CSR Download','', $mapDescription);
        $dscrptn     = preg_replace('/[[:blank:]]{2,}/',' ', $description);


        $binaryDescription = unpack("C*", $dscrptn);
        foreach($binaryDescription as $byte){
            $contentsCal .= chr($byte);
            $f1f2        .= chr($byte);
        }
        $contentsCal .= chr(0);
        $f1f2        .= chr(0);

        $sizeOfBinaryDescription = count($binaryDescription);


        // Description should never be greater than 64 characters max
        // Name field: 13 = length of "csr download" + null char
        // Fill the rest of sector 96 with zeros
        // until 96 * 256 is reached.  Last address is 5ff0 & file size = 24576 bytes
        fseek($cal1, 95 * 256 + $sizeOfBinaryDescription + 13 + 1);
        for ($x = 0; $x < (256 - strlen($f1f2)); $x++) {
            $contentsCal .= chr(0); //0
        }

        fclose($cal1);

        // open file, write to it, save it with one command
        file_put_contents($outputFile, $contentsCal);

        return;

    }
}

?>
