<?php
session_start();
/* This page will allow user to edit the record */
    $username = $_GET['username'];

	function add($id,$min_firmware,$firmware,$hardware,$app,$os,$filename,$vin,$wb_min_firmware,$wb_max_firmware,$wb_hardware,$wb_filename,$wb_vin,$ck,$bt,$aut, $error)
	{
        $ckdCksum=''; $ckBt=''; $ckAut='';
        if($ck==1) $ckdCksum='checked';
        if($bt==1) $ckBt='checked';
        if($aut==1) $ckAut='checked';

?>
	
  <?php require_once('header.php'); ?>

  <h2 class="localize_title"> Edit: OTA info </h2>

 <table class="localize_table">
 	<form method="post" action="">
 		

        <tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>
        <tr> <td> <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>"/></td></tr>


        <tr> <td >min_firmware:</td>
             <td ><textarea name="min_firmware"   id="min_firmware"><?php echo $min_firmware;?></textarea></td></tr>
  		
        <tr><td >firmware:</td>
            <td ><textarea name="firmware"  id="firmware"><?php echo $firmware;?></textarea></td></tr>
	
		<tr><td >hardware:</td>
            <td ><textarea name="hardware"   id="hardware"><?php echo $hardware;?></textarea></td></tr>

        <tr><td >app:</td>
            <td ><textarea name="app"   id="app"><?php echo $app;?></textarea></td></tr>

        <tr><td >os:</td>
            <td ><textarea name="os"   id="os"><?php echo $os;?></textarea></td></tr>

        <tr><td >filename:</td>
            <td ><textarea name="filename"  rows="1" id="filename"><?php echo $filename;?></textarea></td></tr>

        <tr><td >vin:</td>
            <td ><textarea name="vin"  id="vin"><?php echo $vin;?></textarea></td></tr>



        <tr><td >wb_min_firmware:</td>
            <td ><textarea name="wb_min_firmware"   id="wb_min_firmware"><?php echo $wb_min_firmware;?></textarea></td></tr>


        <tr><td >wb_max_firmware:</td>
            <td ><textarea name="wb_max_firmware"   id="wb_max_firmware"><?php echo $wb_max_firmware;?></textarea></td></tr>


        <tr><td >wb_hardware:</td>
            <td ><textarea name="wb_hardware"   id="wb_hardware"><?php echo $wb_hardware;?></textarea></td></tr>


        <tr><td >wb_filename:</td>
            <td ><textarea name="wb_filename"   id="wb_filename"><?php echo $wb_filename;?></textarea></td></tr>


        <tr><td >wb_vin:</td>
            <td ><textarea name="wb_vin"   id="wb_vin"><?php echo $wb_vin;?></textarea></td></tr>


        <tr>
<!-- sy added -->

<tr><td>Checksum</td><td class="chkBox"><input type="checkbox" name="chk" value="1" <?php echo $ckdCksum; ?> > </td></tr>
<tr><td>Bootloader</td><td class="chkBox"><input type="checkbox" name="bootl" value="1" <?php echo $ckBt; ?>> </td></tr>
<tr><td>Autotune</td><td class="chkBox"><input type="checkbox" name="autot" value="1" <?php echo $ckAut; ?>></td></tr>


<!--             <td width="100%" height="30"><P align="center"><input type="duplicate" name="duplicate" value="Duplicate"></P></td> -->
            <td></td><td ><input type="submit" name="submit" class="submit" value="Submit"></td> 
        </tr>
        <tr ><td></td><td class="back"><a  href="http://dev.vhfp3.com/prototype/otaweb2/main.php?language=English&username=<?php echo  $_SESSION['username']; ?>">Back</a></td></tr>

</form>
		</table>
		

		</body>
		</html>


		<?php

	}
		require_once('php/connectdb.php');
                
		//-----check if the form has been submitted.-------

 		if (isset($_POST['submit']))
 		{ 
 			// confirm that the 'id' value is a valid integer before getting the form data
 			if (is_numeric($_POST['id']))
 			{

                $id = $_POST['id'];
               
                $min_firmware = mysqli_real_escape_string($connection, $_POST['min_firmware']);
                $firmware = mysqli_real_escape_string($connection, $_POST['firmware']);
                $hardware = mysqli_real_escape_string($connection, $_POST['hardware']);
                $app = mysqli_real_escape_string($connection, $_POST['app']);
                $os = mysqli_real_escape_string($connection, $_POST['os']);
                $filename = mysqli_real_escape_string($connection, $_POST['filename']);
                $vin = mysqli_real_escape_string($connection, $_POST['vin']);
                $vin=trim($vin);
                 $vin =  $vin=='' ? "vin=NULL" : "vin='$vin'";
               // $vin =  trim($_POST['vin']=='') ? null : $vin;
                $wb_min_firmware = mysqli_real_escape_string($connection, $_POST['wb_min_firmware']);
                $wb_max_firmware = mysqli_real_escape_string($connection, $_POST['wb_max_firmware']);
                $wb_hardware = mysqli_real_escape_string($connection, $_POST['wb_hardware']);                
                $wb_filename = mysqli_real_escape_string($connection, $_POST['wb_filename']);
                $wb_vin = mysqli_real_escape_string($connection, $_POST['wb_vin']);
                $wb_vin=trim($wb_vin);
                 $wb_vin =  $wb_vin=='' ? "wb_vin=NULL" : "wb_vin='$wb_vin'";

                $chk = (int)mysqli_real_escape_string($connection, $_POST['chk']);                
                $bootl =(int) mysqli_real_escape_string($connection, $_POST['bootl']);
                $autot = (int) mysqli_real_escape_string($connection, $_POST['autot']);


                $newstate = "False";
                $q = "UPDATE ota SET id='$id',min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',$vin,wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_firmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename',$wb_vin , chksum='$chk',btloader='$bootl',autotune='$autot' WHERE id='$id'";

                //echo $q;
                mysqli_query($connection,$q)
                or die(mysqli_error($connection)); 

                header("Location: main.php?language=English&username=$username"); 

 			}
 			else
 			{
 				// if the 'id' isn't valid, display an error
 				echo 'Error submit!';
 			}
 		}

        // else if (isset($_POST['duplicate']))
        // { 
        //     // confirm that the 'id' value is a valid integer before getting the form data
        //     if (1) //(is_numeric($_POST['id']))
        //     {

        //         //$id = $_POST['id'];
               
        //         $min_firmware = mysqli_real_escape_string($connection, $_POST['min_firmware']);
        //         $firmware = mysqli_real_escape_string($connection, $_POST['firmware']);
        //         $hardware = mysqli_real_escape_string($connection, $_POST['hardware']);
        //         $app = mysqli_real_escape_string($connection, $_POST['app']);
        //         $os = mysqli_real_escape_string($connection, $_POST['os']);
        //         $filename = mysqli_real_escape_string($connection, $_POST['filename']);
        //         $vin = mysqli_real_escape_string($connection, $_POST['vin']);

        //         $wb_min_firmware = mysqli_real_escape_string($connection, $_POST['wb_min_firmware']);
        //         $wb_max_firmware = mysqli_real_escape_string($connection, $_POST['wb_max_firmware']);
        //         $wb_hardware = mysqli_real_escape_string($connection, $_POST['wb_hardware']);                
        //         $wb_filename = mysqli_real_escape_string($connection, $_POST['wb_filename']);
        //         $wb_vin = mysqli_real_escape_string($connection, $_POST['wb_vin']);


                
        //         $q = "INSERT INTO ota SET min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_firmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename',url='http://m.vhfp3.com/prototype/ota/versions/',wb_url='http://m.vhfp3.com/prototype/ota/versions/'";

        //         //echo $q;
        //         mysqli_query($connection,$q)
        //         or die(mysqli_error($connection)); 

        //         header("Location: main.php?language=English&username=$username"); 

        //     }
        //     else
        //     {
        //         // if the 'id' isn't valid, display an error
        //         echo 'Error duplicate!';
        //     }
        // }


 		else
 		{

            // if the form hasn't been submitted, get the data from the db and display the form 
            // get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
            if (  isset($_GET['id'])>0 )
            {
                // query db
                $id = $_GET['id'];
                $result = mysqli_query($connection, "SELECT * FROM ota WHERE id=$id")
                or die(mysqli_error($connection)); 
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
                // check that the 'id' matches up with a row in the databse
                if($row )
                {

                    // get data from db
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
                    $chks= $row['chksum'];
                    $autot= $row['autotune'];
                    $bootl= $row['btloader'];
                    
                    $error = "";


                    

                    // show form
                    add($id,$min_firmware,$firmware,$hardware,$app,$os,$filename,$vin,$wb_min_firmware,$wb_max_firmware,$wb_hardware,$wb_filename,$wb_vin, $chks, $bootl, $autot, $error);

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