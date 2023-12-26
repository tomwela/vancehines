<?php
 
include 'devengdefine.php';

$_SESSION['password'] = "";
$_SESSION['password'] = $_GET['password'];        


if($_SESSION['password'] == "Fu31pAkL0g1n")
{      
    if (strlen($_GET['LogMessage']) == 0)
        {
         $LogMessage = 'LogMessage';
        }
    else 
        {
        $LogMessage = $_GET['LogMessage'];
        }

     if(strlen($_GET['OS']) == 0)
        {
            $OS = 'null';
        }
       else
        {
            $OS = $_GET['OS'];
        }

    if(strlen($_GET['Vin']) == 0)
        {
            $Vin = 'null';
        }
       else
        {
            $Vin = $_GET['Vin'];
        }

    if(strlen($_GET['TestId']) == 0)
        {
            $TestId = 'null';
        }
       else
        {
            $TestId = $_GET['TestId'];
        }
        
    if(strlen($_GET['timestamp']) == 0)
        {
            $timestamp = '0';
        }
       else
        {
            $timestamp = $_GET['timestamp'];
        }        
                
    //echo "$LogMessage" . "<br>\n";
    //echo "$Vin". "<br>\n";
    //echo "$OS". "<br>\n";
    //echo "$TestId". "<br>\n";
    
    // This SQL statement selects ALL from the table 'Locations'
    
    if ( $TestId == 'null')
    {
        $q1 = "SELECT DISTINCT TestId, timestamp, OS FROM FP3TestLogs";        
    }
    else
    {
        $q1 = "INSERT INTO FP3TestLogs ( LogMessage, OS, Vin , TestId, timestamp ) VALUES ( \"$LogMessage\", \"$OS\", \"$Vin\", \"$TestId\", \"$timestamp\"  )";
    }
    
    #echo $q1;
    $r1 = mysql_query ( $q1);
    #echo "<BR>";


        while ($row = mysql_fetch_array($r1, MYSQL_ASSOC)) 
        {
            print_r($row);
            echo "::<br>";   // unique delimeter to split string
        }       


    #echo "$latest";
    #echo "<BR>";


}
//else
//{
// echo "bad password";
//}

if(is_resource($link))
{
    mysql_close($link); 
}


?>