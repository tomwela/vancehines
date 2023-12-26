<?php
session_start();
error_reporting(0);
/* This page will allow user to edit the record */
    $username = $_GET['username'];

    function add($id,$min_firmware,$firmware,$hardware,$app,$os,$filename,$vin,$wb_min_firmware,$wb_max_firmware,$wb_hardware,$wb_filename,$wb_vin, $error)
    {
?>
       

  <?php require_once('header.php'); ?>

  <h2 class="localize_title"> Add new entry </h2>

 <table class="localize_table">
    <form method="post" action="">

     
        <tr> <td> <input type="hidden" name="id" value="<?php echo $id; ?>"/></td></tr>


        <tr> <td >min_firmware:</td>
             <td ><textarea name="min_firmware"   id="min_firmware"><?php echo $min_firmware;?></textarea></td></tr>
        
        <tr><td >firmware:</td>
            <td ><textarea name="firmware"   id="firmware"><?php echo $firmware;?></textarea></td></tr>
    
        <tr><td >hardware:</td>
            <td ><textarea name="hardware"   id="hardware"><?php echo $hardware;?></textarea></td></tr>

        <tr><td >app:</td>
            <td ><textarea name="app"   id="app"><?php echo $app;?></textarea></td></tr><br/>

        <tr><td >os:</td>
            <td ><textarea name="os"   id="os"><?php echo $os;?></textarea></td></tr>

        <tr><td >filename:</td>
            <td ><textarea name="filename"   id="filename"><?php echo $filename;?></textarea></td></tr>

        <tr><td >vin:</td>
            <td ><textarea name="vin"   id="vin"><?php echo $vin;?></textarea></td></tr>



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

      
 <tr><td>Checksum</td><td class="chkBox"><input type="checkbox" name="chk"  value="1"> </td></tr>
<tr><td>Bootloader</td><td class="chkBox"><input type="checkbox" name="bootl"  value="1"> </td></tr>
<tr><td>Autotune</td><td class="chkBox"><input type="checkbox" name="autot"  value="1" ></td></tr>



        <tr><td></td><td ><input type="submit" class="submit" name="submit" value="Submit"></td></tr>

        <tr ><td></td><td class="back"><a  href="http://dev.vhfp3.com/prototype/otaweb2/main.php?language=English&username=<?php echo  $_SESSION['username']; ?>">Back</a></td></tr>



      </form>

        </table>
       
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
                $vin=trim($vin);
                $vin = $vin=='' ? "vin=NULL" : "vin='$vin'";
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

                // $chk=1; $bootl=1;$autot=1;

              
                // $q = "INSERT INTO ota SET min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',vin='$vin',wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_firmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename',wb_vin='$wb_vin'";

                $q = "INSERT INTO ota SET min_firmware='$min_firmware',firmware='$firmware',hardware='$hardware',app='$app',os='$os',filename='$filename',$vin ,wb_min_firmware='$wb_min_firmware',wb_max_firmware='$wb_max_frmware',wb_hardware='$wb_hardware',wb_filename='$wb_filename', $wb_vin,`url`='http://m.vhfp3.com/prototype/ota/versions/',wb_url='http://m.vhfp3.com/prototype/ota/versions/', chksum='$chk',btloader='$bootl',autotune='$autot'";

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
                if(isset($_GET['id'])){
                   $id = $_GET['id'];
               
                $result = mysqli_query($connection, "SELECT * FROM ota WHERE id=$id") or die(mysqli_error($connection)); 
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);;
                                              }
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






