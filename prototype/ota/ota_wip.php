<?php

/* Connect to database */
include 'define.php';
    
/*  Data Validation Required */    
//$_SESSION['firmware'] = ( isset($_GET['firmware']) && $_GET['firmware'] != 'no_firmware' ) ? $_GET['firmware'] : '0.0.0';     
//$_SESSION['software'] = ( isset($_GET['software']) && $_GET['software'] != 'no_software' ) ? $_GET['software'] : '0.0.0';
//$_SESSION['app']      = ( isset($_GET['app'])      && $_GET['app']      != 'no_app'      ) ? $_GET['app']      : '0.0.0';        
//$_SESSION['os']       = ( isset($_GET['os'])       && $_GET['os']       != 'no_os'       ) ? $_GET['os']       : '0.0.0';        
//$_SESSION['hardware'] = ( isset($_GET['hardware']) && $_GET['hardware'] != 'no_hardware' ) ? $_GET['hardware'] : '0.0.0';     


$_SESSION['firmware'] = ( isset($_GET['firmware'])  ) ? $_GET['firmware'] : '0.0.0';     
$_SESSION['software'] = ( isset($_GET['software'])  ) ? $_GET['software'] : '0.0.0';
$_SESSION['app']      = ( isset($_GET['app'])       ) ? $_GET['app']      : '0.0.0';        
$_SESSION['os']       = ( isset($_GET['os'])        ) ? $_GET['os']       : '0.0.0';        
$_SESSION['hardware'] = ( isset($_GET['hardware'])  ) ? $_GET['hardware'] : '0.0.0';     


?>

<!DOCTYPE html> 
<html> 
    <head> 
        <title>FP3 OTA 2.0</title>         
    </head> 
    <body> <br>
        <div id = "mainlist" class = 'styled-select' align = 'center'> 
        <?php 
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
            //---------------------------Begin building query string----------------//            
            //$base_firmware = "0.0.0";
            //if(strlen($_SESSION['firmware']) > 0)
            {
                $base_firmware = $_SESSION['firmware'];
            }
            $current_firmware = explode(".", $base_firmware);
            $string00 = "FROM ota WHERE ";

                        
            //---------------------------Add to query string data submitted by bike-//            
            //if(strlen($_SESSION['os']) > 0)
            {
                //$string00 .= "os = '".$_SESSION['os']."' AND ";                        
                $string00 .= "os = '".$_SESSION['os']."' ";                        
            }
            //else
            //{
            //    //$string00 .= "os = '".'iOS'."' AND";                        
            //    $string00 .= "os = '".'iOS'."' ";                        
            //}
            
            
            //$base_app = "0.0.0";
            //if(strlen($_SESSION['app']) > 0)
            {
                $base_app = $_SESSION['app'];
                //$string00 .= "app = '".$_SESSION['app']."' AND ";                        
            }
            $current_app = explode(".", $base_app);


            //$base_hardware = "0.0.0";
            //if(strlen($_SESSION['hardware']) > 0)
            {
                $base_hardware = $_SESSION['hardware'];
                //$string00 .= "hardware = '".$_SESSION['hardware']."' AND ";                        
            }
            $current_hardware = explode(".", $base_hardware);
            
            echo $_SESSION['firmware'];
            echo $_SESSION['app'];
            echo $_SESSION['hardware'];
            echo $_SESSION['os'];
            
            //echo $current_firmware;
            //echo $current_app;
            //echo $base_hardware;
            
            
            //---------------------------Remove the last 'AND'----------------------//                        
            $query00 =  $string00; //substr_replace($string00, '', -5, -1);        
            
            $query = "SELECT *"." ".$query00."";
            echo $query;
            
            $result = mysql_query($query);
  
            $update_url = "";
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
            {
                $min = explode(".", $row['min_firmware']);
                $max = explode(".", $row['firmware']); 
                $user_firmware = $current_firmware[0] * 100 + $current_firmware[1] * 10 + $current_firmware[2];               
                $min_firmware = $min[0] * 100 + $min[1] * 10 + $min[2];
                $max_firmware = $max[0] * 100 + $max[1] * 10 + $max[2];
                
                
                $min = explode(".", $row['min_hardware']);
                $max = explode(".", $row['hardware']); 
                $user_hardware = $current_hardware[0] * 100 + $current_hardware[1] * 10 + $current_hardware[2];               
                $min_hardware = $min[0] * 100 + $min[1] * 10 + $min[2];
                $max_hardware = $max[0] * 100 + $max[1] * 10 + $max[2];
                                
                $min = explode(".", $row['min_app']);
                $max = explode(".", $row['app']); 
                $user_app = $current_app[0] * 100 + $current_app[1] * 10 + $current_app[2];               
                $min_app = $min[0] * 100 + $min[1] * 10 + $min[2];
                $max_app = $max[0] * 100 + $max[1] * 10 + $max[2];
                                
                
                if ($user_firmware >= $min_firmware && $user_firmware<= $max_firmware  &&
                    $user_hardware >= $min_hardware && $user_hardware<= $max_hardware  &&
                    $user_app      >= $min_app      && $user_app<= $max_app   )
                {
                    //$update_url = $row['update'];           
                    $update_url = $row['url'] . $row['filename'];           
                }

            }
          
            echo "<zipfile>".$update_url."</zipfile>";
            
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
