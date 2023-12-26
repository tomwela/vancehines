
<!DOCTYPE html>
<html>
<head>
<body bgcolor="black" style="color:white">

<h4>Please add new language and make first character uppercase and rest lowercase in a string.Thank you.</h4>
</head>
<body>
<?php
// If any errors please notify

if($error != "")
{
    echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}
?>

 <div id= "div1" align="center">
 <form method="post" action="addtablename.php">
 <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 50%;'>

 <tr><td width="30%" height="282"><font color = "black"> New Language:</font><font color="red">*</font></td><td width="72%" height="282"><textarea name= "table" style= "font-size: 12pt" rows="4" cols="50"/></textarea></td></tr><br/>
 <p><font color="red">*</font> fields are required</p>
 <tr><td width="100%" colspan="2" height="26"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>
</table>
</form>
</div>
</body>
</html>


















