<?php
include("auth.php"); //include auth.php file on all secure pages
 ?>
<?php
	require_once 'db.php';
	include 'includes/head.php';

	$errors = array();
	$category = '';
	$post_parent = '';

function display_errors($errors) {
		$display = '<ul class="bg-danger">';
		foreach($errors as $error) {
			$display .= '<li class="text-danger">'.$error.'</li>';
		}
		$display .= '</ul>';
		return $display;
	}
	// Edit
	if(isset($_GET['edit']) && !empty($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$edit_id = $edit_id;
		$edit_result = $con->query("SELECT * FROM jewelry_types WHERE types_id = '{$edit_id}'");
		$edit_category = mysqli_fetch_assoc($edit_result);
	}

	// Delete
	if(isset($_GET['delete']) && !empty($_GET['delete'])) {
		$delete_id = (int)$_GET['delete'];
		$delete_id = $delete_id;

		/* Deleting a parent and its children to avoid orphaned categories in the database. */
		$result = $con->query("SELECT * FROM jewelry_types WHERE types_id  = '{$delete_id}'");
		$category = mysqli_fetch_assoc($result);
		if($category['category_id'] == 0) {
			$con->query("DELETE FROM jewelry_types WHERE category_id = '{$delete_id}'");
			header("Location: jewelry_types.php");
		}

		/* Deleting a child only. */
		$con->query("DELETE FROM jewelry_types WHERE types_id = '{$delete_id}'");
		header("Location: jewelry_types.php");
	}

	// Add/Edit
	if(isset($_POST) && !empty($_POST)) {
		$post_parent = $_POST['category_id'];
		$category = $_POST['type_name'];
		$sqlform = "SELECT * FROM jewelry_types WHERE type_name = '{$category}' AND category_id = '{$post_parent}'";
		if(isset($_GET['edit'])) {
			$id = $edit_category['types_id'];
			$sqlform = "SELECT * FROM jewelry_types WHERE type_name = '{$category}' AND category_id = '{$post_parent}' AND types_id != '{$id}'";
		}

		$fresult = $con->query($sqlform);
		$count = mysqli_num_rows($fresult);

		/* Check if category input is blank. */
		if($category == '') {
			$errors[] .= 'The jewelry_types cannot be left blank.';
		}

		/* Check if category inputted has already exist in database. */
		if($count > 0) {
			$errors[] .= $category.' already exists. Please choose a new category.';
		}

		/* Display errors or add/update database. */
		if(!empty($errors)) {
			/* Display errors. */
			$display = display_errors($errors);
?>
		<script>
			jQuery('document').ready(function() {
				jQuery('#errors').html('<?php echo $display; ?>');
			});
		</script>
<?php
		} else {
			/* Add/update database. */
			$updatesql = "INSERT INTO jewelry_types (type_name, category_id) VALUES ('{$category}', '{$post_parent}')";
			if(isset($_GET['edit'])) {
				$updatesql = "UPDATE jewelry_types SET type_name = '{$category}', category_id = '{$post_parent}' WHERE types_id = '{$edit_id}'";
			}
			$con->query($updatesql);
			header("Location: jewelry_types.php");
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
  <div class="header">
  <a href="#" id="menu-action">
    <i class="fa fa-bars"></i>
    <span>Close</span>
  </a>
  <div class="logo">
    All Things Pretty Admin Panel
  </div>
</div>
<?php
include("includes/header.php");
 ?>
<div class="row">

	<!-- Form -->
	<div class="col-md-6">
		<form class="form" action="jewelry_types.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id : ''); ?>" method="post">
			<legend><?php echo ((isset($_GET['edit']))?'Edit' : 'Add A'); ?> Jewelry type</legend>
			<div id="errors"></div>

			<?php
				$category_value = '';
				$parent_value = 0;
				if(isset($_GET['edit'])) {
					$category_value = $edit_category['type_name'];
					$parent_value = $edit_category['category_id'];
				} else {
					/* If post is set during edit. */
					if(isset($_POST)) {
						$category_value = $category; /* $category is from line 37 */
						$parent_value = $post_parent; /* $post_parent is from line 36 */
					}
				}
			?>

			<div class="form-group">
				<label for="parent">Category</label>
				<select class="form-control" name="parent" id="parent">
					<option value="0"<?php echo (($parent_value == 0)?' selected="selected"' : ''); ?>>Jewelry type</option>
					<?php $result = $con->query("SELECT category_name,types_id,type_name FROM `jewelry_types` INNER JOIN category ON category.category_id = jewelry_types.category_id ORDER BY category_name"); ?>
					<?php while($parent = mysqli_fetch_assoc($result)) : ?>
					<option value="<?php echo $parent['types_id']; ?>"<?php echo (($parent_value == $parent['types_id'])?' selected="selected"' : ''); ?>><?php echo $parent['category_name']; ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group">
				<label for="category">Jewelry type</label>
				<input class="form-control" type="text" name="category" id="category" value="<?php echo $category_value; ?>">
			</div>
			<div class="form-group">
				<input class="btn btn-success" type="submit" value="<?php echo ((isset($_GET['edit']))?'Edit' : 'Add'); ?> Category">
			</div>
		</form>
	</div>

	<!-- Category Table -->
	<div class="col-md-6">
		<table class="table table-bordered table-condensed">
			<thead>
				<th>Category</th>
				<th>Parent</th>
				<th></th>
			</thead>
			<tbody>
				<?php $result = $con->query("SELECT category_name,types_id,type_name FROM `jewelry_types` INNER JOIN category ON category.category_id = jewelry_types.category_id ORDER BY category_name"); ?>
				<?php while($parent = mysqli_fetch_assoc($result)) : ?>
				<?php
					$parent_id = (int)$parent['types_id'];
					$cresult = $con->query("SELECT * FROM jewelry_types WHERE category_id = '{$parent_id}'");
				?>
				<tr class="bg-primary">
					<td><?php echo $parent['type_name']; ?></td>
					<td>Parent</td>
					<td>
						<a class="btn btn-xs btn-default" href="jewelry_types.php?edit=<?php echo $parent['types_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
						<a class="btn btn-xs btn-default" href="jewelry_types.php?delete=<?php echo $parent['types_id']; ?>"><span class="glyphicon glyphicon-remove-sign"></span></a>
					</td>
				</tr>
					<?php while($child = mysqli_fetch_assoc($cresult)) : ?>
						<tr class="bg-info">
							<td><?php echo $child['type_name']; ?></td>
							<td><?php echo $parent['type_name']; ?></td>
							<td>
								<a class="btn btn-xs btn-default" href="jewelry_types.php?edit=<?php echo $child['type_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
								<a class="btn btn-xs btn-default" href="jewelry_types.php?delete=<?php echo $child['type_id']; ?>"><span class="glyphicon glyphicon-remove-sign"></span></a>
							</td>
						</tr>
					<?php endwhile; ?>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>
</div>

<?php
