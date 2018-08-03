<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
<?php
	require_once 'db.php';
	include 'includes/head.php';
	$results = $con->query("SELECT * FROM category ORDER BY category_name");
	$errors = array();

	// Edit brand
	if(isset($_GET['edit']) && !empty($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$edit_id = $edit_id;
		$edit_result = $con->query("SELECT * FROM category WHERE category_id = '{$edit_id}'");
		$eBrand = mysqli_fetch_assoc($edit_result);
	}

	// Delete brand
	if(isset($_GET['delete']) && !empty($_GET['delete'])) {
		$delete_id = (int)$_GET['delete'];
		$delete_id = $delete_id;
		$con->query("DELETE FROM category WHERE category_id = '{$delete_id}'");
		header("Location: categories.php");
	}

	function display_errors($errors) {
		$display = '<ul class="bg-danger">';
		foreach($errors as $error) {
			$display .= '<li class="text-danger">'.$error.'</li>';
		}
		$display .= '</ul>';
		return $display;
	}

	if(isset($_POST['add_submit'])) {
		$brand = $_POST['category'];
		// Check if brand is blank
		if($brand == '') {
			$errors[] .= 'You must enter a brand!';
		}
		// Check if brand exist in database
		$sql = "SELECT * FROM category WHERE category_name = '{$brand}'";
		if(isset($_GET['edit'])) {
			$sql = "SELECT * FROM category WHERE category_name = '{$brand}' AND category_id != '{$edit_id}'";
		}
		$result = $con->query($sql);
		$count = mysqli_num_rows($result);
		if($count > 0) {
			$errors[] .= $brand.' already exist. Please choose another brand name.';
		}
		// Display errors
		if(!empty($errors)) {
			echo display_errors($errors);
		} else {
			// Add brand to database
			$sql = "INSERT INTO category (category_name) VALUES ('{$brand}')";
			if(isset($_GET['edit'])) {
				$sql = "UPDATE category SET category_name = '{$brand}' WHERE category_id = '{$edit_id}'";
			}
			$con->query($sql);
			header('Location: categories.php');
		}
	}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Admin Menu</title>

  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp'>
  <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css'>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>

      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/stats.css">
</head>

<body>
  <?php
  include("includes/header.php");
   ?>

<!-- Content -->
<div class="main">

<h2 class="text-center">Categories</h2>
<hr>

<div class="text-center">
	<form class="form-inline" action="categories.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id : ''); ?>" method="post">
		<div class="form-group">
			<label for="category"><?php echo ((isset($_GET['edit']))?'Edit' : 'Add A'); ?> Category:</label>
			<?php
				$brand_value = '';
				if(isset($_GET['edit'])) {
					$brand_value = $eBrand['category_name'];
				} else {
					if(isset($_POST['category_name'])) {
						$brand_value = $_POST['category_name'];
					}
				}
			?>
			<input class="form-control" type="text" name="category" id="category" value="<?php echo $brand_value; ?>">
			<?php if(isset($_GET['edit'])) : ?>
				<a class="btn btn-default" href="categories.php">Cancel</a>
			<?php endif; ?>
			<input class="btn btn-success" type="submit" name="add_submit" value="<?php echo ((isset($_GET['edit']))?'Edit' : 'Add'); ?> category">
		</div>
	</form>
</div>
<hr>

<table class="table table-bordered table-striped table-auto table-condensed">
	<thead>
		<th></th>
		<th>Brand</th>
		<th></th>
	</thead>
	<tbody>
		<?php while($brand = mysqli_fetch_assoc($results)) : ?>
		<tr>
			<td><a class="btn btn-xs btn-default" href="categories.php?edit=<?php echo $brand['category_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><?php echo $brand['category_name']; ?></td>
			<td><a class="btn btn-xs btn-default" href="categories.php?delete=<?php echo $brand['category_id']; ?>"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php
