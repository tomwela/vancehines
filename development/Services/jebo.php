<?php 
// $str='R2Vla3Nmb3JHZWVrcw==';
// echo base64_decode($str);
// exit();
$tempMapC="823_2017.map";


  $f = fopen($tempMapC, "rb"); //read map
      // $fw =fopen($tempMap2,"wb");//create new map 
      $readMap=fread($f,256*96);//read stock map or default zero map
      $sectors=unpack('H*',$readMap);
     //var_dump($sectors[1]);
// $ff='';
// for ($i=0; $i < count($sectors[1]); $i++) { 
// 	$ff.= base64_encode(sprintf("%02X", $sectors[1][$i]));
// 	// echo "\n";

// }
// echo $ff;
// // // echo base64_encode($ff);

function hex_to_base64($sectors){
  $return = '';
  foreach(str_split($sectors, 2) as $pair){
    $return .= chr(hexdec($pair));
    // $return .= chr(hexdec(sprintf("%02X", $pair)));
    // echo $pair."\n";
  }
   
    return base64_encode($return);
}

 echo hex_to_base64($sectors[1]);
?>