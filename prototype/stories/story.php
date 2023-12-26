<?php
/* Allows user to add new FAQ to the database */
function add($imgurl, $headlines,$date,$description)
{
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <meta name="viewport" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
     <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">

    <style>
    body {
        background-color: #FFE4C4;
        color:#000080;
        font-size: 20px;
        font-family: Georgia;
    }
    #logo{
        margin-bottom:20px;
    }
    </style>

    <script>
    $(function() {
        $( "#datepicker" ).datepicker();
    });
    </script>
    </head>
    <body>
 
    <div class="container">
    <div class="row">
    <h3 align="center"> <font color="#000080"><marquee> Add New Story </marquee> </font> </h3>
    </div>
        
    <div class="row">
    <div class= "col-md-4">
    <img id="logo" src="images/image-logo.png"/> 


    <form role="form" action = "<?PHP echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class = "form-group">
    <label for="imgurl">Image Url: </label>
    <textarea name ="imgurl" class="form-control" id="imgurl" name="imgurl" placeholder="Enter Image URL"></textarea>
    </div>
    <div class = "form-group">
    <label for="headlines">Headline: </label>
    <textarea name ="headlines" class="form-control" id="headlines" name="headlines" placeholder="Enter Headlines"></textarea>
    </div>
    <div class = "form-group">
    <label for="date">Date: </label>
    <input type="datetime" id="datepicker" class="form-control" id="date" name="date" >
               
    </div>
    <div class = "form-group">
    <label for="Description">Web url: </label>
    <textarea name ="description" class="form-control" id="description" name="description" placeholder="Enter Url"></textarea>                   
    </div>
    <button type="submit" name="submit" id= "submit" class="btn btn-default">Submit</button>
    </form>
    </div>
    <div class="col-md-8"></div>
    </div><!-- row -->
    </div><!-- container -->
    </body>
    </html> 
 
    <?php 
    }
    //---connect to databse------
    require_once('php/connectdb.php');
    //----To check the data is submitted----
    if (isset($_POST['submit']))
        {

            $imgurl      = mysqli_real_escape_string($connection, $_POST['imgurl']);
            $headlines   = mysqli_real_escape_string($connection, $_POST['headlines']);
            $date        = mysqli_real_escape_string($connection, $_POST['date']);
            $description = mysqli_real_escape_string($connection, $_POST['description']);       
            $sImagePath  = $imgurl;
 
            $iThumbnailWidth = 100;
            $iThumbnailHeight = 100;
            $iMaxWidth = 300;
            $iMaxHeight = 300;
             
            $sType = 'scale';
            $img = NULL;
 
        $sExtension = strtolower(end(explode('.', $sImagePath)));
        if ($sExtension == 'jpg' || $sExtension == 'jpeg') {
 
            $img = @imagecreatefromjpeg($sImagePath)
                or die("Cannot create new JPEG image");
 
        } else if ($sExtension == 'png') {
 
            $img = @imagecreatefrompng($sImagePath)
                or die("Cannot create new PNG image");
 
        } else if ($sExtension == 'gif') {
 
            $img = @imagecreatefromgif($sImagePath)
                or die("Cannot create new GIF image");
 
        }
         
        if ($img) {
         
            $iOrigWidth = imagesx($img);
            $iOrigHeight = imagesy($img);
            $iorigratio = $iOrigWidth/$iOrigHeight;
         
            if ($sType == 'scale') 
                //{


                {
                    $iNewWidth =  $iMaxWidth;
                    $iNewHeight =  $iNewWidth/ $iorigratio;
                    $tmpimg = imagecreatetruecolor($iNewWidth,
                                       $iNewHeight);
         
                    imagecopyresampled($tmpimg, $img, 0, 0, 0, 0,
                    $iNewWidth, $iNewHeight, $iOrigWidth, $iOrigHeight);
         
                    imagedestroy($img);
                    $img = $tmpimg;
                }     

    //}
    else if ($sType == "exact") {
 
        $fScale = max($iThumbnailWidth/$iOrigWidth,
        $iThumbnailHeight/$iOrigHeight);
 
        if ($fScale < 1) 
        {
 
            $iNewWidth = floor($fScale*$iOrigWidth);
            $iNewHeight = floor($fScale*$iOrigHeight);
 
            $tmpimg = imagecreatetruecolor($iNewWidth,
                            $iNewHeight);
            $tmp2img = imagecreatetruecolor($iThumbnailWidth,
                            $iThumbnailHeight);
 
            imagecopyresampled($tmpimg, $img, 0, 0, 0, 0,
            $iNewWidth, $iNewHeight, $iOrigWidth, $iOrigHeight);
 
            if ($iNewWidth == $iThumbnailWidth)
            {
 
                $yAxis = ($iNewHeight/2)-
                    ($iThumbnailHeight/2);
                $xAxis = 0;
 
            } 
            else if ($iNewHeight == $iThumbnailHeight)  {
 
                $yAxis = 0;
                $xAxis = ($iNewWidth/2)-
                    ($iThumbnailWidth/2);
 
            } 
 
            imagecopyresampled($tmp2img, $tmpimg, 0, 0,
                       $xAxis, $yAxis,
                       $iThumbnailWidth,
                       $iThumbnailHeight,
                       $iThumbnailWidth,
                       $iThumbnailHeight);
 
            imagedestroy($img);
            imagedestroy($tmpimg);
            $img = $tmp2img;
        }    
 
    }

    

    $path_parts = pathinfo($imgurl);
    $fname = $path_parts['filename']. "." . $path_parts['extension'];
    //$fname = $path_parts['filename']. "_thumb"."." . $path_parts['extension'];
    

    header("Content-type: image/jpeg");
    imagejpeg($img,$fname);
    
 
}
    $Url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $Url .= $_SERVER['SERVER_NAME'];
    $Url .= $_SERVER['REQUEST_URI'];
    //$Url .= $_SERVER['REQUEST_URI'];

    $dbimage = dirname(dirname($Url))."/stories/".$fname;


            if ($imgurl == '' || $headlines == '' || $date == '' || $description =='')
                    {
                        //---generate error message----
                        $error = 'ERROR: Please fill in all required fields!';
                        //---if either field is blank, display the form again---
                        add($imgurl, $headlines, $date, $description);
                    }
            else
                {
                  
                    mysqli_query($connection,"INSERT INTO topstories SET id='', thumbimgurl='$dbimage', headlines='$headlines', date='$date', description='$description', imgurl ='$imgurl'")
                    or die(mysqli_error($connection)); 
                    header("Location: main.php"); 
                }
        }
        else
            //------if the form hasn't been submitted, display the form------
            {
                add('','','','');
            }
                  
?>
