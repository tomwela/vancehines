<?php 

require_once('connect.php');
// session_start();
// if(!isset($_SESSION['serverTest'])){
// header('Location:login.php');
// }
//header( "refresh:2;url=search.php" );
try {
 
 $db = new PDO("mysql:host=$servername; dbname=$dbname;charset=utf8","$username","$password");
 
$rand=rand(0,100000);
$rand2=rand(1,9);
 

$query=$db->prepare("select substring(m.mapID,5) as `id`, substring(REPLACE(SUBSTRING_INDEX(m.name, '_', -1), '0',$rand2),6) as vin, m.photoUrl, substring(m.`customerID`,4) as cid from Maps as m left JOIN Customers as c on c.`currentMap` = m.`mapID` where  m.photoUrl !='' and m.photoUrl !='x' and m.photoUrl !='http://vhfp3.com/media/photos/no_img_available.jpg'  LIMIT $rand, 1500 ");
  
 $query->execute();
 
 $bikesDB='';
  $bikesDB.= "<div class='table-responsive'><table class='table table-striped'>
<thead ><th >Vin</th><th >Map id</th><th>Customer id</th><th >Photo</th></thead>
 <tbody>";
while($sel=$query->FETCH(PDO::FETCH_ASSOC)){
  $img= ($sel['photoUrl']=='' or $sel['photoUrl']=='x') ? 'http://vhfp3.com/media/photos/no_img_available.jpg' : $sel['photoUrl'];
  //$img=$sel['photoUrl'];
  //$media=($sel['v'] ==34 or $sel['v'] ==36 or $sel['v'] ==41 or $sel['v'] ==43) ? 'vh/'.$sel['v'] : $sel['year'];
    //$media=$sel['year'];
  // $downLink="http://dev.vhfp3.com/development/media/".$media."/".$sel['currentMap'].".map'";

   
    $bikesDB.="<tr><td><div class='vinId'>".trim($sel['vin'])."</div></td><td>map".$sel['id']."</td><td>c".$sel['cid']."</td><td><img src='".$img."' width='150' height='75' class='bikeImg img-responsive'></td></tr>";
   //$bikesDB.="<tr><td><div class='vinId' rel='".$sel["vin"]."'><a href='search.php?vin=".$sel["vin"]."'>".trim($sel['vin'])."</a></div></td><td>".$sel['currentMap']."</td><td>".$sel['year']."</td><td>".$sel['fw']."</td></tr>";
  
}
$bikesDB.="</tbody></table>


</div>";



}

catch(PDOException  $e ){
echo "Error: ".$e;
$db = null;

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
 <title>Server Test</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/main.css?v=<?=time();?>">
  <script type="text/javascript" src='js/main.js'></script>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
<a href="index.php"><img src="images/logo.png" class="logo" /></a>

 
</nav>

<div class="container mainCon"><?php 
echo $bikesDB;

 
?> </div>
</body>
</html>