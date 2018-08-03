<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
<?php
	require_once 'db.php';
	include 'includes/head.php';
	$results = $con->query("SELECT * FROM brand ORDER BY brand_name");
	$errors = array();

	// Edit brand
	if(isset($_GET['edit']) && !empty($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$edit_id = $edit_id;
		$edit_result = $con->query("SELECT * FROM brand WHERE brand_id = '{$edit_id}'");
		$eBrand = mysqli_fetch_assoc($edit_result);
	}

	// Delete brand
	if(isset($_GET['delete']) && !empty($_GET['delete'])) {
		$delete_id = (int)$_GET['delete'];
		$delete_id = $delete_id;
		$con->query("DELETE FROM brand WHERE brand_id = '{$delete_id}'");
		header("Location: brands.php");
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
		$brand = $_POST['brand'];
		// Check if brand is blank
		if($brand == '') {
			$errors[] .= 'You must enter a brand!';
		}
		// Check if brand exist in database
		$sql = "SELECT * FROM brand WHERE brand_name = '{$brand}'";
		if(isset($_GET['edit'])) {
			$sql = "SELECT * FROM brand WHERE brand_name = '{$brand}' AND brand_id != '{$edit_id}'";
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
			$sql = "INSERT INTO brand (brand_name) VALUES ('{$brand}')";
			if(isset($_GET['edit'])) {
				$sql = "UPDATE brand SET brand_name = '{$brand}' WHERE brand_id = '{$edit_id}'";
			}
			$con->query($sql);
			header('Location: brands.php');
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

<h2 class="text-center">Brands</h2>
<hr>

<div class="text-center">
	<form class="form-inline" action="brands.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id : ''); ?>" method="post">
		<div class="form-group">
			<label for="brand"><?php echo ((isset($_GET['edit']))?'Edit' : 'Add A'); ?> Brand:</label>
			<?php
				$brand_value = '';
				if(isset($_GET['edit'])) {
					$brand_value = $eBrand['brand_name'];
				} else {
					if(isset($_POST['brand_name'])) {
						$brand_value = $_POST['brand_name'];
					}
				}
			?>
			<input class="form-control" type="text" name="brand" id="brand" value="<?php echo $brand_value; ?>">
			<?php if(isset($_GET['edit'])) : ?>
				<a class="btn btn-default" href="brands.php">Cancel</a>
			<?php endif; ?>
			<input class="btn btn-success" type="submit" name="add_submit" value="<?php echo ((isset($_GET['edit']))?'Edit' : 'Add'); ?> Brand">
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
			<td><a class="btn btn-xs btn-default" href="brands.php?edit=<?php echo $brand['brand_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><?php echo $brand['brand_name']; ?></td>
			<td><a class="btn btn-xs btn-default" href="brands.php?delete=<?php echo $brand['brand_id']; ?>"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php
