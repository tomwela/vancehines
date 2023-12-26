<?php

    require_once('php/connectdb.php');

?>
	<!DOCTYPE HTML>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<body bgcolor="white" style="color:black">
	<img src="img/image-logo.png" width="200" height="80"/>	
	<h1> Edit: DTC </h1>
	</head>
	<body>

<?php

    echo "<left><H2>". $_GET['code']. "</H2></left>";
    echo "<br>";

    $code = $_GET['code'];
    //$language = $_GET['language'];
    $username = $_GET['username'];

    $result = mysqli_query($connection, "SELECT * FROM dtc WHERE code='$code'")
    or die(mysqli_error($connection)); 
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);


    // check that the 'id' matches up with a row in the databse
    if($row )
    {
        $short = $row['short'];
        $description = $row['description']; 
        $error = "";

    }



?>


        <form method="post" action="">
        <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 60%;color:black;'>

        <tr> <td> <input type="hidden" name="code" value="<?php echo $code; ?>"/></td></tr>
        


        <!-- <tr> <td width="2%" height="30">code:</td><td width="2%" height="30"><textarea name="code" style= "font-size: 12pt" cols="100" rows="1" id="code"><?php echo $code;?></textarea></td></tr><br/> -->
        
        <tr><td width="2%" height="30">short:</td><td width="2%" height="30"><textarea name="short" style= "font-size: 12pt" cols="100" rows="1" id="short"><?php echo $short;?></textarea></td></tr><br/>
    
        <tr><td width="2%" height="30">description:</td><td width="2%" height="30"><textarea name="description" style= "font-size: 12pt" cols="100" rows="10" id="description"><?php echo $description;?></textarea></td></tr><br/>

        <tr><td width="100%" height="30"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>



        </table>
        </form>

	</body>
	</html>

<?php


        if (isset($_POST['submit']))
        {
            echo "hello";
            echo $code;
            echo $_POST['short'];
            echo $_POST['description'];


            //$code = mysqli_real_escape_string($connection, $_POST['code']);
            $short= mysqli_real_escape_string($connection, $_POST['short']);
            $description= mysqli_real_escape_string($connection, $_POST['description']);


            $newstate = "False";
            $q = "UPDATE dtc SET short='$short',description='$description' WHERE code='$code'";

            //LANGUAGE FOR FUTURE REFERENCE?

            mysqli_query($connection,$q)
            or die(mysqli_error($connection)); 

            header('Location: main_dtc.php?username=' . $username .'&language=English ');

        }

?>

