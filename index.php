<?php
/*
gecko-minigb (or minigb) v0.03a by GeckoF
based on the Flat-File Guestbook Script code by taufik-nurrohman

(c) 2022 GeckoF/Gecko Fish
(c) 2014 taufik-nurrohman
*/

require_once("cfg.php");

define('IncludeAccess', TRUE);
require_once("include/swatch.php");

date_default_timezone_set("UTC");

// Get version information
$verinfo = parse_ini_file($verinfo_fn, true);
$mgb_ver = $verinfo['version']['name'];
$mgb_verdate = $verinfo['version']['date'];
$mgb_verdate = date('d M Y', strtotime($mgb_verdate));
$mgb_url = $verinfo['version']['url'];
$mgb_name = $verinfo['version']['project'];

// Default variable values
$guest_n = $guest_e = $guest_u = $guest_c = "";

// Load CSS and database file from user if "usr" GET is set
if (isset($_GET['usr']) && !empty($_GET['usr'])) {
	$user = $_GET['usr'];

	$database = $users_dir . '/entries_' . $user . '.txt';
	if (file_exists($database)) {
		$data = file_get_contents($database);
	}
	$style = $users_dir . '/style_' . $user;
	$config = $users_dir . '/conf_' . $user . '.ini';
	if (file_exists($config)) {
		$config_data = parse_ini_file($config, true);
		// Load user config variables from INI file
		$disable_entries = isset($config_data['booleans']['disable_entries']) ? $config_data['booleans']['disable_entries'] : false;
		$swatch = isset($config_data['booleans']['swatch']) ? $config_data['booleans']['swatch'] : false;
	}
} else {
	// If "usr" is not set or empty, use the default style
	$style = $default_css_fn;
	$database = false;
}

// Check if the database file exists
if (!file_exists($database)) {
	$data = false;
} else {
	$old_data = file_get_contents($database);
}

// Kill the script if the database file doesn't have read permissions
if (file_exists($database) && !is_readable($database)) {
	echo $messages['perms_invalid'];
	return false;
	exit;
}

// Reject if database file doesn't exists
if ($data === false) {
	// Return user listing if "usr" is empty
	if (empty($user)) {
		require_once("include/usrlist.php");
		return true;
	} else {
		echo $messages['db_missing'];
		return false;
		exit;
	}
}

function create_or_update_file($file_path, $data) {
	$handle = fopen($file_path, 'w') or die('Cannot open file: ' . $file_path);
	fwrite($handle, $data);
}

// Random values for robot verification
$x = mt_rand(1, 40);
$y = mt_rand(90, 500);
$robot_randid = bin2hex(random_bytes(15));
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $user . "'s guestbook (" . $mgb_name . " v" . $mgb_ver . ")" ?></title>

		<!-- Robots and spiders should not index guestbooks on their search engines. -->
		<meta name="robots" content="noindex, nofollow">
		<meta charset="UTF-8">

		<link rel="stylesheet" href="<?php echo $default_gb_css_fn ?>">
		<?php if (!file_exists($style)): ?>
		<link rel="stylesheet" href="<?php echo $style ?>.css">
		<?php endif; ?>

		<script type="text/javascript">
		function setSmiley(which) {
			document.signForm.guest_c.value += which+" ";
			document.signForm.guest_c.focus();
		}
		</script>
	</head>

	<body>
<?php
	$error = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$guest_n = send_input($_POST["guest_n"]);
		$guest_e = send_input($_POST["guest_e"]);
		$guest_u = send_input($_POST["guest_u"]);
		$guest_c = send_input($_POST["guest_c"]);
		$guest_unix_ts = gmdate('U'); // only used for ID
		$guest_date = gmdate("Y-m-d H:m:s");
		$guest_robot = send_input($_POST["guest_robot"]);

		// Reject post if required values are empty
		if (empty($guest_n) || empty($guest_c)) {
			$error .= "<div class='alertBox-Error'>" . $messages['input_empty'] . "</div>";
		}

		// URL validation
		if (isset($guest_u) && !empty($guest_u)) {
			if (filter_var($guest_u, FILTER_VALIDATE_URL)) {
				$guest_u = strip_tags($guest_u);
			} else {
				$error .= "<div class='alertBox-Error'>" . $messages['url_invalid'] . "</div>";
			}
		} else {
			$guest_u = "";
		}

		// E-Mail validation
		if (isset($guest_e) && !empty($guest_e)) {
			if (filter_var($guest_e, FILTER_VALIDATE_EMAIL)) {
				$guest_e = strip_tags($guest_e);
			} else {
				$error .= "<div class='alertBox-Error'>" . $messages['email_invalid'] . "</div>";
			}
		} else {
			$guest_e = "";
		}

		// Check for character length limit
		if (strlen($guest_n) > $max_length_name) $error .= "<div class='alertBox-Error'>" . $messages['max_length_name'] . "</div>";
		if (strlen($guest_e) > $max_length_email) $error .= "<div class='alertBox-Error'>" . $messages['max_length_email'] . "</div>";
		if (strlen($guest_u) > $max_length_url) $error .= "<div class='alertBox-Error'>" . $messages['max_length_url'] . "</div>";
		if (strlen($guest_c) > $max_length_comment) $error .= "<div class='alertBox-Error'>" . $messages['max_length_comment'] . "</div>";

		// Reject robot/human verification if number is lower than $y
		if ($guest_robot < $y || empty($guest_robot)) {
			$error .= "<div class='alertBox-Error'>" . $messages['captcha_invalid'] . "</div>";
		}

		// Reject post if the database file doesn't have write permissions
		if (!is_writable($database)) {
			$error .= "<div class='alertBox-Error'>" . $messages['perms_invalid'] . "</div>";
		}

		// Reject post if entries are disabled
		if ($disable_entries == 1 || $disable_entries_all == 1) {
			$error .= "<div class='alertBox-Error'>" . $messages['disabled_entries'] . "</div>";
		}

		// Reject post if cookie is set (only if $unique_cookie variable is enabled with "1" on cfg.php)
		if ($unique_cookie == 1) {
			if (isset($_COOKIE["guest_uniq"])) {
				$error .= "<div class='alertBox-Error'>" . $messages['cookie_set'] . "</div>";
			}
		}

		// If all the above is OK, then send
		if ($error === "") {
			header("Location:" . $_SERVER['SCRIPT_NAME'] . "?usr=" . $user . "&posted=1");
        		$new_data = $guest_n. "<||>" . $guest_e . "<||>" . $guest_u . "<||>" . $guest_c . "<||>" . "" . "<||>" . $guest_date . "<||>" . $guest_unix_ts;

			if ($unique_cookie == 1) {
				setcookie("guest_uniq", rand(), time() + 3600);
			}

        		if (!empty($old_data)) {
            			create_or_update_file($database, $new_data . "\n" . $old_data); // Prepend data
        		} else {
            			create_or_update_file($database, $new_data); // Insert data
        		}
		} else {
			echo $error;
		}
	}

	// Display an success message if message was sent correctly
	$get_posted = (isset($_GET['posted']) ? $_GET['posted'] : null);
	if ($get_posted > 0) {
		if ($error === "") {
			echo "<div class='alertBox-Success'>" . $messages['posted_message'] . "</div>";
		}
	}

	// Removing redundant HTML characters if any exist
	function send_input($post_input) {
		$post_input = preg_replace("/\r|\n/", " ", $post_input);

		$post_input = trim($post_input);
		$post_input = stripslashes($post_input);
		$post_input = htmlspecialchars($post_input);
		return $post_input;
	}
?>
		<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); echo "?usr=$user"; ?>" method="post" name="signForm">
			<div class="signF1">
				<input type="name" name="guest_n" style="width: 135px" placeholder="Your name" maxlength="<?php echo $max_length_name ?>" oninput="this.value=this.value.slice(0,this.maxLength)" required> <input type="email" name="guest_e" style="width: 163px;" placeholder="Your e-mail (optional)" maxlength="<?php echo $max_length_email ?>" oninput="this.value=this.value.slice(0,this.maxLength)">
			</div>

			<div class="signF2">
				<input type="url" name="guest_u" style="width: 309.5px" placeholder="Your website/URL (optional)" maxlength="<?php echo $max_length_url ?>" oninput="this.value=this.value.slice(0,this.maxLength)">
			</div>

			<div class="signF3">
				<textarea name="guest_c" id="guest_c" style="width: 310px; height: 136px" placeholder="Type here your comment..." maxlength="<?php echo $max_length_comment ?>" required></textarea>
			</div>

			<div class="signF4">
			<?php
				for ($f = 0; $f < count($smileys); $f++) {
					echo "<a href='javascript:setSmiley(&quot;$smileys[$f]&quot;)'><img src='$smileys_dir/$smileys_img[$f].gif' alt='$smileys[$f]' border='0'></a>\n";
				}
			?>
			</div>

			<div class="signF5">
				Verify that you're a human: <input type="checkbox" name="guest_robot" value="<?php echo $x ?>" id="<?php echo $robot_randid ?>" onchange="sumValue();">
			</div>

			<div class="signF6">
				<button type="submit">Submit!</button> <button type="reset">Reset</button>
			</div>

			<div class="nojs">
				<noscript><p style="color: red;">JAVASCRIPT DISABLED! CAPTCHA SYSTEM WILL NOT WORK.</p></noscript>
			</div>
		</form>

		<hr>
<?php
if(!empty($data)) {
	$data = trim($data, "\n");
	$data = explode("\n", $data);

	for($i = 0; $i < count($data); $i++) {
		$item = explode("<||>", $data[$i]);

		if ($dump_entries == 1) {
			echo var_dump($item);
		}

		echo "<div class='guestComment' id='guest-" . $item[6] . "'>";
		?>

		<p><img src='<?php echo $icons_dir ?>/user.png' class='icons'> <b><?php echo $item[0] ?></b>
		<?php
			if($item[2] === "") {
			} else {
				echo " (<a href='" . $item[2] . "' rel='nofollow' target='_blank'><img src='" . $icons_dir . "/world.png' class='icons'></a>)";
			}

			if($item[1] === "") {
			} else {
				echo " (<a href='mailto:" . $item[1] . "'><img src='" . $icons_dir . "/email.png' class='icons'></a>)";
			}
		?>
		<i class='date'>wrote on <b><?php echo gmdate('F d, Y H:i A', strtotime($item[5])) ?> UTC<?php if ($swatch == 1): echo " @" . GetSwatchTime(true, $item[5]); endif; ?></b></i>:</p>

		<?php
		// Smileys replacement
		for ($f = 0; $f < count($smileys); $f++) {
			// Detects if a smiley is written in uppercase (e.g.: ":P" and ":O") and convert it to lowercase
			if (strtoupper($smileys[$f])) {
				$item[3] = str_replace(strtoupper($smileys[$f]), strtolower($smileys[$f]), $item[3]);
				$item[4] = str_replace(strtoupper($smileys[$f]), strtolower($smileys[$f]), $item[4]);
			}

			$item[3] = str_replace($smileys[$f], "<img src='$smileys_dir/$smileys_img[$f].gif' border='0'>", $item[3]);
			$item[4] = str_replace($smileys[$f], "<img src='$smileys_dir/$smileys_img[$f].gif' border='0'>", $item[4]);
		}

		echo "<p>" . $item[3] . "</p>\n";

		if(!empty($item[4])) {
			echo "<p><i>Owner reply: " . $item[4] . "</i></p>\n";
		}

		echo "</div>\n\n";
		echo "<hr>\n\n";
	}
} else {
	echo "<div class='guestComment'>\n";
	echo "	<p><i>There's not messages yet...</i></p>\n";
	echo "</div>\n";
}

if ($powered_by == 1) {
	echo "<div class='software'>\n";
	echo "	<a href='" . $mgb_url . "' target='_blank'><img src='img/minigb.gif'></a>\n";
	echo "	<p><i>powered by <a href='" . $mgb_url ."' target='_blank'>" . $mgb_name . " v" . $mgb_ver . "</a> edited " . $mgb_verdate . "</i></p>\n";
	echo "</div>\n";
}
?>
		<script type="text/javascript">
		var robot = document.getElementById("<?php echo $robot_randid ?>");
		function sumValue() {
			if (robot.checked == true) {
				robot.value =+ <?php echo $y ?>;
			} else {
				robot.value = <?php echo $x ?>;
			}
		}
		</script>
	</body>
</html>
