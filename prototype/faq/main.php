<?php
session_start();
if (isset($_SESSION['username'])) {

} else {
    header('Location: index.php');
}


require_once('php/connectdb.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FP3 FAQ's</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        body {
            background-color: burlywood;
            color: white;
            margin: 50px;
        }
    </style>
</head>
<body>
<!--<img id="logo" src="img/image-logo.png" width="200" height="80"/>-->
<img id="logo" class="pull-right" src="img/image-logo.png" />

<h1>Vance and Hines FAQ database</h1>
<div id="div1"></div>

<button class="btn btn-default btn-sm" id="language">Add new Language</button>

<div style='float: right;'><a class="btn btn-default" href="logout.php">Logout</a></div>
<?php

//$sql = "SHOW TABLES FROM $db";
$sql = "SHOW TABLES ";
$result = mysqli_query($connection, $sql);
$tableNames = array();
//var_dump($tableNames);

while ($row = mysqli_fetch_row($result)) {
    if (substr($row[0], 0, 3) == "faq") {
        $tableNames[] = substr($row[0], 3);
    }
}
echo '<form action="" method="GET">';
echo '<select name="language" id="language" onchange="this.form.submit();">';
echo '<option value="">Select the Language</option>';
foreach ($tableNames as $name) {
    echo '<option  value="' . $name . ' ">' . $name . ' </option>';
}

echo '</select>';

//-----get selected option from the dropdown----

$language = $_GET['language'];
if ($_GET['language'] != "") {

    echo "<p> </p> FAQ Database: " . $language;
    $result = mysqli_query($connection, "SELECT * FROM faq" . $language . " ORDER BY Priority ASC");
    echo "<table class='table table-condensed'>";
    echo "<tr> <th>ID</th> <th>Question</th> <th>Answer</th> <th>OS</th> <th>Priority</th> <th></th> <th></th> </tr>";
    //----display data in table------
    //----loop through results of database query and display them in the table-----
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        //-----echo out the contents of each row into a table------
        echo "<tr>";
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['Question'] . '</td>';
        echo '<td>' . $row['Answer'] . '</td>';
        echo '<td>' . $row['OS'] . '</td>';
        echo '<td>' . $row['Priority'] . '</td>';
        echo '<td><a class="btn btn-default btn-xs" href="editfaq.php?language=' . $language . '&amp;id=' . $row['id'] . '"> Edit </a>  </td>';
        echo '<td><a class="btn btn-default btn-xs" href="deletefaq.php?language=' . $language . '&amp;id=' . $row['id'] . '"> Delete </a> </td>';

        echo "</tr>";
    }
    //-------close table-------
    echo "</table>";
    echo '<a class="btn btn-default btn-lg" href="addfaq.php?language=' . $_GET['language'] . '"><b>Add new FAQ</b></a></p>';
}
?>

</form>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#language').click(function () {
            $.ajax({
                url: "newlang.php", success: function (result) {
                    $("#div1").html(result);
                }
            });
        });
    });

</script>
</body>
</html>