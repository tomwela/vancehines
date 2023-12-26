<?php
/* This page will allow user to edit the record */

	function add($id,$imgurl, $headlines,$date,$description,$error)
	{
?>
		<!DOCTYPE HTML>
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
        <h3 align="center"> <font color="#000080"><marquee> Edit Selected Story </marquee> </font> </h3>
        </div>
 		 <div class="row">
            <div class= "col-md-4">
                <img id="logo" src="image-logo.png"/> 
                <form role="form" action = "" method="POST">

                <div class = "form-group">
               	<input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <p> <strong>ID:</strong><?php echo $_GET['id']; ?></p>
                
                </div>

                <div class = "form-group">
                <label for="imgurl">Image Url: </label>
                <textarea name ="imgurl" class="form-control" id="imgurl" name="imgurl"><?php echo $imgurl;?></textarea>
                </div>

                <div class = "form-group">
                <label for="headlines">Headline: </label>
                <textarea name ="headlines" class="form-control" id="headlines" name="headlines"><?php echo $headlines;?></textarea>
                </div>

                <div class = "form-group">
                <label for="date">Date: </label>
                <input type="datetime" id="datepicker" class="form-control"  name="date" value="<?php echo $date;?>"/>

                </div>

                <div class = "form-group">
                <label for="description">Web url: </label>
                <textarea name ="description" class="form-control" id="description" name="description"><?php echo $description;?></textarea>       
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
        require_once('php/connectdb.php');


        //-----check if the form has been submitted.-------

        if (isset($_POST['submit']))
        {    

            if (is_numeric($_POST['id']))
            {
                $id = $_POST['id'];
                $imgurl = mysqli_real_escape_string($connection, $_POST['imgurl']);

                $headlines = mysqli_real_escape_string($connection, $_POST['headlines']);
                $date = mysqli_real_escape_string($connection, $_POST['date']);
                $description = mysqli_real_escape_string($connection, $_POST['description']);
                
                


                $sImagePath = $imgurl;
                $iThumbnailWidth = 100;
                $iThumbnailHeight = 100;
                $iMaxWidth = 300;
                $iMaxHeight = 300;
                     
                $sType = 'scale';
                $img = NULL;
                $sExtension = strtolower(end(explode('.', $sImagePath)));

                    if ($sExtension == 'jpg' || $sExtension == 'jpeg')
                        {
                 
                            $img = @imagecreatefromjpeg($sImagePath)
                            or die("Cannot create new JPEG image");
                 
                        } 
                        else if ($sExtension == 'png') 

                        {
                 
                            $img = @imagecreatefrompng($sImagePath)
                            or die("Cannot create new PNG image");
                 
                        } 

                         else if ($sExtension == 'gif') 
                        {
                 
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
                        //$fname = $path_parts['filename']. "_thumb"."." . $path_parts['extension'];
                        $fname =  $path_parts['filename']. "." . $path_parts['extension'];

                       



                        header("Content-type: image/jpeg");
                        //imagejpeg($img, $fname);
                        imagejpeg($img,$fname);
                      //  imagejpeg($droidimage,$save);
                      
                     
                    }


                            
          

             $Url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
             $Url .= $_SERVER['SERVER_NAME'];
             $Url .= $_SERVER['REQUEST_URI'];
                                           
              $dbimage = dirname(dirname($Url))."/stories/".$fname;
              if ($imgurl == '' || $headlines == '' || $date == '' || $description =='')
               {
                  $error = 'ERROR: Please fill in all required fields!';
                   //error, display form
                    add($id,$imgurl, $headlines, $date, $description, $error);
                }
              else
              {
                      // save the data to the database
                      mysqli_query($connection, "UPDATE topstories SET thumbimgurl='$dbimage', headlines='$headlines', date='$date', description='$description', imgurl ='$imgurl' WHERE id='$id'")
                      or die(mysqli_error($connection)); 
                       // once saved, redirect back to the main page
                       header("Location: main.php"); 
               }
         
          }
      }
                    //    else
                    //  {
                    // // if the 'id' isn't valid, display an error
                    //     echo 'Error!';
                    //     echo 'id not found';
                    //  }
            
               
       // }



             
        else
            // if the form hasn't been submitted, get the data from the db and display the form
        {
 
            // get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
            if ((isset($_GET['id'])>0))
            {
                // query db
                $id = $_GET['id'];      
                $result = mysqli_query($connection, "SELECT * FROM topstories WHERE id=$id")
                or die(mysqli_error($connection)); 
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
                // check that the 'id' matches up with a row in the databse
                if($row)
                {
                    // get data from db
                    $imgurl = $row['imgurl'];
                    $headlines = $row['headlines'];
                    $date = $row['date'];
                    $description = $row['description'];
                     
                    // show form
                    add($id,$imgurl, $headlines, $date, $description,'');
                

                }
                else
                    // if no match, display result
                {
                    echo "No results!";
                }
            }
            else
                // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
            {
                echo 'Error!';
                echo 'id not valid';
            }
        }            
?>




