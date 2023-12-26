<?php
/* This page will allow user to edit the record */

    function add($id, $uniqueKey, $androidKey, $iphoneKey, $description, $os, $modPosition, $modString, $error)
    {
?>
        <!DOCTYPE HTML>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <body bgcolor="white" style="color:black">
        <img src="img/image-logo.png" width="200" height="80"/> 
        <h1> Add new string </h1>
        </head>
        <body>
        <div align="left">
        <form method="post" action="">
        <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 60%;color:black;'>
        <tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>
        <tr> <td> <input type="hidden" name="language" value="<?php echo $_GET['language']; ?>"/></td></tr>
        <tr> <td> <p><strong>Language:</strong><?php echo $_GET['language'];?></p></td></tr>
        
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
            if ( isset($_POST['language']))
            {

                $uniqueKey = mysqli_real_escape_string($connection, $_POST['uniqueKey']);
                $androidKey= mysqli_real_escape_string($connection, $_POST['androidKey']);
                $iphoneKey= mysqli_real_escape_string($connection, $_POST['iphoneKey']);
                $description= mysqli_real_escape_string($connection, $_POST['description']);
                $os= mysqli_real_escape_string($connection, $_POST['os']);
                $modPosition= mysqli_real_escape_string($connection, $_POST['modPosition']);
                $modString= mysqli_real_escape_string($connection, $_POST['modString']);


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
                    //echo 'localize' .$name;
                     mysqli_query($connection,"INSERT INTO localize".$name." SET id='', sourceMod='True',uniqueKey='$uniqueKey',androidKey='$androidKey',iphoneKey='$iphoneKey',description='$description',os='$os',modPosition='$modPosition',modString='$modString' ")
                     or die(mysqli_error($connection)); 
                }

                header("Location: main.php?language=$language"); 


            }
            else
            {
                // if the 'id' isn't valid, display an error
                echo 'Submit Error!';
            }
        }

        else
        {
            // if the form hasn't been submitted, get the data from the db and display the form 
            // get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
            if ( (isset($_GET['language'])) )
            {
                // query db
                //$id = $_GET['id'];
                //$language= $_GET['language'];
                //$result = mysqli_query($connection, "SELECT * FROM localize".$language." WHERE id=$id")
                //or die(mysqli_error($connection)); 
                //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
                // check that the 'id' matches up with a row in the databse
                //if($row)
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
                    // show form
                    add($id, $uniqueKey, $androidKey, $iphoneKey, $description, $os, $modPosition, $modString, $error);

                }
                //else
                //    // if no match, display result
                //{
                //    echo "No results!";
                //}
            }
            else
                // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
            {
                echo 'Error!';
            }
        }
?>






