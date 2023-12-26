<?php
require_once('php/connectdb.php');
//if (isset($_POST['table']))
//assign the value in the variable

$language = $_GET['language'];
$table = "localize".$language;

$osget = $_GET['os'];


$q = "SELECT * FROM " . $table;
//echo $q;
$result = mysqli_query($connection, $q) or  die(mysql_error());

$iosflag = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) 
{
    if ($osget =="Android" )
    {
        #var_dump($row);

        $uniqueKey = $row['uniqueKey'];
        $androidKey = $row['androidKey'];
        $iphoneKey = $row['iphoneKey'];
        $description = $row['description'];
        $os = $row['os'];
        $modPosition = $row['modPosition'];
        $modString = $row['modString'];

        //echo $q;
        //echo $uniqueKey;
        //echo $androidKey;
        //echo $description;    

        $androidKeyArray = explode ( ",", $androidKey);


        if ( $os=="Both" || $os=="Android" )
        {
            foreach ( $androidKeyArray as $key)
            {

                $keytrimmed = trim($key, " ");

                echo '&lt;string name="' . $keytrimmed . '"&gt;' . $description .'&lt;/string&gt;';
                echo "<BR>";
            }
        }



        //echo "<BR>";
    }

    if ($osget =="iPhone" )
    {

        $uniqueKey = $row['uniqueKey'];
        $androidKey = $row['androidKey'];
        $iphoneKey = $row['iphoneKey'];
        $description = $row['description'];
        $os = $row['os'];
        $modPosition = $row['modPosition'];
        $modString = $row['modString'];

        //echo $q;
        //echo $uniqueKey;
        //echo $androidKey;
        //echo $description;    

        $iphonekeyArray = explode ( ",", $iphoneKey);
        //var_dump($iphonekeyArray);



        if ( $os=="Both" || $os=="iOS" )
        {


            if ( $iosflag == 0 )
            {
                echo '/* Localizable.strings ('. $language .')';
                echo "<BR>";


                echo 'Copyright (c) 2012 Vance & Hines. All right reserved.';
                echo "<BR>";


                echo $today = date("Y-m-d H:i:s");
                echo "<BR>";

                echo "*/<BR><BR>";
                $iosflag = 1;
            }

            foreach ( $iphonekeyArray as $key)
            {

                $keytrimmed = trim($key, " ");
                $newstring = str_replace("\u","\U", $description);
                $description = $newstring;

                echo '"' . $keytrimmed . '"="' . $description .'";';
                echo "<BR>";

            }
        }

    }

    if ($osget =="None" )
    {

        $uniqueKey = $row['uniqueKey'];
        $androidKey = $row['androidKey'];
        $iphoneKey = $row['iphoneKey'];
        $description = $row['description'];
        $os = $row['os'];
        $modPosition = $row['modPosition'];
        $modString = $row['modString'];


        if ( $language == "English" )
        {

            $newstring = str_replace("\u000D","⏎", $description);
            $description = $newstring;

            $newstring = str_replace("\u0026","&", $description);
            $description = $newstring;

            $newstring = str_replace("\u0022","\"", $description);
            $description = $newstring;

            $newstring = str_replace("\u2026","…", $description);
            $description = $newstring;

            $newstring = str_replace("\u0027","'", $description);
            $description = $newstring;


            $newstring = str_replace("\u00B0","°", $description);
            $description = $newstring;

            $newstring = str_replace("\u00B7","·", $description);
            $description = $newstring;                            

        }

        echo '<pre>'. $uniqueKey . '&#9;' . $description . '</pre>';
        //echo "<BR>";
    }


    if ($osget =="Delimited" )
    {

        $uniqueKey = $row['uniqueKey'];
        $androidKey = $row['androidKey'];
        $iphoneKey = $row['iphoneKey'];
        $description = $row['description'];
        $os = $row['os'];
        $modPosition = $row['modPosition'];
        $modString = $row['modString'];



        echo 
        '<pre>'.  '&#9;' .
        $uniqueKey . '&#9;' .
        $androidKey . '&#9;' .
        $iphoneKey . '&#9;' .
        $description . '&#9;' .
        $os . '&#9;' .
        $modPosition . '&#9;' .
        $modString . '&#9;' .  'False' .
        '</pre>';

    }




}

//header("Location:main.php?language=$language"); 

?>
