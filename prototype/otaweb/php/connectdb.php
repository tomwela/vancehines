<?php

$server = 'localhost';
$user = 'vhfp3';
$pass = '5111TDuc6';
$db = 'vhfp3';

  $connection = mysqli_init();
  if (!$connection) {
      die('mysqli_init() failed');
  }
  
  // 'SET CHARACTER SET utf8'   'SET NAMES "utf8"'
/*
  if (!mysqli_options($connection, MYSQLI_INIT_COMMAND, 'SET CHARACTER SET utf8')) {
      die("Setting 'SET CHARACTER SET utf8' failed");
  }
*/
  
  
/*
  if (!mysqli_options($connection, MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
      die("Setting 'SET AUTOCOMMIT = 0' failed");
  }
*/
  
/*
  if (!mysqli_options($connection, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
      die("Setting MYSQLI_OPT_CONNECT_TIMEOUT failed");
  }
*/
  
  if (!mysqli_real_connect($connection, $server, $user, $pass, $db)) {
      die('Connect Error (' . mysqli_connect_errno() . ') '
              . mysqli_connect_error());
  }
