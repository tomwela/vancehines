<?php
// if text data was posted
if($_POST){
    print_r($_POST);
}

// if a file was posted
else if($_FILES){
    $file = $_FILES['file'];
    $fileContents = file_get_contents($file["tmp_name"]);
    $time = time();
    print_r($time);
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/prototype/reader/'.$time.'.txt', $fileContents);
 
}
?>