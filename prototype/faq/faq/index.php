<?php
    session_start();
   //$_SESSION['username'] = 'Root';

    if(isset($_POST['login']))
     {
        require_once('php/connectdb.php');

       

        $username=$_POST['username']; 
        $password=$_POST['password']; 
        $username = $username;
        $password = $password;


        $query = mysqli_query($connection, "SELECT * FROM Users WHERE email='". $username ."' AND password='". $password ."'");
        $res = mysqli_num_rows($query);

        if ($res == 1) {
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
        <meta charset="utf-8">
  
    <h1 align="center"><font color="yellow">Vance and Hines FAQ Database </font></h1>
<img src="img/image-logo.png" width="200" height="80"/>	
<h1 align="center">Please Provide your Login Credentials</h1>
<body bgcolor="black" style="color:white">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"></head>
        <body>
                <div align="center">


    <form action="<?PHP echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <p><label>User Name : </label>
    <input id="username" type="text" name="username" placeholder="username" /></p>
    <p><label>Password&nbsp;&nbsp; : </label>
    <input id="password" type="password" name="password" placeholder="password" /></p>
   
    <input type="submit" name="login" id="login" value="Login" />
    
</form>
</div>
            </table>
            </body>
    </html>
    <?php
    }


?>