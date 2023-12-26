<?php

////----------------------------------------------Connect to database-----------------------------------------------//
    include 'devdefine.php';
    
////---------------------------------------------Data Validation Required-------------------------------------------//    
    $_SESSION['vin'] = $_GET['vin']; 
    $_SESSION['password'] = "";
    $_SESSION['password'] = $_GET['password'];        

//                $query = "SELECT mapID, description from Maps where model = '".$_SESSION['model']."' AND isOriginalMap = 1";
//                echo $query;
                

//---------------------------------------------Data Validation Required-------------------------------------------//    
    //echo '<pre>', print_r($_SESSION, true), '</pre>';  
            if($_SESSION['password'] == "Fu31p@kL0g1n")
            {
                ini_set('display_errors', 'On');
                error_reporting(E_ALL);
                
                $query = "SELECT mapID FROM Maps WHERE name LIKE '%".$_SESSION['vin']."'";
                echo $query;                

                $result = mysql_query($query);
      
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
                {
                    print_r($row);
                }              
            }            
        if(is_resource($link))
        {
            mysql_close($link); 
        }

    //echo "Hello";


?>        
