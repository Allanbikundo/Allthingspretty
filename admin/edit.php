<?php
include("auth.php"); //include auth.php file on all secure pages ?>
<?php

	require_once 'dbconfig.php';

	if(isset($_GET['delete_id']))
	{
		// select image from db to delete
		$stmt_select = $DB_con->prepare('SELECT image FROM products WHERE ID =:uid');
		$stmt_select->execute(array(':uid'=>$_GET['delete_id']));
		$imgRow=$stmt_select->fetch(PDO::FETCH_ASSOC);
		unlink("product_images/".$imgRow['image']);

		// it will delete an actual record from db
		$stmt_delete = $DB_con->prepare('DELETE FROM products WHERE ID =:uid');
		$stmt_delete->bindParam(':uid',$_GET['delete_id']);
		$stmt_delete->execute();

		header("Location: edit.php");
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
    <div class="row">
    <?php

    	$stmt = $DB_con->prepare('SELECT ID, name, price, image , description FROM products ORDER BY ID DESC');
    	$stmt->execute();

    	if($stmt->rowCount() > 0)
    	{
    		while($row=$stmt->fetch(PDO::FETCH_ASSOC))
    		{
    			extract($row);
    			?>
    			<div class="col-xs-3">
    				<p class="page-header"><?php echo $name."&nbsp;/&nbsp;".$price; ?></p>
						<p class="page-header">Product description:</br><?php echo $description; ?></p>
    				<img src="product_images/<?php echo $row['image']; ?>" class="img-rounded" width="250px" height="250px" />
    				<p class="page-header">
    				<span>
    				<a class="btn btn-info" href="editform.php?edit_id=<?php echo $row['ID']; ?>" title="click for edit" onclick="return confirm('sure to edit ?')"><span class="glyphicon glyphicon-edit"></span> Edit</a>
    				<a class="btn btn-danger" href="?delete_id=<?php echo $row['ID']; ?>" title="click for delete" onclick="return confirm('sure to delete ?')"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>
    				</span>
    				</p>
    			</div>
    			<?php
    		}
    	}
    	else
    	{
    		?>
            <div class="col-xs-12">
            	<div class="alert alert-warning">
                	<span class="glyphicon glyphicon-info-sign"></span> &nbsp; No Data Found ...
                </div>
            </div>
            <?php
    	}

    ?>
    </div>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>

</body>
</html>
