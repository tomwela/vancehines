<?php
/* This page will allow user to edit the record */
    $username = $_GET['username'];

    function add($id,$min_firmware,$firmware,$hardware,$app,$os,$filename,$vin,$wb_min_firmware,$wb_max_firmware,$wb_hardware,$wb_filename,$wb_vin, $error)
    {
?>
        <!DOCTYPE HTML>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <body bgcolor="white" style="color:black">
        <img src="img/image-logo.png" width="200" height="80"/> 
        <h1> Add new entry </h1>
        </head>
        <body>
        <div align="left">
        <form method="post" action="">
        <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 60%;color:black;'>
        <tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>


        <tr> <td width="2%" height="30">min_firmware:</td>
             <td width="2%" height="30"><textarea name="min_firmware" style= "font-size: 12pt" cols="100" rows="1" id="min_firmware"><?php echo $min_firmware;?></textarea></td></tr><br/>
        
        <tr><td width="2%" height="30">firmware:</td>
            <td width="2%" height="30"><textarea name="firmware" style= "font-size: 12pt" cols="100" rows="1" id="firmware"><?php echo $firmware;?></textarea></td></tr><br/>
    
        <tr><td width="2%" height="30">hardware:</td>
            <td width="2%" height="30"><textarea name="hardware" style= "font-size: 12pt" cols="100" rows="1" id="hardware"><?php echo $hardware;?></textarea></td></tr><br/>

        <tr><td width="2%" height="30">app:</td>
            <td width="2%" height="30"><textarea name="app" style= "font-size: 12pt" cols="100" rows="1" id="app"><?php echo $app;?></textarea></td></tr><br/>

        <tr><td width="2%" height="30">os:</td>
            <td width="2%" height="30"><textarea name="os" style= "font-size: 12pt" cols="100" rows="1" id="os"><?php echo $os;?></textarea></td></tr><br/>

        <tr><td width="2%" height="30">filename:</td>
            <td width="2%" height="30"><textarea name="filename" style= "font-size: 12pt" cols="100" rows="1" id="filename"><?php echo $filename;?></textarea></td></tr><br/>

        <tr><td width="2%" height="30">vin:</td>
            <td width="2%" height="30"><textarea name="vin" style= "font-size: 12pt" cols="100" rows="1" id="vin"><?php echo $vin;?></textarea></td></tr><br/>



        <tr><td width="2%" height="30">wb_min_firmware:</td>
            <td width="2%" height="30"><textarea name="wb_min_firmware" style= "font-size: 12pt" cols="100" rows="1" id="wb_min_firmware"><?php echo $wb_min_firmware;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">wb_max_firmware:</td>
            <td width="2%" height="30"><textarea name="wb_max_firmware" style= "font-size: 12pt" cols="100" rows="1" id="wb_max_firmware"><?php echo $wb_max_firmware;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">wb_hardware:</td>
            <td width="2%" height="30"><textarea name="wb_hardware" style= "font-size: 12pt" cols="100" rows="1" id="wb_hardware"><?php echo $wb_hardware;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">wb_filename:</td>
            <td width="2%" height="30"><textarea name="wb_filename" style= "font-size: 12pt" cols="100" rows="1" id="wb_filename"><?php echo $wb_filename;?></textarea></td></tr><br/>


        <tr><td width="2%" height="30">wb_vin:</td>
            <td width="2%" height="30"><textarea name="wb_vin" style= "font-size: 12pt" cols="100" rows="1" id="wb_vin"><?php echo $wb_vin;?></textarea></td></tr><br/>


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
            if (1) // ( isset($_POST['language']))
            {

                $min_firmware = mysqli_real_escape_string($connection, $_POST['min_firmware']);
                $firmware = mysqli_real_escape_string($connection, $_POST['firmware']);
                $hardware = mysqli_real_escape_string($connection, $_POST['hardware']);
                $app = mysqli_real_escape_string($connection, $_POST['app']);
                $os = mysqli_real_escape_string($connection, $_POST['os']);
                $filename = mysqli_real_escape_string($connection, $_POST['filename']);
                $vin = mysqli_real_escape_string($connection, $_POST['vin']);

                $wb_min_firmware = mysqli_real_escape_string($connection, $_POST['wb_min_firmware']);
                $wb_max_firmware = mysqli_real_escape_string($connection, $_POST['wb_max_firmware']);
                $wb_hardware = mysqli_real_escape_string($connection, $_POST['wb_hardware']);                
                $wb_filename = mysqli_real_escape_string($connection, $_POST['wb_filename']);
                $wb_vin = mysqli_real_escape_string($connection, $_POST['wb_vin']);

                
                // $q = "INSERT INTO ota SET min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',vin='$vin',wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_firmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename',wb_vin='$wb_vin'";


                $q = "INSERT INTO ota SET min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_firmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename',url='http://m.vhfp3.com/prototype/ota/versions/',wb_url='http://m.vhfp3.com/prototype/ota/versions/'";


                echo $q;
                mysqli_query($connection,$q) 
                or die(mysqli_error($connection)); 

                header("Location: main.php?language=English&username=$username"); 


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

            {
                // query db

                $min_firmware= $row['min_firmware'];
                $firmware= $row['firmware']; 
                $hardware= $row['hardware'];
                $app= $row['app'];
                $os= $row['os'];
                $filename= $row['filename'];
                $vin= $row['vin'];
                $wb_min_firmware= $row['wb_min_firmware'];
                $wb_max_firmware= $row['wb_max_firmware'];
                $wb_hardware= $row['wb_hardware'];
                $wb_filename= $row['wb_filename']; 
                $wb_vin= $row['wb_vin'];
                $error = "";


                // show form
                add($id,$min_firmware,$firmware,$hardware,$app,$os,$filename,$vin,$wb_min_firmware,$wb_max_firmware,$wb_hardware,$wb_filename,$wb_vin, $error);

            }
            // else
            //     // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
            // {
            //     echo 'Error!';
            // }

        }
?>






