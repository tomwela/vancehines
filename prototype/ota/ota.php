<?php

/* Connect to database */
include 'define.php';

/*  Data Validation Required */
$_SESSION['firmware'] = ( isset($_GET['firmware']) && $_GET['firmware'] != 'no_firmware' ) ? $_GET['firmware'] : '0.0.0';
$_SESSION['software'] = ( isset($_GET['software']) && $_GET['software'] != 'no_software' ) ? $_GET['software'] : '0.0.0';
$_SESSION['app']      = ( isset($_GET['app'])      && $_GET['app']      != 'no_app'      ) ? $_GET['app']      : '0.0.0';
$_SESSION['os']       = ( isset($_GET['os'])       && $_GET['os']       != 'no_os'       ) ? $_GET['os']       : '0.0.0';
$_SESSION['hardware'] = ( isset($_GET['hardware']) && $_GET['hardware'] != 'no_hardware' ) ? $_GET['hardware'] : '0.0.0';



function logger_procedural($fname, $_SESSION)
{
    $fdata = array(
        'firmware' => $_SESSION['firmware'],
        'app'      => $_SESSION['app'],
        'os'       => $_SESSION['os'],
        'hardware' => $_SESSION['hardware']
    );

    $logPath = 'tmp';
    $logFile = $logPath . "/" . $fname;

    $lsm = fopen($logFile, "a");
    fwrite($lsm, date("m/d/Y h:i:s A") . ' ');
    fwrite($lsm, "\n" . json_encode($fdata) . "\n\n");
    fclose($lsm);
}

logger_procedural('log_ota_'. date('m_d_Y') .'.txt', $_SESSION);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>FP3 OTA</title>
    </head>
    <body> <br>
        <div id = "mainlist" class = 'styled-select' align = 'center'>
        <?php
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
            //---------------------------Begin building query string----------------//
            $base_firmware = "0.0.0";
            if(strlen($_SESSION['firmware']) > 0)
            {
                $base_firmware = $_SESSION['firmware'];
            }
            $current_firmware = explode(".", $base_firmware);
            $string00 = "FROM ota WHERE ";


            //---------------------------Add to query string data submitted by bike-//
            if(strlen($_SESSION['os']) > 0)
            {
                $string00 .= "os = '".$_SESSION['os']."' AND ";
            }
            else
            {
                $string00 .= "os = '".'iOS'."' AND ";
            }

//            if(strlen($_SESSION['firmware']) > 0)
//            {
//                $string00 .= "firmware = '".$_SESSION['firmware']."' AND ";
//            }
//            if(strlen($_SESSION['software']) > 0)
//            {
//                $string00 .= "software = '".$_SESSION['software']."' AND ";
//            }
            if(strlen($_SESSION['app']) > 0)
            {
                $string00 .= "app = '".$_SESSION['app']."' AND ";
            }
//            if(strlen($_SESSION['os']) > 0)
//            {
//                $string00 .= "os = '".$_SESSION['os']."' AND ";
//            }
            if(strlen($_SESSION['hardware']) > 0)
            {
                $string00 .= "hardware = '".$_SESSION['hardware']."' AND ";
            }
            //---------------------------Remove the last 'AND'----------------------//
            $query00 =  substr_replace($string00, '', -5, -1);

            $query = "SELECT *"." ".$query00."";
//            echo $query;

            $result = mysql_query($query);

            $update_url = "";
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                $min = explode(".", $row['min_firmware']);
                $max = explode(".", $row['firmware']);
                $user_firmware = $current_firmware[0] * 100 + $current_firmware[1] * 10 + $current_firmware[2];
                $min_firmware = $min[0] * 100 + $min[1] * 10 + $min[2];
                $max_firmware = $max[0] * 100 + $max[1] * 10 + $max[2];
                if($user_firmware >= $min_firmware && $user_firmware<= $max_firmware)
                {
                    //$update_url = $row['update'];
                    $update_url = $row['url'] . $row['filename'];
                }

            }

            echo "<zipfile>".$update_url."</zipfile>";

/*
            //---------------------------Build the dropdown lists-------------------//
            $countlist = 0;
            while ($countlist < count($labelArray))
            {
                $query = "SELECT DISTINCT ".$mysqlArray[$countlist]." ".$query00."";
                $result = mysql_query($query);
                echo "<div class = 'style_list'><select class = ".$classArray[$countlist]." onChange='update_search(this.value);'>";
                echo "<option value='*' >".$labelArray[$countlist]."</option>";
                while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    echo "<option value='".$line[$mysqlArray[$countlist]]."'>".$line[$mysqlArray[$countlist]]."</option>";
                }
                echo "</select><br></div>";
                $countlist = $countlist + 1;
            }

            //---------------------------Update how many options are available-------//
            $result00 = mysql_query("SELECT * ".$query00);
            $resultsize00 = mysql_num_rows($result00);
            echo "<div align = 'center'>";
                echo "<br>".$resultsize00." maps match your ".$_SESSION['year']." ".$_SESSION['model']." configuration.<br>";
            echo "</div>";
            lo
            //---------------------------Show a search reset button------------------//
            echo "<form name='revisons' align = 'center' method='post' action='search.php?model=".$_SESSION['model']."&year=".$_SESSION['year']."'>
                <input type='submit' class='input-button' name='refresh' value='Refresh' />
            </form>";
*/
        ?>
        </div>

        <!---------------------------jQuery request w/ Ajax response----------------->

        <?php
        if(is_resource($link))
        {
            mysql_close($link);
        }
        ?>
    </body>
</html>
