<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
 <?php
 include("db.php"); //include auth.php file on all secure pages
  ?>
<!DOCTYPE html>
<html >
<?php
include("includes/head.php");
 ?> 

<body>
  <?php
  include("includes/header.php");
   ?>

<!-- Content -->
<div class="main">
  <?php
  $con = mysql_connect("localhost","root","","atp");
  if (!$con) {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("atp", $con);
  //counting number of orders
  $result = mysql_query("select count(1) FROM order_items");
  $row = mysql_fetch_array($result);

  $orders = $row[0];
 // counting number of users
  $uresult = mysql_query("select count(1) FROM users");
  $urow = mysql_fetch_array($uresult);

  $users = $urow[0];


  //counting total number of products
  $presult = mysql_query("select count(1) FROM products");
  $prow = mysql_fetch_array($presult);

  $products = $prow[0];

  mysql_close($con);



  ?>
  <div id="projectFacts" class="sectionClass">
    <div class="fullWidth eight columns">
        <div class="projectFactsWrap ">
            <div class="item wow fadeInUpBig animated animated" data-number="<?php echo  "$users"; ?>" style="visibility: visible;">
                <i class="fa fa-smile-o"></i>
                <p id="number2" class="number"><?php echo  "$users"; ?></p>
                <span></span>
                <p>Number of happy users</p>
            </div>
            <div class="item wow fadeInUpBig animated animated"  style="visibility: visible;">
                <i class="fa fa-coffee"></i>
                <p id="number3" class="number"><?php echo "$products"; ?></p>
                <span></span>
                <p>Number of products</p>
            </div>
            <div class="item wow fadeInUpBig animated animated" data-number="246" style="visibility: visible;">
                <i class="fa fa-camera"></i>
                <p id="number4" class="number"><?php echo "$orders"; ?></p>
                <span></span>
                <p>Total Number of Orders</p>
            </div>
        </div>
    </div>
</div>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>
</body>
</html>
