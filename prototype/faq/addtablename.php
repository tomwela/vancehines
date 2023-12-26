<?php
require_once('php/connectdb.php');
//if (isset($_POST['table']))
//assign the value in the variable

$table = "faq".$_POST['table'];
 mysqli_query($connection,"CREATE TABLE $table (
    `id` int primary key auto_increment,
    Question VARCHAR(1000), 
    Answer VARCHAR(1000), OS VARCHAR(200), Priority INT) CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci") or  die(mysql_error());
//$sql="CREATE TABLE $table (ID INT, Question VARCHAR(200), Answer VARCHAR(200)";
//$result=mysql_query($sql);
echo "Table Created";
header("location:main.php");
?>
