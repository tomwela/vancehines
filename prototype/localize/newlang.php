
<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FP3 Localize New Language</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: black;
            margin: 50px;
        }
    </style>
</head>


<body>

<img id="logo" class="pull-right" src="img/image-logo.png" />
<h1>FP3 Localize New Language</h1>

<?php
// If any errors please notify

echo '<h4>Please add new language and make first character uppercase and rest lowercase in a string.Thank you.</h4>';

if($error != "")
{

    echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}
?>

 <div id= "div1" align="left">
 <form method="post" action="addtablename.php">
 <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 50%;'>

 <tr><td width="30%" height="200"><font color = "black"> New Language:</font><font color="red">*</font></td><td width="72%" height="200"><textarea name= "table" style= "font-size: 12pt" rows="1" cols="50"/></textarea></td></tr><br/>
 <p><font color="red">*</font> fields are required</p>
 <tr><td width="100%" colspan="2" height="26"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>
</table>
</form>
</div>
</body>
</html>


















