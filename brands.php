<?php
// include database configuration file
//shopping cart
include 'dbConfig.php';
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

    //Storing user data into session
    $_SESSION['userData'] = $userData;

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
    }
} else {
  $authUrl = $gClient->createAuthUrl();
  $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin.png" alt="" width=400px; height=70px;/></a>';
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="CSS/style.css" rel="stylesheet">
        <link href="bootstrap-social.css" rel="stylesheet">
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <link href="assets/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/docs.css" rel="stylesheet" >
            <title>AllThingsPretty</title>

            <style>
            
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
      <form class="navbar-form navbar-left" id="searchbar">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for Products, Brands and more">
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


<!--sales products section-->
<div class="container" id="sales-products">
    <div class="col-md-12 col-md-ofst-4 text-center" id="products-title">
    <!--Dynamically display Titles-->
    <span style="margin-top: 50px;">Flashy Deals</span>

     <!--Dynamic Heading-->
       <?php if (isset($_GET['id'])) { ?> 
                            <?php 
                             $titleid = $_GET['id'];
                             $title ="SELECT brand_name FROM brand WHERE brand_id = $titleid";
                             $titlequery = $db->query($title);
                            ?>
                             <?php while ($tt = mysqli_fetch_assoc($titlequery)) : ?>
                                  <h1 style="margin-right: 20px; margin-bottom: 0px;">
                                  <?php echo $tt['brand_name'] ;?> : 
                                  </h1>
                                  <?php endwhile; ?>
         <?php  } ?>

<!-- Display Brands-->
        <p style="margin: 10px;">Discover Exclusive Brands at Incredible Prices</p>
        <?php   
        $brands ="SELECT * FROM brand ";
           $brandquery = $db->query($brands); 
        ?>
         <?php while ($b = mysqli_fetch_assoc($brandquery)) : ?>
          <a href="brands.php?action=branddisplay&id=<?php echo $b["brand_id"]; ?>" class="btn btn-default"><?php echo $b['brand_name'] ;?></a>
              <?php endwhile; ?>

    </div>
    <div id="products" class="row list-group">
            <div class="col-md-3">
                <span class="spantitle">Categories</span>
                 <ul>
                     <?php 
                     $sql ="SELECT * FROM category";
                     $pquery = $db->query($sql);
                     ?>
                     <?php while ($parent = mysqli_fetch_assoc($pquery)) : ?>
                     <?php 
                     $type = $parent['category_id'];
                     $sql2 = "SELECT * FROM jewelry_types INNER JOIN category ON category.category_id = jewelry_types.category_id WHERE jewelry_types.category_id = $type";
                     $cquery = $db->query($sql2);
                     ?>
                        <li class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><?php echo $parent['category_name']; ?><span class="caret"></span>
                            </button><br>
                            <ul class="dropdown-menu">
                                <?php while($child = mysqli_fetch_assoc($cquery)) : ?>
                                <li><a href="category.php?action=sortdisplay&id=<?php echo $child["types_id"]; ?>"><?php echo $child['type_name']; ?> </a></li>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                     <?php endwhile; ?>
                </ul>
            </div>
                 <?php if (isset($_GET['id'])) { ?> 
                      <?php 
                             $brandid = $_GET['id'];
                             $brands ="SELECT * FROM products WHERE brand_id= $brandid";
                             $brandquery = $db->query($brands);
                            ?>

                      <?php 
                          if($brandquery->num_rows > 0){
                              while($row = $brandquery->fetch_assoc()){
                      ?>

            <div class="item col-md-3">
                            <div class="caption">
                                <h4 class="list-group-item-heading"><?php echo $row["name"]; ?></h4>
                                <img src="images\<?php echo $row["image"]; ?>" class="img-responsive" height="150" width="150"/><br/>
                                <p class="list-group-item-text"><?php echo $row["description"]; ?></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="lead"><?php echo $row["price"].' KSH'; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-success" href="cartAction.php?action=addToCart&id=<?php echo $row["id"]; ?>">Add to cart</a>
                                    </div>
                                </div>
                            </div>
                    </div>
                <?php } }else{ ?>
                <p>Product(s) not found.....</p>
                <?php } ?>

   <?php  } ?>
    </div>
</div>

    <!--contact-us section-->
<div class="contact">
    <div class="col-md-12 col-md-ofst-4 text-center" id="contact-title">
            <span>Let Us Help You</span>
            <h1>Contact Us</h1>
    </div>
        <form action="contact.php" method="post" id="contact-input">
            <label>
              Your name *
              <input type="text" name="name" autocomplete="off" <?php echo isset($fields['name'] )? 'value="' .e($fields['name']).'"' : ''  ?>>
            </label>
            <label>
              Your email address *
              <input type="email" name="email" autocomplete="off" <?php echo isset($fields['email'] )? 'value="' .e($fields['email']).'"' : ''  ?> required>
            </label>
            <br><br>
            <label id="message">
              Your suggestion *
              <input type="text" name="message" autocomplete="off" <?php echo isset($fields['message'] )? 'value="' .e($fields['message']).'"' : ''  ?> required>
            </label>
           <p class="muted">* means a required field</p>
           <button class="btn btn-default" type="submit" value="send">Send</button>
        </form>
</div>

<!--footer section-->
<div id="footer">
    <div class="row" id="footer-links">
        <div class="col-md-6">
            <span>Get To Know Us</span>
            <ul>
                <li><a href="#">About us</a></li>
                <li><a href="#">Find Store</a></li>
                <li><a href="#">payment</a></li>
                <li><a href="#">Support</a></li>
            </ul>
        </div>
        <div class="col-md-6">
        <span>Let Us Help You</span>
            <ul>
                <li><a href="#">We are Hirng</a></li>
                <li><a href="#">Help</a></li>
                <li><a href="#">Contact us</a></li>
                <li><a href="#">Terms</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="social-icons">
            <ul>
                <li><a  href="#" class="btn btn-social-icon btn-twitter"><span class="fa fa-twitter"></span></a></li>
                <li><a href="#" class="btn btn-social-icon btn-google"><span class="fa fa-google""></span></a></li>
                <li><a  href="#" class="btn btn-social-icon btn-facebook"><span class="fa fa-facebook"></span></a></li>
                <li><a  href="#" class="btn btn-social-icon btn-instagram"><span class="fa fa-instagram"></span></a></li>
                <li><a  href="#" class="btn btn-social-icon btn-linkedin"><span class="fa fa-linkedin"></span></a></li>
                <li><a  href="#" class="btn btn-social-icon btn-pinterest"><span class="fa fa-pinterest"></span></a></li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>