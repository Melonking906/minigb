<?php require_once(__DIR__ . "/redir_include.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $mgb_name . " v" . $mgb_ver ?></title>

		<meta name="robots" content="noindex, nofollow">
		<meta charset="UTF-8">

		<link rel="stylesheet" href="<?php echo $style ?>">
	</head>

	<body>
	<?php
		if ($powered_by == 1) {
			echo '<a href="'. $mgb_url .'" target="_blank"><img src="img/minigb.gif"></a>';
			echo '<p style="margin-top: -4px;"><i><a href="'. $mgb_url .'" target="_blank">'. $mgb_name .' v'. $mgb_ver .'</a> edited '. $mgb_verdate .'</i></p>';
		}

		if ($user_listing == 1) {
			echo "<h2>User listing</h2>";

			$files = glob($users_dir . "/entries_*.txt");
			$users = array();

			foreach ($files as $file) {
				$username = basename($file, ".txt");
				$username = str_replace("entries_", "", $username);
				$users[] = $username;
			}

			echo "<ul>";
			foreach ($users as $user_each) {
				echo '<li><a href="'. $script_fn .'?usr='. $user_each .'">'. $user_each .'</a></li>';
			}
			echo "</ul>";
		} else {
			echo "The owner of this guestbook system disabled user listing.";
		}
	?>
	</body>
</html>