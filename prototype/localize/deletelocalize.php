<?php
/* 
 Deletes a FAQ in the given table
*/

 // connect to the database
 require_once('php/connectdb.php');
 
 // check if the 'id' variable is set in URL, and check that it is valid
 if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['language']))
 {
     // get id value
     $id = $_GET['id'];
     $language= $_GET['language'];
     // delete the entry
     $result = mysqli_query($connection, "DELETE FROM localize".$language." WHERE id=$id")
     or die(mysqli_error($connection)); 
     
     // redirect back to the main page
     header("Location: main.php?language=$language");
 }
 else
 // if id isn't set, or isn't valid, redirect back to view page
 {
    header("Location: main.php?language=$language");
 }
 
?>