<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Admin Menu</title>


  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>

      <link rel="stylesheet" href="css/style.css">
      <style>
.register-form{
    font-size: 16px;
    left: 50%;
    padding-top: 10%;
    position: relative;
    -webkit-transform: translate3d(-50%, -50%, 0);
    -moz-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
}

.regbutton{
    height: 50px;
    width: 200px;
    background-color:tomato;
    border-radius: 0px;
    font-size: 18px;
    color:white;
    border: none !important;
    margin-bottom: 5px;
}
.regbutton:hover{
    color: white;
    background-color:#aa422f;
}
.regbutton:active{
    color: white;
    background-color:#aa422f;
}

.register-form label{
    color: aliceblue;

}
.register-form input{
    margin-bottom: 5px;
    width: 430px;
    height: 40px;
    border-radius: 0px;
}

      </style>
</head>

<body>
  <?php
  include("includes/header.php");
   ?>

<!-- Content -->
<div class="main">
  <?php
  	require('db.php');
      // If form submitted, insert values into the database.
      if (isset($_REQUEST['username'])){
  		$username = stripslashes($_REQUEST['username']); // removes backslashes
  		$username = mysqli_real_escape_string($con,$username); //escapes special characters in a string
  		$email = stripslashes($_REQUEST['email']);
  		$email = mysqli_real_escape_string($con,$email);
  		$password = stripslashes($_REQUEST['password']);
  		$password = mysqli_real_escape_string($con,$password);

  		$trn_date = date("Y-m-d H:i:s");
          $query = "INSERT into `admin` (username, password, email, trn_date) VALUES ('$username', '".md5($password)."', '$email', '$trn_date')";
          $result = mysqli_query($con,$query);
          if($result){
              echo "<div class='form'><h3>You are registered successfully.</h3><br/>Click here to <a href='login.php'>Login</a></div>";
          }
      }else{
  ?>
  <h1>Registration</h1>
  <div class="container-fluid">

  <form name="registration" action="" method="post" class="register-form">
  <input type="text" name="username" placeholder="Username" required />
  <input type="email" name="email" placeholder="Email" required />
  <input type="password" name="password" placeholder="Password" required />
  <button class="btn btn-default regbutton" type="submit" name="submit" value="Register">Register</button>
  </form>
  <br /><br />
  </div>
  <?php } ?>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>

</body>
</html>
