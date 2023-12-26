<?php 
$enc="1524";
echo base64_encode($enc);

$dec="MTUyNA==";
echo "\n".base64_decode($dec);

 ?>