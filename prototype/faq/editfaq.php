<?php
/* This page will allow user to edit the record */

	function add($id, $Question, $Answer, $OS, $Priority, $error)
	{
?>
		<!DOCTYPE HTML>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<body bgcolor="black" style="color:white">
		<img src="img/image-logo.png" width="200" height="80"/>	
		<h1> Update FAQ </h1>
		</head>
		<body>
 		<div align="center">
 		<form method="post" action="">
 		<table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 50%;color:black;'>
 		<tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>
 		<tr><td><p> <strong>ID:</strong><?php echo $id; ?></p></td></tr>
 		<tr> <td> <input type="hidden" name="language" value="<?php echo $_GET['language']; ?>"/></td></tr>
 		<tr><td><p><strong>Language:</strong><?php echo $_GET['language'];?></p></td></tr>
 		<tr><td width="2%" height="10">Question:</td><td width="2%" height="10"><textarea name="Question" style= "font-size: 12pt" cols="30" rows="5" id="Question"><?php echo $Question;?></textarea></td></tr><br/>
  		<tr><td width="2%" height="10">Answer:</td><td width="2%" height="10"><textarea name="Answer" style= "font-size: 12pt" cols="30" rows="5" id="Answer"><?php echo $Answer;?></textarea></td></tr><br/>
		
		<tr><td width="2%" height="10"><font color="black">OS:</font><font color="red">*</font></td><td width="2%" height="10">
   		<select name="OS" id="OS" style="width: 320px; font-size: 50pt;">
   		<?php
		switch ($OS) {
		    case "Android":
		    	echo "<option value='$OS'>$OS";
		    	echo "<option value='iOS'>iOS";
		    	echo "<option value='Both'>Both</option>";
		        break;
		    case "iOS":
		    	echo "<option value='$OS'>$OS";
		    	echo "<option value='Android'>Android";
		    	echo "<option value='Both'>Both</option>";
		        break;
		    case "Both":
		        echo "<option value='$OS'>$OS";
		        echo "<option value='iOS'>iOS</option>";
		    	echo "<option value='Android'>Android";
		    	
		        break;
		    case "":
		   		echo "<option value='$OS'>$OS";
		  		echo "<option value='iOS'>iOS";
		    	echo "<option value='Android'>Android";
		    	echo "<option value='Both'>Both</option>";
		    	break;
		}
		?>
    	</select>

    	
		<tr><td width="2%" height="10">Priority:</td><td width="2%" height="10"><textarea name="Priority" style= "font-size: 12pt" cols="30" rows="5" id="Priority"><?php echo $Priority;?></textarea></td></tr><br/>
		<tr><td width="12%" colspan="2" height="26"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>
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
 				$id = $_POST['id'];
 				$language= $_POST['language'];
 				$Question = mysqli_real_escape_string($connection, $_POST['Question']);
 				$Answer = mysqli_real_escape_string($connection, $_POST['Answer']);
 				$OS = mysqli_real_escape_string($connection, $_POST['OS']);
 				$Priority = mysqli_real_escape_string($connection, $_POST['Priority']);
 				// check that firstname/lastname fields are both filled in
 				if ($Question == '' || $Answer == '')
 				{
 					//------generate error message------
 					$error = 'ERROR: Please fill in all required fields!';
 					//error, display form
 					add($id, $Question, $Answer, $OS, $Priority, $error);
 				}
 				else
 				{
 					// save the data to the database
 					mysqli_query($connection, "UPDATE faq".$language." SET Question='$Question', Answer='$Answer', OS='$OS', Priority='$Priority' WHERE id='$id'")
 					or die(mysqli_error($connection)); 
 					// once saved, redirect back to the main page
 					header("Location: main.php?language=$language"); 
 				}
 			}
 			else
 			{
 				// if the 'id' isn't valid, display an error
 				echo 'Error!';
 			}
 		}
 		else
 			// if the form hasn't been submitted, get the data from the db and display the form
 		{
 
 			// get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
			if ((isset($_GET['id'])>0) && (isset($_GET['language'])))
 			{
 				// query db
 				$id = $_GET['id'];
 				$language= $_GET['language'];
 				$result = mysqli_query($connection, "SELECT * FROM faq".$language." WHERE id=$id")
				or die(mysqli_error($connection)); 
 				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
 				// check that the 'id' matches up with a row in the databse
 				if($row)
 				{
 					// get data from db
 					$Question = $row['Question'];
 					$Answer = $row['Answer'];
  					$OS = $row['OS'];
  					$Priority = $row['Priority'];
 					// show form
 					add($id, $Question, $Answer, $OS, $Priority,'');
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
 				echo 'Error!';
			}
 		}
?>