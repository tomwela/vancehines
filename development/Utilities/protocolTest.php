<?php  
   	  //echo $_SERVER['HTTPS'] ."<br />";
   	  $uri = NULL;
      if ($uri === null) $uri = (substr($_SERVER['SERVER_PROTOCOL'], 0, 5) == 'HTTPS' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      
      //echo $uri  .'<br />';
 
// 	  
// 	  
// 	  if (isset($_SERVER['HTTPS'])) {
// 	  	echo '$_SERVER[\'HTTPS\'] is '. $_SERVER['HTTPS'];
// 	  }	else {
// 	  	echo '$_SERVER[\'HTTPS\'] is OFF!';
// 	  }
// 	  
// 	  echo "<br /><br />";
//       echo 'SERVER_PROTOCOL: '. $_SERVER['SERVER_PROTOCOL'] .'<br />';
//       echo 'HTTP_HOST: '. $_SERVER['HTTP_HOST'] .'<br />';
//       echo 'REQUEST_URI: '. $_SERVER['REQUEST_URI'] .'<br />';
//       
//       
//       echo substr($_SERVER['SERVER_PROTOCOL'], 0, 5);
//       
// 	  echo "<br /><br />";
// 	  if (isset($_SERVER['HTTPS'])) {
// 	  	$uri = 'https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// 	  }	else {
// 	  	$uri = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// 	  }      
// 	  echo $uri;

      //if ($uri === null) $uri = (substr($_SERVER['SERVER_PROTOCOL'], 0, 5) == 'HTTPS' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $uri = null;
      if ($uri === null) $uri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	  echo $uri;      

?>