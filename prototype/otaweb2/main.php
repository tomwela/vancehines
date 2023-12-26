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



  <?php require_once('header.php'); ?>



<div style='float: right;'>
<!-- <?php
    echo $username .'&nbsp';
    if ($userrole == "admin" || $userrole == "engineer")
    {
        echo '<a class="btn btn-default" href="newlang.php">Add new Entry</a>';
    }
?> -->


</div>


<?php
//-----get selected option from the dropdown----

$language = trim($_GET['language']);

//var_dump($language);
if ($_GET['language'] != "") {

    echo "<ul class='otaDb'><li><H3> OTA Database</H3></li>";

    if ( $userrole == "admin" || $userrole == "engineer" )
    {
        echo '<li class="newEntry"><a class="btn btn-default btn-lg" href="addlocalize.php?language=' . $language . '&username='. $username. '"><b>Add new Entry</b></a></li>';
    }   

  echo "</ul>";
    $result = mysqli_query($connection, "SELECT * FROM ota order by os,app ASC" );
    echo '<table class="table table-striped dbTable" >';
    echo '<th class="emptyTh"></th>   
     <th>min_firmware</th>  <th>firmware</th>  <th>hardware</th> <th>app</th>  <th>os</th>  <th>filename</th> <th>vin</th> 
     <th>wb_min_firmware</th>  <th>wb_max_firmware</th>  <th>wb_hardware</th> <th>wb_filename</th>  <th>wb_vin</th>
     <th>cks</th><th>btl</th><th>aut</th>
     ';
    //----display data in table------
    //----loop through results of database query and display them in the table-----
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {

        //-----echo out the contents of each row into a table------
        echo '<tr  hoverdID="' . $row['id'] . '">';

        echo '<td><a id="' . $row['id'] . '_id" class="editBtn" href="editlocalize.php?id=' . $row['id']
        // . '&amp;min_firmware=' . $row['min_firmware'] 
        // . '&amp;firmware=' . $row['firmware'] 
        // . '&amp;hardware=' . $row['hardware'] 
        // . '&amp;app=' . $row['app'] 
        // . '&amp;os=' . $row['os'] 
        // . '&amp;filename=' . $row['filename']         
        // . '&amp;vin=' . $row['vin'] 
        // . '&amp;wb_min_firmware=' . $row['wb_min_firmware'] 
        // . '&amp;wb_max_firmware=' . $row['wb_max_firmware']
        // . '&amp;wb_hardware=' . $row['wb_hardware']
        // . '&amp;wb_filename=' . $row['wb_filename'] 
        // . '&amp;wb_vin=' . $row['wb_vin']
        . '&amp;username=' . $username        
        . '"> <img src="img/edit.png" alt="Edit" title="Edit"/> </a>  <a id="' . $row['id'] . '_id2" class="editBtn" href="addlocalize.php?id='.$row['id'].'"><img src="img/dup.png" class="imgdup" alt="Duplicate" title="Duplicate" /></a> </td>';

        
       // echo '<td class="editBtn"><img src="img/edit1.png"/><img src="img/dup.png" class="imgdup" /></td>';

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

        echo '<td>' . $row['chksum'] . '</td>';
        echo '<td>' . $row['btloader'] . '</td>';
        echo '<td>' . $row['autotune'] . '</td>';


        echo "</tr>";
    }
    //-------close table-------
    echo "</table>";

}
?>

</form>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>
</html>