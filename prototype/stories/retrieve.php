<?php


   require_once('php/connectdb.php');

    $string = "SELECT * FROM topstories order by id DESC " ;   

    $result = mysqli_query($connection,$string);

    while ($row = mysqli_fetch_array($result,MYSQL_ASSOC)) 
       
    {
        
          $json[] = array_map(null,$row);
          
    }

    $result = json_encode($json);         
    if($result == "null"){
    
        $result = "[{\"ID\":\"0\",\"thumbimgurl\":\"null\",\"headlines\":\"null\",\"date\":\"null\"}]";
    }
        
    echo $result;

?>        
