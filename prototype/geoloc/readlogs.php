<?php

////----------------------------------------------Connect to database-----------------------------------------------//
    include 'devengdefine.php';
    
////---------------------------------------------Data Validation Required-------------------------------------------//    
    $_SESSION['testid'] = $_GET['testid']; 
    $_SESSION['password'] = "";
    $_SESSION['password'] = $_GET['password'];        

//                $query = "SELECT mapID, description from Maps where model = '".$_SESSION['model']."' AND isOriginalMap = 1";
//                echo $query;
                

//---------------------------------------------Data Validation Required-------------------------------------------//    
    //echo '<pre>', print_r($_SESSION, true), '</pre>';  
            if($_SESSION['password'] == "Fu31pAkL0g1n")
            {
                ini_set('display_errors', 'On');
                error_reporting(E_ALL);
                
                $query = "SELECT LogMessage, OS, TestId, timestamp FROM FP3TestLogs WHERE TestId like '%".$_SESSION['testid']."' ORDER BY timestamp ASC";

                #echo $query;                

                $result = mysql_query($query);
      
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
                {
                    print_r($row);
                    echo ":$:^:<br>";   // unique delimeter to split string
                    
                }              
            }            
        if(is_resource($link))
        {
            mysql_close($link); 
        }

    //echo "Hello";


?>        
