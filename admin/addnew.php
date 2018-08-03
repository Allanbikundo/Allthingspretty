<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
 <?php

 	error_reporting( ~E_NOTICE ); // avoid notice

 	require_once 'dbconfig.php';

 	if(isset($_POST['btnsave']))
 	{
 		$name = $_POST['user_name'];// item name
 		$userjob = $_POST['user_job'];// user email
    $description = $_POST['pro_desc']; // item description
    $buy_p = $_POST['buy_p'];// item name
    $featS = $_POST['featS'];// item name
    $Cat = '2';// item name
    $Bran ='3';// item name
    $Jew = '4';// item name

 		$imgFile = $_FILES['user_image']['name'];
 		$tmp_dir = $_FILES['user_image']['tmp_name'];
 		$imgSize = $_FILES['user_image']['size'];


 		if(empty($name)){
 			$errMSG = "Please Enter name.";
 		}
 		else if(empty($userjob)){
 			$errMSG = "Please Enter Your Job Work.";
 		}
 		else if(empty($imgFile)){
 			$errMSG = "Please Select Image File.";
 		}
 		else
 		{
 			$upload_dir = 'product_images/'; // upload directory

 			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension

 			// valid image extensions
 			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions

 			// rename uploading image
 			$image = rand(1000,1000000).".".$imgExt;

 			// allow valid image file formats
 			if(in_array($imgExt, $valid_extensions)){
 				// Check file size '5MB'
 				if($imgSize < 5000000)				{
 					move_uploaded_file($tmp_dir,$upload_dir.$image);
 				}
 				else{
 					$errMSG = "Sorry, your file is too large.";
 				}
 			}
 			else{
 				$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
 			}
 		}


 		// if no error occured, continue ....
 		if(!isset($errMSG))
 		{
 			$stmt = $DB_con->prepare('INSERT INTO products(name,price,description,image,buying_price,featured_status,category_id,brand_id,type_id) VALUES(:uname, :ujob, :udesc, :upic, :buy, :fet, :cat, :bran, :type)');
 			$stmt->bindParam(':uname',$name);
 			$stmt->bindParam(':ujob',$userjob);
 			$stmt->bindParam(':upic',$image);
      $stmt->bindParam(':udesc',$description);
      $stmt->bindParam(':buy', $buy_p);
      $stmt->bindParam(':fet',$featS);
      $stmt->bindParam(':cat',$Cat);
      $stmt->bindParam(':bran',$Bran);
      $stmt->bindParam(':type',$Jew);

 			if($stmt->execute())
 			{
 				$successMSG = "new record succesfully inserted ...";
 				header("refresh:5;index.php"); // redirects image view page after 5 seconds.
 			}
 			else
 			{
 				$errMSG = "error while inserting....";
 			}
 		}
 	}
 ?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Admin Menu</title>


  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>

      <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
  include("includes/header.php");
   ?>

<!-- Content -->
<div class="main">
    <?php
  	if(isset($errMSG)){
  			?>
              <div class="alert alert-danger">
              	<span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
              </div>
              <?php
  	}
  	else if(isset($successMSG)){
  		?>
          <div class="alert alert-success">
                <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG; ?></strong>
          </div>
          <?php
  	}
  	?>

  <form method="post" enctype="multipart/form-data" class="form-horizontal">

  	<table class="table table-bordered table-responsive">

      <tr>
      	<td><label class="control-label">Name</label></td>
          <td><input class="form-control" type="text" name="user_name" placeholder="Enter product name" value="<?php echo $name; ?>" /></td>
      </tr>

      <tr>
      	<td><label class="control-label">Price</label></td>
          <td><input class="form-control" type="text" name="user_job" placeholder="Enter product price" value="<?php echo $userjob; ?>" /></td>
      </tr>

      <tr>
        <td><label class="control-label">Product description</label></td>
          <td><input class="form-control" type="text" name="pro_desc" placeholder="Enter brief product description" accept="image/*" /></td>
      </tr>
      <tr>
        <td><label class="control-label">Buying Price</label></td>
          <td><input class="form-control" type="text" name="buy_p" placeholder="Enter Buying Price" accept="image/*" /></td>
      </tr>
      <tr>
        <td><label class="control-label">Featured status</label></td>
          <td><input class="form-control" type="text" name="featS" placeholder="Enter Featured status" accept="image/*" /></td>
      </tr>
      <tr>
      	<td><label class="control-label">Product Image</label></td>
          <td><input class="input-group" type="file" name="user_image" accept="image/*" /></td>
      </tr>


      <tr>
          <td colspan="2"><button type="submit" name="btnsave" class="btn btn-default">
          <span class="glyphicon glyphicon-save"></span> &nbsp; save
          </button>
          </td>
      </tr>

      </table>

  </form>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>

</body>
</html>
