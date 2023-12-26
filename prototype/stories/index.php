<?php
    session_start();
   //$_SESSION['username'] = 'Root';

    function compareHashes($password, $dbHash) {
        if( crypt($password, $dbHash) == $dbHash ) {
          return true;
        } else {
          return false;
        }
    }
    
    
    if(isset($_POST['login']))
    {
        require_once('php/connectdb.php');

        $username=$_POST['username']; 
        $password=$_POST['password']; 
        $username = $username;
        $password = $password;


        $result = mysqli_query($connection, "SELECT * FROM Users WHERE email='". $username ."'");
        $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);        
        
        if ( compareHashes($password, $row['password']) ) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            //$_SESSION['userobj'] = mysql_fetch_assoc($query);

            header('Location: main.php?username='.$_SESSION['username'].'');
            exit;
        } else 
        
        {
        header('Location: index.php');
        }
    } 
    else {

?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
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
<body>
<div class="container">
    <div class="row">
        <h3 align="center"><font color="#000080"><marquee>Vance and Hines Top Stories </marquee> </font></h3>
    </div>
    <div class="row">
          <div class="col-md-4">
                <img id="logo" src="images/image-logo.png"/> 


              
              <form role="form" action = "<?PHP echo $_SERVER['PHP_SELF']; ?>" method="POST">

                <div class="form-group">
                  <label for="email">Email:</label>
                  <input type="email" class="form-control" id="username" name="username" placeholder="Enter Email">
                  </div>
                
                <div class="form-group">
                  <label for="pwd">Password:</label>        
                  <input type="password" class="form-control" id="password" name= "password" placeholder="password">
                  </div>
                
                <!-- <input type="submit" name="login" id="login" value="Login" /> -->
                <button type="submit" name="login" id= "login" class="btn btn-default">Login</button>
              </form>

          </div>
        <div class="col-md-8"></div>
    </div><!-- row -->
</div><!-- container -->

<!--      // <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
     // <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
  </body>
</html>
<?php
   
 }


?>