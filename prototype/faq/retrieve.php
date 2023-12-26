<?php

//----------------------------------------------Connect to database-----------------------------------------------//
    //include 'view.php';

    if(strlen($_GET['language']) == 0)
    {
        $language = 'English';
    }
   else
    {
        $language = $_GET['language'];
    }

    if(strlen($_GET['os']) == 0)
    {
        $os = 'null';
    }
   else
    {
        $os = $_GET['os'];
    }
//    echo $language;
        
                
//    ini_set('display_errors', 'On');
//    error_reporting(E_ALL);

    $check_language_table = "SELECT 1 FROM faq".$language;                

//    echo $check_language_table;

   require_once('php/connectdb.php');


    $count = mysqli_query($connection,$check_language_table);
    
//    echo $count;
        
            
    if($count == FALSE)
    {
        $language = 'English';
//        echo "False";        
    }
//    else
//    {
//        echo "Found it!";
//    }

  

    
    $string = "SELECT * FROM faq".$language." where OS='".$os."' or OS='Both' or OS='' ORDER BY Priority ASC" ;   
    
/*
    //for debuggin, display the query
    echo "<br /><br />";  
    echo $string;                     
    echo "<br /><br />"; 
*/                      

    $result = mysqli_query($connection,$string);
 



    while ($row = mysqli_fetch_array($result,MYSQL_ASSOC)) 
       
    {
        
          $json[] = array_map(null,$row);
          
    ;
    }
    $result = json_encode($json);         
    if($result == "null"){
    
      $result = "[{\"ID\":\"0\",\"Question\":\"null\",\"Answer\":\"null\"}]";
    }
    
//    $result = "[{\"ID\":\"0\",\"Question\":\"".$language."\",\"Answer\":\"".$os."\"}]";
    
    echo $result;

?>        
