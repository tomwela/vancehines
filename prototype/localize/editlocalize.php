<?php
/* This page will allow user to edit the record */

	function add($id, $uniqueKey, $androidKey, $iphoneKey, $description, $os, $modPosition, $modString, $language, $srcDesc,  $error)
	{
?>
		<!DOCTYPE HTML>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<body bgcolor="white" style="color:black">
		<img src="img/image-logo.png" width="200" height="80"/>	
		<h1> Edit: Translate from English to <?php echo $language;?> </h1>
		</head>
		<body>

<?php

    //echo $language;
    //echo $id;
    //echo "<br>";
    echo "<left><H2>". $srcDesc. "</H2></left>";
    echo "<br>";
?>



 		<div align="left">
 		<form method="post" action="">
 		<table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 60%;color:black;'>

        <tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>
        <tr> <td> <input type="hidden" name="language" value="<?php echo $_GET['language']; ?>"/></td></tr>



        <tr> <td width="2%" height="30">uniqueKey</td><td width="2%" height="30"><textarea name="uniqueKey" style= "font-size: 12pt" cols="100" rows="1" id="uniqueKey"><?php echo $uniqueKey;?></textarea></td></tr><br/>
  		
        <tr><td width="2%" height="30">androidKey</td><td width="2%" height="30"><textarea name="androidKey" style= "font-size: 12pt" cols="100" rows="1" id="androidKey"><?php echo $androidKey;?></textarea></td></tr><br/>
	
		<tr><td width="2%" height="30">iphoneKey:</td><td width="2%" height="30"><textarea name="iphoneKey" style= "font-size: 12pt" cols="100" rows="1" id="iphoneKey"><?php echo $iphoneKey;?></textarea></td></tr><br/>

        <tr><td width="2%" height="30">description:</td><td width="2%" height="30"><textarea name="description" style= "font-size: 12pt" cols="100" rows="10" id="description"><?php echo $description;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">modString:</td><td width="2%" height="30"><textarea name="modString" style= "font-size: 12pt" cols="100" rows="1" id="modString"><?php echo $modString;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">os:</td><td width="2%" height="30"><textarea name="os" style= "font-size: 12pt" cols="100" rows="1" id="os"><?php echo $os;?></textarea></td></tr><br/>


        <tr><td width="100%" height="30"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>



		</table>
		</form>
		</div>
		</body>
		</html>


		<?php

	}
		require_once('php/connectdb.php');
                
		//-----check if the form has been submitted.-------

 		if (isset($_POST['submit']))
 		{ 
 			// confirm that the 'id' value is a valid integer before getting the form data
 			if (is_numeric($_POST['id']) && isset($_POST['language']))
 			{
 				//----get form data, making sure it is valid-----
 				// $id = $_POST['id'];
 				// $language= $_POST['language'];
 				// $uniqueKey = mysqli_real_escape_string($connection, $_POST['uniqueKey']);
 				// $androidKey = mysqli_real_escape_string($connection, $_POST['androidKey']);
 				// $OS = mysqli_real_escape_string($connection, $_POST['os']);
 				// $iphoneKey = mysqli_real_escape_string($connection, $_POST['iphoneKey']);
 				// check that firstname/lastname fields are both filled in
 				// if ($Question == '' || $Answer == '')
 				// {
 				// 	//------generate error message------
 				// 	$error = 'ERROR: Please fill in all required fields!';
 				// 	//error, display form
 				// 	add($id, $Question, $Answer, $OS, $Priority, $error);
 				// }
 				// else
 				// {
 				// 	// save the data to the database
 				// 	mysqli_query($connection, "UPDATE localize".$language." SET Question='$Question', Answer='$Answer', OS='$OS', Priority='$Priority' WHERE id='$id'")
 				// 	or die(mysqli_error($connection)); 
 				// 	// once saved, redirect back to the main page
 				// 	header("Location: main.php?language=$language"); 
 				// }

                $id = $_POST['id'];
                $language= trim($_POST['language']);

                $uniqueKey = mysqli_real_escape_string($connection, $_POST['uniqueKey']);
                $androidKey= mysqli_real_escape_string($connection, $_POST['androidKey']);
                $iphoneKey= mysqli_real_escape_string($connection, $_POST['iphoneKey']);
                $description= mysqli_real_escape_string($connection, $_POST['description']);
                $os= mysqli_real_escape_string($connection, $_POST['os']);
                $modPosition= mysqli_real_escape_string($connection, $_POST['modPosition']);
                $modString= mysqli_real_escape_string($connection, $_POST['modString']);

                //echo $language;

                $newstate = "False";
                $q = "UPDATE localize".$language." SET id='$id',sourceMod='". $newstate ."',uniqueKey='$uniqueKey',androidKey='$androidKey',iphoneKey='$iphoneKey',description='$description',os='$os',modPosition='$modPosition',modString='$modString' WHERE uniqueKey='$uniqueKey'";

                mysqli_query($connection,$q)
                or die(mysqli_error($connection)); 

                //header("Location: main.php?language=$language"); 


                $sql = "SHOW TABLES ";
                $result = mysqli_query($connection, $sql);
                $tableNames = array();
                while ($row = mysqli_fetch_row($result)) {
                    if (substr($row[0], 0, 8) == "localize") {
                        $tableNames[] = substr($row[0], 8);
                    }
                }

                foreach ($tableNames as $name) 
                {

                    #echo $name;
                    #echo "<BR>";


                    if ( $name == $language )
                    {
                        $newstate = "False";

                        if ( $language == "English" )
                        {

                            //$newstring = str_replace("u000D","\\\u000D", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u0026","\\\u0026", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u0022","\\\u0022", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u0022","\\\u0022", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u2026","\\\u2026", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u0027","\\\u0027", $description);
                            //$description = $newstring;


                            //$newstring = str_replace("u00B0","\\\u00B0", $description);
                            //$description = $newstring;

                            //$newstring = str_replace("u00B7","\\\u00B7", $description);
                            //$description = $newstring;                            


                        }

                        if ( $language == "French" )
                        {

                            $newstring = str_replace("u000D","\\\u000D", $description);
                            $description = $newstring;

                            $newstring = str_replace("&","\\\u0026", $description);
                            $description = $newstring;

                            $newstring = str_replace("”","\\\u0022", $description);
                            $description = $newstring;

                            $newstring = str_replace("“","\\\u0022", $description);
                            $description = $newstring;

                            $newstring = str_replace("…","\\\u2026", $description);
                            $description = $newstring;

                            $newstring = str_replace("’","\\\u0027", $description);
                            $description = $newstring;


                            $newstring = str_replace("°","\\\u00B0", $description);
                            $description = $newstring;

                            $newstring = str_replace("·","\\\u00B7", $description);
                            $description = $newstring;                            

                        }


                        if ( $language == "Spanish" )
                        {

                            $newstring = str_replace("&","\\\u0026", $description);
                            $description = $newstring;

                            $newstring = str_replace("”","\\\u0022", $description);
                            $description = $newstring;

                            $newstring = str_replace("“","\\\u0022", $description);
                            $description = $newstring;

                            $newstring = str_replace("…","\\\u2026", $description);
                            $description = $newstring;

                            $newstring = str_replace("’","\\\u0027", $description);
                            $description = $newstring;


                            $newstring = str_replace("°","\\\u00B0", $description);
                            $description = $newstring;

                            $newstring = str_replace("·","\\\u00B7", $description);
                            $description = $newstring;                            


                            $newstring = str_replace("á","\\\u00E1", $description);
                            $description = $newstring;

                            $newstring = str_replace("é","\\\u00E9", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("í","\\\u00ED", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("ó","\\\u00F3", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("ú","\\\u00FA", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("ü","\\\u00FC", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("ñ","\\\u00F1", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("¿","\\\u00BF", $description);
                            $description = $newstring;                            

                            $newstring = str_replace("¡","\\\u00A1", $description);
                            $description = $newstring;                            




                        }


                        $q = "UPDATE localize".$language." SET id='$id',sourceMod='". $newstate ."',uniqueKey='$uniqueKey',androidKey='$androidKey',iphoneKey='$iphoneKey',description='$description',os='$os',modPosition='$modPosition',modString='$modString' WHERE uniqueKey='$uniqueKey'";

                    }
                    else
                    {
                        if ($name == "English")
                        {
                            $newstate = "False";   
                        }
                        else
                        {
                            $newstate = "True";                               
                        }
                        $q = "UPDATE localize".$name." SET id='$id',sourceMod='". $newstate ."' WHERE uniqueKey='$uniqueKey'";
                    }


                        mysqli_query($connection,$q)
                        or die(mysqli_error($connection)); 

                        //header("Location: main.php?language=$language"); 
                    

                }





  


                header("Location: main.php?language=$language"); 


 			}
 			else
 			{
 				// if the 'id' isn't valid, display an error
 				echo 'Error submit!';
 			}
 		}

 		else
 		{
            // if the form hasn't been submitted, get the data from the db and display the form 
 			// get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
			if ((isset($_GET['id'])>0) && (isset($_GET['language'])))
 			{
 				// query db
 				$id = $_GET['id'];
 				$language= $_GET['language'];
 				$result = mysqli_query($connection, "SELECT * FROM localize".$language." WHERE id=$id")
				or die(mysqli_error($connection)); 
 				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
 				// check that the 'id' matches up with a row in the databse
 				if($row )
 				{


 					// get data from db
 					$uniqueKey = $row['uniqueKey'];
 					$androidKey = $row['androidKey'];
  					$iphoneKey = $row['iphoneKey'];
                    $os = $row['os'];
                    $description = $row['description'];
                    $modPosition = $row['modPosition'];
                    $modString = $row['modString']; 
 					$error = "";


                    if ( $language !="English")
                    {

                        $q = "SELECT * FROM localizeEnglish WHERE uniqueKey='". $uniqueKey . "'" ;
                        $result2 = mysqli_query($connection, $q )
                        or die(mysqli_error($connection)); 

                        //echo $q;
                        $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
                        $desc = $row2['description'];

                    }
                    else
                    {
                        $desc = "";
                    }



                    // show form
                    add($id, $uniqueKey, $androidKey, $iphoneKey, $description, $os, $modPosition, $modString, $language, $desc, $error);

 				}
 				else
 					// if no match, display result
 				{
 					echo "No results!";
 				}
 			}
 			else
 				// if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
 			{
 				echo 'Error show!';
			}
 		}
?>