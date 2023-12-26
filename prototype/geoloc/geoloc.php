<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include 'define.php';

$_SESSION['make'] = $_GET['make'];
$_SESSION['password'] = "";
$_SESSION['password'] = $_GET['password'];

if($_SESSION['password'] == "Fu31p@kL0g1n")
{
    $query = "SELECT
                model,
                year,
                vin,
                ecmFirmware,
                ecmPart,
                fVersion,
                hVersion,
                aVersion,
                currentMap,
                updated,
                created installed,
                ipAddress
              From Customers
              WHERE ecmPart > 0.0  AND ";



    $string00 = " make = '".$_SESSION['make']."' AND ";

    $query00 =  substr_replace($string00, '', -5, -1);

    $query = $query . $query00."";
    //echo $query;

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

?>
