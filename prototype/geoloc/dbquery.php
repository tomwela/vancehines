<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include 'define.php';

$_SESSION['query'] = $_GET['query'];
$_SESSION['make'] = $_GET['make'];
$_SESSION['password'] = "";
$_SESSION['password'] = $_GET['password'];

if($_SESSION['password'] == "Fu31p@kL0g1nDbAcc3ss")
{
    $query = $_SESSION['query'];
    //echo $query;
    $result = mysql_query($query);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
      print_r($row);
      //print_r("--d3l1m1t3r--");
    }
}

if(is_resource($link))
{
    mysql_close($link);
}

?>
