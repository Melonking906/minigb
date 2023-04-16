<?php

/*
gecko-minigb (or minigb) v0.02a by GeckoF
based on the Flat-File Guestbook Script code by taufik-nurrohman

(c) 2022 GeckoF/Gecko Fish
(c) 2014 taufik-nurrohman
*/

require_once("cfg.php");

// Version date conversion
$mgb_verdate = date('d M Y', strtotime($mgb_verdate));

// Default variable values
$guest_n = $guest_e = $guest_u = $guest_c = "";

// Username GET. If it's not set, it will load the one set by default for this GB.
$user = $_GET['usr'] ?? $default_user;

// If another username is specified in the "?usr=" GET, the database and CSS style variable will be renamed.
if(isset($_GET['usr'])) {
	$database = "entries_$user";
	$style = "style_$user";
}

function create_or_update_file($file_path, $data) {
	$handle = fopen($file_path, 'w') or die('Cannot open file: ' . $file_path);
	fwrite($handle, $data);
}

if(!file_exists($database . '.txt')) {
	echo $messages['database_missing'];
	return false;
	exit;
} else {
	$old_data = file_get_contents($database . '.txt');
}

// Kill the script if the database file doesn't have read permissions.
if (!is_readable($database . '.txt')) {
	echo $messages['perms_invalid'];
	return false;
	exit;
}

$data = file_get_contents($database . '.txt');

// Random values for robot verification
$x = mt_rand(1, 30);
$y = mt_rand(50, 100);

?>

<!DOCTYPE html>
	<html>
		<head>
			<title><?php echo $user ?>'s guestbook (gecko-minigb v<?php echo $mgb_ver ?>)</title>

			<meta name="robots" content="noindex, nofollow">
			<meta charset="UTF-8">

			<link rel="stylesheet" href="<?php echo $style ?>.css">

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

		// Reject post if required values are empty.
		if (empty($guest_n) || empty($guest_c)) {
			$error .= "<div class='alertBox-Error'>" . $messages['input_empty'] . "</div>";			
		}

		// URL validation.
		if (isset($guest_u) && ! empty($guest_u)) {
			if (filter_var($guest_u, FILTER_VALIDATE_URL)) {
				$guest_u = strip_tags($guest_u);
			} else {
				$error .= "<div class='alertBox-Error'>" . $messages['url_invalid'] . "</div>";
			}
		} else {
			$guest_u = "";
		}

		// E-Mail validation.
		if (isset($guest_e) && ! empty($guest_e)) {
			if (filter_var($guest_e, FILTER_VALIDATE_EMAIL)) {
				$guest_e = strip_tags($guest_e);
			} else {
				$error .= "<div class='alertBox-Error'>" . $messages['email_invalid'] . "</div>";
			}
		} else {
			$guest_e = "";
		}

		// Check for character length limit.
		if (strlen($guest_n) > $max_length_name) $error .= "<div class='alertBox-Error'>" . $messages['max_length_name'] . "</div>";
		if (strlen($guest_e) > $max_length_email) $error .= "<div class='alertBox-Error'>" . $messages['max_length_email'] . "</div>";
		if (strlen($guest_u) > $max_length_url) $error .= "<div class='alertBox-Error'>" . $messages['max_length_url'] . "</div>";
		if (strlen($guest_c) > $max_length_comment) $error .= "<div class='alertBox-Error'>" . $messages['max_length_comment'] . "</div>";

		// Reject robot/human verification if number is lower than $y.
		if ($guest_robot < $y || empty($guest_robot)) {
			$error .= "<div class='alertBox-Error'>" . $messages['captcha_invalid'] . "</div>";
		}

		// Reject post if entries are disabled (from cfg.php).
		if ($disable_entries > 0) {
			$error .= "<div class='alertBox-Error'>" . $messages['disabled_entries'] . "</div>";
		}

		// Reject post if the database file doesn't have write permissions.
		if (!is_writable($database . '.txt')) {
			$error .= "<div class='alertBox-Error'>" . $messages['perms_invalid'] . "</div>";
		}

		// Reject post if cookie is set (only if $unique_cookie variable is enabled with "1" on cfg.php).
		if ($unique_cookie > 0) {
			if (isset($_COOKIE["guest_uniq"])) {
				$error .= "<div class='alertBox-Error'>" . $messages['cookie_set'] . "</div>";
			}
		}

		// If all the above is OK, then send.
		if ($error === "") {
			header("Location:" . $_SERVER['PHP_SELF'] . "?usr=$user&posted=1");
        		$new_data = $guest_n. "<||>" . $guest_e . "<||>" . $guest_u . "<||>" . $guest_c . "<||>" . "" . "<||>" . $guest_date . "<||>" . $guest_unix_ts;

			if ($unique_cookie > 0) {
				setcookie("guest_uniq", rand(), time() + 3600);
			}

        		if (!empty($old_data)) {
            			create_or_update_file($database . '.txt', $new_data . "\n" . $old_data); // Prepend data
        		} else {
            			create_or_update_file($database . '.txt', $new_data); // Insert data
        		}
		} else {
			echo $error;
		}
	}
	
	// Display an success message if message was sent correctly.
	$get_posted = (isset($_GET['posted']) ? $_GET['posted'] : null);
	
	if ($get_posted > 0) {
		if ($error === "") {
			echo "<div class='alertBox-Success'>" . $messages['posted_message'] . "</div>";
		}
	}
 
	// Removing redundant HTML characters if any exist.
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
					<input type="name" name="guest_n" style="width: 135px" placeholder="Your name" maxlength="<?php echo $max_length_name ?>" oninput="this.value=this.value.slice(0,this.maxLength)"> <input type="email" name="guest_e" style="width: 163px;" placeholder="Your e-mail (optional)" maxlength="<?php echo $max_length_email ?>" oninput="this.value=this.value.slice(0,this.maxLength)">
				</div>

				<div class="signF2">
					<input type="url" name="guest_u" style="width: 309.5px;" placeholder="Your website/URL (optional)" maxlength="<?php echo $max_length_url ?>" oninput="this.value=this.value.slice(0,this.maxLength)">
				</div>

				<div class="signF3">
					<textarea name="guest_c" id="guest_c" style="resize: none; width: 310px; height: 136px;" placeholder="Type here your comment..." maxlength="<?php echo $max_length_comment ?>"></textarea>
				</div>

				<div class="signF4">
				<?php
				for ($f = 0; $f < count($smileys); $f++) {
					echo "<a href='javascript:setSmiley(&quot;$smileys[$f]&quot;)'><img src='$smileys_dir/$smileys_img[$f].gif' alt='$smileys[$f]' border='0'></a>\n";
				}
				?>
				</div>
				
				<div class="signF5">
					Verify that you're a human: <input type="checkbox" name="guest_robot" value="<?php echo $x ?>" id="robot" onchange="sumValue();">
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

		// Uncomment for debug array values.
		//echo var_dump($item);

		echo "<div class='guestComment' id='guest-" . $item[6] . "'>";

		?>

		<p><img src='img/icons/user.png' class='icons'> <b><?php echo $item[0] ?></b>
		
		<?php
			if($item[2] === "") {
			} else {
				echo " (<a href='" . $item[2] . "' rel='nofollow' target='_blank'><img src='img/icons/world.png' class='icons'></a>)";
			}

			if($item[1] === "") {
			} else {
				echo " (<a href='mailto:" . $item[1] . "'><img src='img/icons/email.png' class='icons'></a>)";
			}
		?>

		<i class='date'>wrote on <b><?php echo gmdate('F d, Y H:i A', strtotime($item[5])) ?> UTC</i></b>:</p>

		<?php

		// Smileys replacement
		for ($f = 0; $f < count($smileys); $f++) {
			// Detects if a smiley is written in uppercase (e.g.: ":P" and ":O") and convert it to lowercase
			if (strtoupper($smileys[$f])) {
				$item[3] = str_replace(strtoupper($smileys[$f]), strtolower($smileys[$f]), $item[3]);
				$item[4] = str_replace(strtoupper($smileys[$f]), strtolower($smileys[$f]), $item[4]);
			}

			$item[3] = str_replace($smileys[$f], "<img src='$smileys_dir/$smileys_img[$f].gif' border='0' align='abdsmiddle'>", $item[3]);
			$item[4] = str_replace($smileys[$f], "<img src='$smileys_dir/$smileys_img[$f].gif' border='0' align='abdsmiddle'>", $item[4]);
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

if ($powered_by > 0) {
	echo "<div class='software'>\n";
	echo "	<a href='$mgb_url' target='_blank'><img src='img/minigb.gif'></a>\n";
	echo "	<p><i>powered by <a href='$mgb_url' target='_blank'>gecko-minigb v$mgb_ver</a> edited $mgb_verdate</i></p>\n";
	echo "</div>\n";
}

}
									   
?>
		</body>
		
		<script type="text/javascript">
		function sumValue(){
			if (robot.checked == true) {
				robot.value =+ <?php echo $y ?>;
			} else {
				robot.value = <?php echo $x ?>;
			}		
		}
		</script>
	</html>
