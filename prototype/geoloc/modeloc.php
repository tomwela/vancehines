<?php

////----------------------------------------------Connect to database-----------------------------------------------//
    include 'define.php';
    
////---------------------------------------------Data Validation Required-------------------------------------------//    
    $_SESSION['model'] = $_GET['model']; 
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
                
                $query = "SELECT mapID, description from Maps where model = '".$_SESSION['model']."' AND isOriginalMap = 1";
                //echo $query;
                
                
                //select mapID from Maps where model = 'Sportster' and isOriginalMap = 1;
                
                
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
