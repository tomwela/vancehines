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
    <title>FP3 Localize's</title>
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
<h1>Vance and Hines Localize database</h1>


<div style='float: right;'>
<?php
    echo $username .'&nbsp';
    if ($userrole == "admin" || $userrole == "engineer")
    {
        echo '<a class="btn btn-default" href="newlang.php">Add new Language</a>';
    }
?>
<a class="btn btn-default" href="logout.php">Logout</a>


</div>


<?php

$sql = "SHOW TABLES ";
$result = mysqli_query($connection, $sql);
$tableNames = array();

while ($row = mysqli_fetch_row($result)) {
    if (substr($row[0], 0, 8) == "localize") {
        $tableNames[] = substr($row[0], 8) ;
    }
}
// echo '<form action="" method="GET">';
// echo '<select name="language" id="language" onchange="this.form.submit();">';
// echo '<option value="">Select the Language</option>';
// foreach ($tableNames as $langname) 
// {
// //    echo '<option  value="' . $name .' ">' . $name . ' </option>';
    
//     $phpname =  $langname;// . '&username=' . $username;

//     echo '<option  value="' . $phpname .'">' . $langname . ' </option>';

// //.  "&amp;id=" . $row['id'] .  "&amp;username=" . $username 

// }
// echo '</select>';

foreach ($tableNames as $langname) 
{

    echo '<a class="btn btn-default btn-xs" href="main.php?language=' . $langname . '&amp;username=' . $username . '">' . $langname . '</a> ';
}

?>


<?php
//-----get selected option from the dropdown----

$language = trim($_GET['language']);
//var_dump($language);
if ($_GET['language'] != "") {

    echo "<H3> Localize Database: " . $language . "</H3>";

    if ( $language == "English")
    {
        //echo '<a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $_GET['language'] . '"><b>Add new string</b></a></p>';
        if ( $userrole == "admin" || $userrole == "engineer" )
        {
            echo '<a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $language . '"><b>Add new string</b></a></p>';
        }   
    }

    if ( $userrole == "admin" || $userrole == "engineer")
    {
    echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=Android"> Generate localize Android </a>&nbsp;&nbsp;';
    echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=iPhone"> Generate localize Iphone </a>&nbsp;&nbsp;';
    echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=None"> Generate CSV </a>';

    echo '<a class="btn btn-default btn-lg" href="genlocalize.php?language=' . $language . '&amp;os=Delimited"> Delimited </a>';


    }


    $result = mysqli_query($connection, "SELECT * FROM localize" . $language );
    echo '<table class="table table-condensed">';
    echo '<tr>  <th></th>  <th></th>  <th>uniqueKey</th>  <th>Description</th>  <th>androidKey</th> <th>iphoneKey</th>  <th>ID</th>  </tr>';
    //----display data in table------
    //----loop through results of database query and display them in the table-----
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        //-----echo out the contents of each row into a table------
        echo "<tr >";

        echo '<td><a class="btn btn-default btn-xs" href="editlocalize.php?language=' . $language . '&amp;id=' . $row['id'] .  '&amp;username=' . $username . '"> Edit </a>  </td>';

        if ( $row['sourceMod'] == "False" )
        {
            echo '<td>' . '</td>';
        }
        else
        {
            echo '<td>' . $row['sourceMod'] . '</td>';
        }

        echo '<td>' . $row['uniqueKey'] . '</td>';
        echo '<td>' . $row['description'] . '</td>';

        echo '<td>' . $row['androidKey'] . '</td>';
        echo '<td>' . $row['iphoneKey'] . '</td>';
        echo '<td>' . $row['id'] . '</td>';

        //echo '<td><a class="btn btn-default btn-xs" href="deletelocalize.php?language=' . $language . '&amp;id=' . $row['id'] . '"> Delete </a> </td>';

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