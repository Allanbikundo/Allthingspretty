<?php
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>AllThingsPretty</title>
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
    .container{
        padding: 50px;
        margin-top: 20px;
    }
    #foot .btn-warning{
         background-color:#ffe11b;
         margin-top: 20px;
         border-color: #000;
         color: #000;
    }
    #foot .btn-success {
        background-color: #8132ff;
         margin-top: 20px;
         border-color: #000;
    }
    input[type="number"]{width: 20%;}
    </style>
    <script>
    function updateCartItem(obj,id){
        $.get("cartAction.php", {action:"updateCartItem", id:id, qty:obj.value}, function(data){
            if(data == 'ok'){
                location.reload();
            }else{
                alert('Cart update failed, please try again.');
            }
        });
    }
    </script>
</head>
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

<!--table display-->
<div class="container">
    <h1 style="text-align: center; margin-bottom: 50px;">Shopping Cart</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>&nbsp;</th>
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
            <td><input type="number" class="form-control text-center" value="<?php echo $item["qty"]; ?>" onchange="updateCartItem(this, '<?php echo $item["rowid"]; ?>')"></td>
            <td><?php echo $item["subtotal"].' KSH'; ?></td>
            <td>
                <!--<a href="cartAction.php?action=updateCartItem&id=" class="btn btn-info"><i class="glyphicon glyphicon-refresh"></i></a>-->
                <a href="cartAction.php?action=removeCartItem&id=<?php echo $item["rowid"]; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
        </tr>
        <?php } }else{ ?>
        <tr><td colspan="5"><p>Your cart is empty.....</p></td>
        <?php } ?>
    </tbody>
    <tfoot id="foot">
        <tr>
            <td><a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a></td>
            <td colspan="2"></td>
            <?php if($cart->total_items() > 0){ ?>
            <td class="text-center"><strong>Total <?php echo $cart->total().' KSH'; ?></strong></td>
            <td><a href="checkout.php" class="btn btn-success btn-block">Checkout <i class="glyphicon glyphicon-menu-right"></i></a></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
</div>
</body>
</html>
