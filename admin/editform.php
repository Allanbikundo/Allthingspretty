<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
 <?php

 	error_reporting( ~E_NOTICE );

 	require_once 'dbconfig.php';

 	if(isset($_GET['edit_id']) && !empty($_GET['edit_id']))
 	{
 		$id = $_GET['edit_id'];
 		$stmt_edit = $DB_con->prepare('SELECT name,price,description,image FROM products WHERE ID =:uid');
 		$stmt_edit->execute(array(':uid'=>$id));
 		$edit_row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
 		extract($edit_row);
 	}
 	else
 	{
 		header("Location: edit.php");
 	}



 	if(isset($_POST['btn_save_updates']))
 	{
 		$name = $_POST['user_name'];// user name
 		$userjob = $_POST['user_job'];// user email
    $description = $_POST['pro_desc'];

 		$imgFile = $_FILES['user_image']['name'];
 		$tmp_dir = $_FILES['user_image']['tmp_name'];
 		$imgSize = $_FILES['user_image']['size'];

 		if($imgFile)
 		{
 			$upload_dir = 'product_images/'; // upload directory
 			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
 			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
 			$image = rand(1000,1000000).".".$imgExt;
 			if(in_array($imgExt, $valid_extensions))
 			{
 				if($imgSize < 5000000)
 				{
 					unlink($upload_dir.$edit_row['image']);
 					move_uploaded_file($tmp_dir,$upload_dir.$image);
 				}
 				else
 				{
 					$errMSG = "Sorry, your file is too large it should be less then 5MB";
 				}
 			}
 			else
 			{
 				$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
 			}
 		}
 		else
 		{
 			// if no image selected the old image remain as it is.
 			$image = $edit_row['image']; // old image from database
 		}


 		// if no error occured, continue ....
 		if(!isset($errMSG))
 		{
 			$stmt = $DB_con->prepare('UPDATE products
 									     SET name=:uname,
 										     price=:ujob,
                         description=:udesc,
 										     image=:upic
 								       WHERE ID=:uid');
 			$stmt->bindParam(':uname',$name);
 			$stmt->bindParam(':ujob',$userjob);
      $stmt->bindParam(':udesc',$description);
 			$stmt->bindParam(':upic',$image);
 			$stmt->bindParam(':uid',$id);

 			if($stmt->execute()){
 				?>
                 <script>
 				alert('Successfully Updated ...');
 				window.location.href='edit.php';
 				</script>
                 <?php
 			}
 			else{
 				$errMSG = "Sorry Data Could Not Updated !";
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
  <div class="page-header">
    	<h1 class="h2">update profile. <a class="btn btn-default" href="edit.php"> all members </a></h1>
    </div>
    <div class="clearfix"></div>

    <form method="post" enctype="multipart/form-data" class="form-horizontal">


        <?php
    	if(isset($errMSG)){
    		?>
            <div class="alert alert-danger">
              <span class="glyphicon glyphicon-info-sign"></span> &nbsp; <?php echo $errMSG; ?>
            </div>
            <?php
    	}
    	?>


    	<table class="table table-bordered table-responsive">

        <tr>
        	<td><label class="control-label">name.</label></td>
            <td><input class="form-control" type="text" name="user_name" value="<?php echo $name; ?>" required /></td>
        </tr>

        <tr>
        	<td><label class="control-label">Profession(Job).</label></td>
            <td><input class="form-control" type="text" name="user_job" value="<?php echo $price; ?>" required /></td>
        </tr>
        <tr>
          <td><label class="control-label">Product description</label></td>
            <td><input class="form-control" type="text" name="pro_desc" value="<?php echo $description; ?>"placeholder="Enter brief product description"/></td>
        </tr>

        <tr>
        	<td><label class="control-label">Profile Img.</label></td>
            <td>
            	<p><img src="product_images/<?php echo $image; ?>" height="150" width="150" /></p>
            	<input class="input-group" type="file" name="user_image" accept="image/*" />
            </td>
        </tr>

        <tr>
            <td colspan="2"><button type="submit" name="btn_save_updates" class="btn btn-default">
            <span class="glyphicon glyphicon-save"></span> Update
            </button>

            <a class="btn btn-default" href="edit.php"> <span class="glyphicon glyphicon-backward"></span> cancel </a>

            </td>
        </tr>

        </table>

    </form>


</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>

    <script src="js/index.js"></script>

</body>
</html>
