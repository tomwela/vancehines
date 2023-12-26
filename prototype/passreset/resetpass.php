<?php 

$dbhost = "localhost";
// $dbhost = "magwebdb00.dmz.maggroup.com";
$dbname = "vhfp3";
$dbusername = "vhfp3";
$dbpassword = "5111TDuc6";

// $dbhost = "127.0.0.1";
// $dbname = "vhfp3";
// $dbusername = "vhfp3";
// $dbpassword = "macvhfp3";

try{

$link = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

$pass= $_POST['pass'];
$code=$_POST['code'];

$stmt = $link->prepare("SELECT count(*) as `count` FROM  CodeReadUsers  where passwd_reset=:code");

$stmt->execute(array(
    "code" =>$code
));

//$stmt->execute(array($sUserCook));
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rowCount=$row[0]['count'];

if($rowCount==0) {
	echo "Unknown reset code";
	exit();
}




// foreach ($row as $key => $value) {
	
// 	echo $value["email"];
// }

$statement = $link->prepare("UPDATE  CodeReadUsers SET password=:pass WHERE passwd_reset=:resetcode");
$statement->execute(array(
    "pass" =>$pass,
    "resetcode" => $code
));


if($statement){ 
	$upd=$link->prepare("UPDATE  CodeReadUsers SET passwd_reset=:code WHERE passwd_reset=:resetcode");
	$upd->execute(array(
    "code" =>NULL,
    "resetcode" =>$code 
));

if($upd) echo "Password updated";
else echo "Problem updating password";
}

}//try
catch(Exception $e){
echo "Server error, try later";
}

 ?>