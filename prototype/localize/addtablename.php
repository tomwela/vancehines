<?php
require_once('php/connectdb.php');
//if (isset($_POST['table']))
//assign the value in the variable

$language = $_POST['table'];
$table = "localize".$language;

$q = "CREATE TABLE $table (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueKey` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `androidKey` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iphoneKey` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `os` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modPosition` int(11) DEFAULT NULL,
  `modString` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourceMod` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueKey` (`uniqueKey`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

mysqli_query($connection, $q) or  die(mysql_error());
echo "Table Created";
echo "<BR>";


$q = "SELECT * FROM localizeEnglish";
$result = mysqli_query($connection, $q) or  die(mysql_error());


while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) 
{

    #var_dump($row);

    $uniqueKey = $row['uniqueKey'];
    $androidKey = $row['androidKey'];
    $iphoneKey = $row['iphoneKey'];
    $description = $row['description'];
    $os = $row['os'];
    $modPosition = $row['modPosition'];
    $modString = $row['modString'];

    //echo $uniqueKey;
    //echo $androidKey;


    $q = "INSERT INTO $table SET id='', sourceMod='True',uniqueKey='$uniqueKey',androidKey='$androidKey',iphoneKey='$iphoneKey',description='$description',os='$os',modPosition='$modPosition',modString='$modString' ";

    #echo $q;
    #echo "<BR>";

    mysqli_query($connection,$q)
    or die(mysqli_error($connection)); 
}


header("Location:main.php?language=$language"); 
//header("location:main.php");

?>
