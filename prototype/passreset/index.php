<?php

$code=$_GET['c'];
 ?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="main.js"></script>
     <link rel="stylesheet" type="text/css" href="main.css?v={random number/string}">
</head>
<body>
<div class="container ">
  <div class="row head">
<img src="coderead.png" class="center-block img-responsive">
  </div>
  <div class="feed" ></div>
<table>

<tr>

<td><input type="password" class="pass1" placeholder ="New password"></td>
</tr>

<tr> <td><input type="password" class="pass2" placeholder ="Confirm password"></td></tr>
<tr>
  
  <td><div class="reset text-center" rel="<?php echo $code; ?>">SUBMIT</div></td>
</tr>
</table>
<div class="trash ">Please check the trash or spam folder if you did not get an email</div>
</div>

</body>
</html>