<?php

session_start();

/*
=============================================================================
| Based in the "Flat-Text File Guestbook" PHP script by "taufik-nurrohman". |
| Readapted for my personal website by Gecko.                               |
=============================================================================
*/

// Default variable values
$guest_n = $guest_e = $guest_u = $guest_c = "";

// Default username
$default_user = "geckof";

// Default entries TXT database file
$database = "entries_$default_user";

// Username GET. If it's not set, it will load the one set by default for this GB.
$user = $_GET['usr'] ?? $default_user;

// If another username is specified in the "?usr=" GET, the database variable will be renamed.
if(isset($_GET['usr'])) {
    $database = "entries_$user";
}

// Max length for posts
$max_length_name = 40;
$max_length_email = 250;
$max_length_url = 250;
$max_length_comment = 520;

// Error messages
$messages = array(
	'database_missing' => 'ERROR: Database file not found for this user.',
	'input_empty' => 'ERROR: Name and Comment cannot be empty!',
	'url_invalid' => 'ERROR: Invalid URL format (use required: http://example.org/).',
	'email_invalid' => 'ERROR: Invalid Email format (use required: example@example.org).',
	'captcha_invalid' => 'ERROR: Invalid Math CAPTCHA.',
	'max_length_name' => 'ERROR: Maximum character length for guest name is ' . $max_length_name,
	'max_length_email' => 'ERROR: Maximum character length for guest Email is ' . $max_length_url,
	'max_length_url' => 'ERROR: Maximum character length for guest URL is ' . $max_length_url,
	'max_length_message' => 'ERROR: Maximum character length for guest comments is ' . $max_length_comment,
);

function create_or_update_file($file_path, $data) {
	$handle = fopen($file_path, 'w') or die('Cannot open file: ' . $file_path);
	fwrite($handle, $data);
}

if(!file_exists($database . '.txt')) {
	// Prevent guest to create new database via GET.
	echo $messages['database_missing'];
	return false;
} else {
	$old_data = file_get_contents($database . '.txt');
}

$data = file_get_contents($database . '.txt');

// Math CAPTCHA
$math_session = htmlspecialchars($_SESSION['math']);

$x = mt_rand(1, 50);
$y = mt_rand(1, 50);

if($x - $y > 0) {
	$math = $x . ' - ' . $y;
	$math_session = $x - $y;
} else {
	$math = $x . ' + ' . $y;
	$math_session = $x + $y;
}

?>

<!DOCTYPE html>
	<html>
		<head>
			<meta name="robots" content="noindex, nofollow">
			<meta charset="UTF-8">

			<link rel="stylesheet" href="style_<?php echo $user ?>.css">

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
		$guest_unix_ts = date('U'); // only used for ID
		$guest_date = date("Y-m-d H:m:s");
		$guest_math = send_input($_POST["guest_math"]);

		// Reject post if required values are empty
		if (empty($guest_n) || empty($guest_c)) {
			$error .= "<div class='alertBox-Error'>" . $messages['input_empty'] . "</div>";			
		}

		// URL Validation
		if (isset($guest_u) && ! empty($guest_u)) {
			if (filter_var($guest_u, FILTER_VALIDATE_URL)) {
				$guest_u = strip_tags($guest_u);
			} else {
				$error .= "<div class='alertBox-Error'>" . $messages['url_invalid'] . "</div>";
			}
		} else {
			$guest_u = "";
		}

		// E-Mail Validation
		if (isset($guest_e) && ! empty($guest_e)) {
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

		// Check the math challenge answer to prevent spam robot.
		if (!isset($guest_math) || empty($guest_math) || $guest_math != $guest_math) {
			$error .= "<div class='alertBox-Error'>" . $messages['captcha_invalid'] . "</div>";
		}

		// If all the above is OK, then send.
		if ($error === "") {
			header("Location:" . $_SERVER['PHP_SELF'] . "?usr=$user");
        		$new_data = $guest_n. "<||>" . $guest_e . "<||>" . $guest_u . "<||>" . $guest_c . "<||>" . "" . "<||>" . $guest_date . "<||>" . $guest_unix_ts;

        		if (!empty($old_data)) {
            			create_or_update_file($database . '.txt', $new_data . "\r" . $old_data); // Prepend data
        		} else {
            			create_or_update_file($database . '.txt', $new_data); // Insert data
        		}
		} else {
			echo $error;
		}
	}
 
	// Removing the redundant HTML characters if any exist.
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
					<a href='javascript:setSmiley(":)")'><img src='img/smil/smile.gif' alt=':)' border='0'></a>
					<a href='javascript:setSmiley(":(")'><img src='img/smil/sad.gif' alt=':<' border='0'></a>
					<a href='javascript:setSmiley(":D")'><img src='img/smil/grin.gif' alt=':D' border='0'></a>
					<a href='javascript:setSmiley(":P")'><img src='img/smil/stick.gif' alt=':P' border='0'></a>
					<a href='javascript:setSmiley(";)")'><img src='img/smil/wink.gif' alt=';)' border='0'></a>
					<a href='javascript:setSmiley("B)")'><img src='img/smil/cool.gif' alt='B)' border='0'></a>	
					<a href='javascript:setSmiley(":o")'><img src='img/smil/gasp.gif' alt=':o' border='0'></a>
					<a href='javascript:setSmiley(":eek:")'><img src='img/smil/eek.gif' alt=':eek:' border='0'></a>
					<a href='javascript:setSmiley(":crazy:")'><img src='img/smil/crazy.gif' alt=':crazy:' border='0'></a>
					<a href='javascript:setSmiley(":love:")'><img src='img/smil/love.gif' alt=':love:' border='0'></a>
				</div>

				<div class="signF5">
					<?php echo $math; ?> = <input type="number" name="guest_math" autocomplete="off" style="width: 55px;">
				</div>

				<div class="signF6">
					<button type="submit">Submit!</button> <button type="reset">Reset</button>
				</div>
			</form>

			<hr>

<?php

if(!empty($data)) {

	$data = explode("\r", $data);

	for($i = 0; $i < count($data); $i++) {
		$item = explode("<||>", $data[$i]);

		//echo var_dump($item);

		echo "        <div class='guestComment' id='guest-" . $item[6] . "'>";

		if(empty($item[1])) {
			echo "          <p><img src='img/icons/user.png' class='icons'> <b>". $item[0] ."</b> (<a href='" . $item[2] . "' rel='nofollow' target='_blank'>" . $item[2] . "</a>) <i class='date'>wroted at <b><time datetime='" . gmdate('c', strtotime($item[5])) . "'>" . gmdate('F d, Y H:i A', strtotime($item[5])) . "</time> UTC</i></b>:</p>\n";
		} else if(empty($item[2])) {
			echo "          <p><img src='img/icons/user.png' class='icons'> <b>". $item[0] ."</b> (<a href='mailto:" . $item[1] . "'><img src='img/icons/email.png' class='icons'></a>) <i class='date'>wroted at <b><time datetime='" . gmdate('c', strtotime($item[5])) . "'>" . gmdate('F d, Y H:i A', strtotime($item[5])) . "</time> UTC</i></b>:</p>\n";
		} else {
			echo "		<p><img src='img/icons/user.png' class='icons'> <b>". $item[0] ."</b> (<a href='mailto:" . $item[1] . "'><img src='img/icons/email.png' class='icons'></a> | <a href='" . $item[2] . "' rel='nofollow' target='_blank'><img src='img/icons/world.png' class='icons'></a>) <i class='date'>wroted at <b><time datetime='" . gmdate('c', strtotime($item[5])) . "'>" . gmdate('F d, Y H:i A', strtotime($item[5])) . "</time> UTC</i></b>:</p>\n";
		}

		// Smileys replacement
		$item[3] = str_replace(":)","<img src='img/smil/smile.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":(","<img src='img/smil/sad.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":o","<img src='img/smil/gasp.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":D","<img src='img/smil/grin.gif' border='0' align='abdsmiddle'>",$item[3]);	
		$item[3] = str_replace(":P","<img src='img/smil/stick.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(";)","<img src='img/smil/wink.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace("B)","<img src='img/smil/cool.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":eek:","<img src='img/smil/eek.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":crazy:","<img src='img/smil/crazy.gif' border='0' align='abdsmiddle'>",$item[3]);
		$item[3] = str_replace(":love:","<img src='img/smil/love.gif' border='0' align='abdsmiddle'>",$item[3]);

		echo "          <p>" . $item[3] . "</p>\n";

		if(!empty($item[4])) {
			echo "          <p><i>Owner reply: " . htmlspecialchars($item[4]) . "</i></p>\n";
		} else {
			// nothing.
		}

		if(empty($item[0])){
			echo "";
		}

		echo "        </div>\n\n";
		echo "        <hr>\n\n";
	}

} else {
	echo "        <div class='guestComment'>\n";
	echo "          <p><i>There's not messages yet...</i></p>\n";
	echo "        </div>\n";
}

?>
		</body>
	</html>
