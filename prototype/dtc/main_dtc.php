<?php
session_start();
if (isset($_SESSION['username'])) {

} else {
    header('Location: index_dtc.php');
}
require_once('php/connectdb.php');

$sql = 'SELECT role from Users where email="'. $_GET['username'].'"';
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_array($result, MYSQLI_BOTH);
#echo $sql;
$username = $_GET['username'];
$userrole = $row['role'];
//var_dump($username);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FP3 DTC</title>
    <!-- <title>FP3 Localize's</title> -->
    
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

<h1>Vance and Hines DTC database</h1>
<!-- <h1>Vance and Hines Localize database</h1> -->


<div style='float: right;'>
<?php
    echo $username .'&nbsp';
    if ($userrole == "admin" || $userrole == "engineer")
    {
        echo ' ['.$userrole.'] ';
    }
?>
<a class="btn btn-default" href="logout_dtc.php">Logout</a>


</div>



<?php

//var_dump($language);
if (1) //($_GET['language'] != "") 
{

    echo "<H3> DTC Database: </H3>";

    $language = "English";

    if ( $language == "English")
    {
        //echo '<a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $_GET['language'] . '"><b>Add new string</b></a></p>';
        if ( $userrole == "admin" || $userrole == "engineer" )
        {
            echo '<a class="btn btn-default btn-lg" href = " add_dtc.php?username=' . $username . ' " ><b>Add new DTC Code</b></a></p>';
            // echo '<a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $language . '"><b>Add new string</b></a></p>';
        }   
    }

    if ( $userrole == "admin" || $userrole == "engineer")
    {

        // ------------- NOT NEEDED FOR DTC -------------------

    // echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=Android"> Generate localize Android </a>&nbsp;&nbsp;';
    // echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=iPhone"> Generate localize Iphone </a>&nbsp;&nbsp;';
    // echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=None"> Generate CSV </a>';
    // echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=Delimited"> Delimited </a>';

    }

    $result = mysqli_query($connection, "SELECT * FROM dtc"); 

    echo '<table class="table table-condensed">';
    echo '<tr> <th>edit</th> <th>code</th>  <th>short</th>  <th>description</th>  </tr>';
    // echo '<tr>  <th></th>  <th></th>  <th>uniqueKey</th>  <th>Description</th>  <th>androidKey</th> <th>iphoneKey</th>  <th>ID</th>  </tr>';

    //----display data in table------
    //----loop through results of database query and display them in the table-----
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        //-----echo out the contents of each row into a table------
        echo "<tr>";

        echo '<td><a class="btn btn-default btn-xs" href="edit_dtc.php?code=' . $row['code'] .'&amp;username=' . $username .'  "> Edit </a>  </td>';
        // echo '<td><a class="btn btn-default btn-xs" href="editlocalize.php?language=' . $language . '&amp;id=' . $row['id'] .  '&amp;username=' . $username . '"> Edit </a>  </td>';


        // if ( $row['sourceMod'] == "False" )
        // {
        //     echo '<td>' . '</td>';
        // }
        // else
        // {
        //     echo '<td>' . $row['sourceMod'] . '</td>';
        // }

        echo '<td>' . $row['code'] . '</td>';
        echo '<td>' . $row['short'] . '</td>';
        echo '<td>' . $row['description'] . '</td>';
        //echo '<td>' . $row['id'] . '</td>';

        // echo '<td>' . $row['uniqueKey'] . '</td>';
        // echo '<td>' . $row['description'] . '</td>';
        // echo '<td>' . $row['androidKey'] . '</td>';
        // echo '<td>' . $row['iphoneKey'] . '</td>';
        // echo '<td>' . $row['id'] . '</td>';


        echo "</tr>";
    }
    //-------close table-------
    echo "</table>";

}
?>

</form>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
    // $(document).ready(function () {
    //     $('#language').click(function () {
    //         $.ajax({
    //             url: "main.php", success: function (result) {
    //                 $("#div1").html(result);
    //             }
    //         });
    //     });
    // });

</script>


</body>
</html>