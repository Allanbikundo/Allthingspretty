<?php
// include database configuration file
include 'dbConfig.php';

// initializ shopping cart class
include 'Cart.php';
$cart = new Cart;

//Include GP config file && User class
include_once 'gpConfig.php';
include_once 'User.php';

if(isset($_GET['code'])){
  $gClient->authenticate($_GET['code']);
  $_SESSION['token'] = $gClient->getAccessToken();
  header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
  $gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
  //Get user profile data from google
  $gpUserProfile = $google_oauthV2->userinfo->get();

  //Initialize User class
  $user = new User();

  //Insert or update user data to the database
    $gpUserData = array(
        'oauth_provider'=> 'google',
        'oauth_uid'     => $gpUserProfile['id'],
        'first_name'    => $gpUserProfile['given_name'],
        'last_name'     => $gpUserProfile['family_name'],
        'email'         => $gpUserProfile['email'],
        'gender'        => $gpUserProfile['gender'],
        'locale'        => $gpUserProfile['locale'],
        'picture'       => $gpUserProfile['picture'],
        'link'          => $gpUserProfile['link']
    );
    $userData = $user->checkUser($gpUserData);

  //Render facebook profile data
    if(!empty($userData)){
        $output = '<h3>Profile Details </h3>';
        $output .= '<img src="'.$userData['picture'].'" width="300" height="220">';
        $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
        $output .= '<br/>Email : ' . $userData['email'];
        $output .= '<br/>Gender : ' . $userData['gender'];
        $output .= '<br/><a href="logout.php">Sign Out</a>';
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        //$output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin.png" alt=""/></a>';
    }
} else {
  $authUrl = $gClient->createAuthUrl();
  $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin.png" alt="" width=400px; height=70px;/></a>';
}

// redirect to home if cart is empty
if($cart->total_items() <= 0){
    header("Location: index.php");
}


// get customer details by session customer ID
$query = $db->query("SELECT * FROM users WHERE id = ".$_SESSION['username']);
$custRow = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout -ALL THINGS PRETTY</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="CSS/style.css" rel="stylesheet">
        <link href="bootstrap-social.css" rel="stylesheet">
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <link href="assets/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/docs.css" rel="stylesheet" >
    <style>
    .container{width: 100%;padding: 50px;}
    .table{width: 100%;}
    .shipAddr{
        width: 30%;
        text-align: center;
        margin-left: auto;
        margin-right: auto;

    }
    .footBtn{width: 95%;float: left;}
    .orderBtn {float: right;}
    .footBtn .btn-warning{
         background-color:#ffe11b;
         border-color: #000;
         color: #000;
    }
    .footBtn .btn-success {
        background-color: #8132ff;
         border-color: #000;
    }
    </style>
</head>
<body>

<!--Navigation Bar-->
    <nav class="navbar navbar-default navbar-fixed-top" id="navbartop">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" id="brand" href="index.php">AllThingsPretty</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <form action="search.php"  method="post" class="navbar-form navbar-left" id="searchbar">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search for Products, Brands and more">
            <div class="input-group-btn">
              <button type="submit" class="btn btn-default" id="searchbtn">
                <span class="glyphicon glyphicon-search" id="glyphicon-search"></span>
              </button>
            </div>
          </div>
        </form>
          <ul class="nav navbar-nav navbar-right" id="glyphicon-text">
                <a href="viewCart.php" title="View Cart"><i class="glyphicon glyphicon-shopping-cart" id="glyphicon-cart"></i>Cart</a>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Profile <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                    <p>Welcome to AllThingsPretty</p>
                     <li><a href="#"><?php echo $output; ?></a></li>
                    </ul>
                </li>
               
          </ul>
        </div>
      </div>
    </nav>
<div class="container">
    <h1 style="text-align: center; margin-bottom: 20px;">Order Preview</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($cart->total_items() > 0){
            //get cart items from session
            $cartItems = $cart->contents();
            foreach($cartItems as $item){
        ?>
        <tr>
            <td><?php echo $item["name"]; ?></td>
            <td><?php echo $item["price"].' KSH'; ?></td>
            <td><?php echo $item["qty"]; ?></td>
            <td><?php echo $item["subtotal"].' KSH'; ?></td>
        </tr>
        <?php } }else{ ?>
        <tr><td colspan="4"><p>No items in your cart......</p></td>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <?php if($cart->total_items() > 0){ ?>
            <td class="text-center"><strong>Total <?php echo $cart->total().' KSH'; ?></strong></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    
    <div class="footBtn">
        <a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a>
        <a href="cartAction.php?action=placeOrder" class="btn btn-success orderBtn">Place Order <i class="glyphicon glyphicon-menu-right"></i></a>
    </div>
    <div class="shipAddr">
        <h2 style="font-weight: bold; font-style: italic;">Shipping Details</h2>
        <p>Firstname:  <?php echo $custRow['first_name']; ?></p>
         <p>Lastname:  <?php echo $custRow['last_name']; ?></p>
        <p>Email:  <?php echo $custRow['email']; ?></p>
                
    </div>

</div>
</body>
</html>
