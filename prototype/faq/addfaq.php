<?php
/* Allows user to add new FAQ to the database */
function add($Question, $Answer,$error)
{
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <body bgcolor="black" style="color:white">
    <img src="img/image-logo.png" width="200" height="80"/>	
    <h1> Add New FAQ to the database </h1>
    </head>
    <body>
 
    <div align="center">
    <form method="post" action="">
    <table style='margin: 1em 0;background: #eee;font-size: 20px;border-collapse: collapse;text-align: center;width: 50%; color:black;'>
    <tr> <td> <input type="hidden" name="language" value="<?php echo $_GET['language']; ?>"/></td></tr>
    <tr><td><p><strong><font color="black">Language:</font></strong><?php echo $_GET['language'];?></p></td></tr>
    <tr><td width="2%" height="10"><font color="black">Question:</font><font color="red">*</font></td><td width="2%" height="10"><textarea name= "Question" style= "font-size: 12pt" rows="5" cols="30"/></textarea></td></tr><br/>
    <tr><td width="2%" height="10"><font color="black">Answer:</font><font color="red">*</font></td><td width="2%" height="10"><textarea name= "Answer"rows="5" style= "font-size: 12pt" cols="30"/></textarea></td></tr><br/>

     

    <tr><td width="2%" height="10"><font color="black">OS:</font><font color="red">*</font></td><td width="2%" height="10">
    <select name="OS" id="OS" style="width: 320px; font-size: 50pt;">
    <option value="iOS">----------Please select the OS----------
    <option value="iOS">iOS
    <option value="Android">Android
    <option value="Both">Both
    </select>

    <tr><td width="2%" height="10"><font color="black">Priority:</font><font color="red">*</font></td><td width="2%" height="10"><textarea name= "Priority"rows="1" style= "font-size: 12pt" cols="30"/></textarea></td></tr><br/>
    <p><font size = 6pt>All <font color="red">*</font> fields are required</font></p>
    <tr><td width="100%" colspan="2" height="26"><P align="center"><input type="submit" name="submit" value="Submit"></P></td></tr>
    </table>
    </form>
    </div>
    </body>
    </html>
 
    <?php 
 }
    //---connect to databse------
    require_once('php/connectdb.php');
    //mysql_query("SET NAMES 'utf8'");
    //----To check the data is submitted----
    if (isset($_POST['submit']))
        {
    
            $Question = mysqli_real_escape_string($connection, $_POST['Question']);
            $Answer = mysqli_real_escape_string($connection, $_POST['Answer']);
            $OS = mysqli_real_escape_string($connection, $_POST['OS']);
            $Priority = mysqli_real_escape_string($connection, $_POST['Priority']);
            if ($Question == '' || $Answer == '')
                    {
                        //---generate error message----
                        $error = 'ERROR: Please fill in all required fields!';
                        //---if either field is blank, display the form again---
                        add($Question, $Answer, $error);
                    }
            else
                {
                    if(isset($_GET['language']))
                    $language= $_GET['language'];
                    mysqli_query($connection,"INSERT INTO faq".$language." SET id='', Question='$Question', Answer='$Answer', OS='$OS', Priority='$Priority'")
                    or die(mysqli_error($connection)); 
                    header("Location: main.php?language=$language"); 
                }
        }
        else
            //------if the form hasn't been submitted, display the form------
            {
                add('','','');
            }
                   
?>
