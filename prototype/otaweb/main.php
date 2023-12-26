<?php
session_start();
if (isset($_SESSION['username'])) {

} else {
    header('Location: index.php');
}
require_once('php/connectdb.php');
 mysqli_set_charset($connection,'utf8');
$sql = 'select role from Users where email="'. $_GET['username'].'"';
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
    <meta http-equiv="Content-Type"; content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTA FP3</title>
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
<h1>Vance and Hines OTA database</h1>


<div style='float: right;'>
<!-- <?php
    echo $username .'&nbsp';
    if ($userrole == "admin" || $userrole == "engineer")
    {
        echo '<a class="btn btn-default" href="newlang.php">Add new Entry</a>';
    }
?> -->
<a class="btn btn-default" href="logout.php">Logout</a>

</div>


<?php
//-----get selected option from the dropdown----

$language = trim($_GET['language']);

//var_dump($language);
if ($_GET['language'] != "") {

    echo "<H3> OTA Database:</H3>";

    if ( $userrole == "admin" || $userrole == "engineer" )
    {
        echo '<a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $language . '&username='. $username. '"><b>Add new Entry</b></a></p>';
    }   


    $result = mysqli_query($connection, "SELECT * FROM ota order by os,app ASC" );
    echo '<table class="table table-condensed">';
    echo '<tr>  <th></th>   
     <th>min_firmware</th>  <th>firmware</th>  <th>hardware</th> <th>app</th>  <th>os</th>  <th>filename</th> <th>vin</th> 
     <th>wb_min_firmware</th>  <th>wb_max_firmware</th>  <th>wb_hardware</th> <th>wb_filename</th>  <th>wb_vin</th> 
     </tr>';
    //----display data in table------
    //----loop through results of database query and display them in the table-----
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        //-----echo out the contents of each row into a table------
        echo "<tr >";

        echo '<td><a class="btn btn-default btn-xs" href="editlocalize.php?id=' . $row['id']
        . '&amp;min_firmware=' . $row['min_firmware'] 
        . '&amp;firmware=' . $row['firmware'] 
        . '&amp;hardware=' . $row['hardware'] 
        . '&amp;app=' . $row['app'] 
        . '&amp;os=' . $row['os'] 
        . '&amp;filename=' . $row['filename']         
        . '&amp;vin=' . $row['vin'] 
        . '&amp;wb_min_firmware=' . $row['wb_min_firmware'] 
        . '&amp;wb_max_firmware=' . $row['wb_max_firmware']
        . '&amp;wb_hardware=' . $row['wb_hardware']
        . '&amp;wb_filename=' . $row['wb_filename'] 
        . '&amp;wb_vin=' . $row['wb_vin']
        . '&amp;username=' . $username        
        . '"> Edit </a>  </td>';


        echo '<td>' . $row['min_firmware'] . '</td>';
        echo '<td>' . $row['firmware'] . '</td>';
        echo '<td>' . $row['hardware'] . '</td>';
        echo '<td>' . $row['app'] . '</td>';
        echo '<td>' . $row['os'] . '</td>';
        echo '<td>' . $row['filename'] . '</td>';        
        echo '<td>' . $row['vin'] . '</td>';


        echo '<td>' . $row['wb_min_firmware'] . '</td>';
        echo '<td>' . $row['wb_max_firmware'] . '</td>';
        echo '<td>' . $row['wb_hardware'] . '</td>';
        echo '<td>' . $row['wb_filename'] . '</td>';        
        echo '<td>' . $row['wb_vin'] . '</td>';


        echo "</tr>";
    }
    //-------close table-------
    echo "</table>";

}
?>

</form>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
</script>


</body>
</html>